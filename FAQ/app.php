<?php

namespace Apps\FAQ;

use Sellfino\DB;
use Sellfino\Helpers;
use Sellfino\Shopify;

Class App
{

  public function install() {

    $id = DB::get('blog_id', 'FAQ.json');

    if (!$id) {

      $blog = [
        'blog' => [
          'title' => 'SellfinoFAQ'
        ]
      ];

      $res = Shopify::request('blogs', $blog, 'POST');
      $res = json_decode($res, true);
      $id = $res['blog']['id'];

      DB::put('blog_id', $id, 'FAQ.json');

    }

  }

  public function uninstall() { }

  public function router($route)
  {

    switch ($route) {

      case 'faqs':
      case 'questions':
      case 'answers':
        $this->get($route);
        break;

      case 'faq':
        $this->faq();
        break;

      case 'update':
        $this->update();
        break;

      case 'manageQA':
        $this->manageQA();
        break;

      case 'delete':
        $this->delete();
        break;

      case 'ask':
        $this->ask();
        break;

      case 'answer':
        $this->answer();
        break;

    }

  }

  public function get($handle)
  {

    $items = DB::get('faqs', 'FAQ.json');
    $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
    $search = isset($_REQUEST['search']) ? $_REQUEST['search'] : '';

    if ($handle == 'questions') {
      $questions = [];

      foreach ($items as $item) {
        foreach ($item['questions'] as $question) {
          $i_question = $question;
          $i_question['faq'] = [
            'id' => $item['id'],
            'name' => $item['name']
          ];
          $questions[] = $i_question;
        }
      }

      $items = $questions;
    }

    if ($handle == 'answers') {
      $answers = [];

      foreach ($items as $item) {
        foreach ($item['questions'] as $question) {
          foreach ($question['answers'] as $answer) {
            $i_answer = $answer;
            $i_answer['question'] = isset($question['question']) ? $question['question'] : '';
            $i_answer['question_id'] = isset($question['id']) ? $question['id'] : '';
            $i_answer['faq'] = [
              'id' => $item['id'],
              'name' => $item['name']
            ];
            $answers[] = $i_answer;
          }
        }
      }

      $items = $answers;
    }

    if ($search != '') {
      $items = array_filter($items, function($item) use($handle, $search) {
        if ($handle == 'faqs') {
          return strpos(strtolower($item['name']), strtolower($search)) !== false;
        }
        if ($handle == 'questions') {
          return strpos(strtolower($item['question']), strtolower($search)) !== false;
        }
        if ($handle == 'answers') {
          return strpos(strtolower($item['answer']), strtolower($search)) !== false;
        }
      });
    }

    usort($items, function($a, $b) {
      return strtotime($b["created_at"]) - strtotime($a["created_at"]);
    });

    $cut = array_slice($items, $page * 50 - 50, $page * 50);

    if ($handle == 'faqs') {
      foreach ($cut as $key => $row) {
        
        if (isset($row['questions'])) {
          $cut[$key]['q'] = count($row['questions']);
          $a = 0;

          foreach ($row['questions'] as $q) {
            $a += count($q['answers']);
          }

          $cut[$key]['a'] = $a;
        }

        unset($cut[$key]['questions']);

      }
    }

    Helpers::json($cut);

  }

  public function faq()
  {

    $items = DB::get('faqs', 'FAQ.json');
    $found = array_search($_REQUEST['id'], array_column($items, 'id'));

    if (!is_bool($found)) {

      Helpers::json($items[$found]);

    } else {

      Helpers::error(404);

    }

  }

  public function update()
  {

    $data = json_decode(file_get_contents('php://input'), true);
    $items = DB::get('faqs', 'FAQ.json');

    foreach ($items as $item) {
      if ($item['name'] == $data['name']) {
        if (isset($data['id'])) {
          if ($item['id'] != $data['id']) {
            Helpers::error(400, 'This name is already taken');
          }
        } else {
          Helpers::error(400, 'This name is already taken');
        }
      }
    }

    if (isset($data['id'])) {

      $found = array_search($data['id'], array_column($items, 'id'));

      if (!is_bool($found)) {

        $items[$found] = $data;
        DB::put('faqs', $items, 'FAQ.json');

        $blog_id = DB::get('blog_id', 'FAQ.json');
        $meta = [
          'metafield' => [
            'id' => $data['metafield_id'],
            'value' => json_encode($data)
          ]
        ];
        Shopify::request('blogs/' . $blog_id . '/articles/' . $data['article_id'] . '/metafields/' . $data['metafield_id'], $meta, 'PUT');

      }

    } else {

      $blog_id = DB::get('blog_id', 'FAQ.json');
      $id = 'faq-' . Helpers::slugify($data['name']);

      $article = [
        'article' => [
          'title' => $id
        ]
      ];

      $res = Shopify::request('blogs/' . $blog_id . '/articles', $article, 'POST');
      $res = json_decode($res, true);
      $article_id = $res['article']['id'];

      $data['id'] = $id;
      $data['article_id'] = $article_id;

      $meta = [
        'metafield' => [
          'namespace' => 'sellfino',
          'key' => 'faq',
          'value' => json_encode($data),
          'value_type' => 'json_string'
        ]
      ];

      $res = Shopify::request('blogs/' . $blog_id . '/articles/' . $article_id . '/metafields', $meta, 'POST');
      $res = json_decode($res, true);
      $data['metafield_id'] = $res['metafield']['id'];

      $items[] = $data;
      DB::put('faqs', $items, 'FAQ.json');

      Helpers::json([
        'id' => $data['id'],
        'article_id' => $data['article_id'],
        'metafield_id' => $data['metafield_id'],
      ]);

    }

    Helpers::success();

  }

  public function manageQA()
  {

    $data = json_decode(file_get_contents('php://input'), true);
    $items = DB::get('faqs', 'FAQ.json');
    $found = array_search($data['faq']['id'], array_column($items, 'id'));

    if (!is_bool($found)) {

      if ($data['type'] == 'q') {

        $index = array_search($data['id'], array_column($items[$found]['questions'], 'id'));

        if ($data['remove']) {
          unset($items[$found]['questions'][$index]);
        } else {
          $items[$found]['questions'][$index]['active'] = !$items[$found]['questions'][$index]['active'];
        }

      }

      if ($data['type'] == 'a') {

        $index = array_search($data['question_id'], array_column($items[$found]['questions'], 'id'));
        $answer_key = array_search($data['id'], array_column($items[$found]['questions'][$index]['answers'], 'id'));

        if ($data['remove']) {
          unset($items[$found]['questions'][$index]['answers'][$answer_key]);
        } else {
          $items[$found]['questions'][$index]['answers'][$answer_key]['active'] = !$items[$found]['questions'][$index]['answers'][$answer_key]['active'];
        }

      }

      DB::put('faqs', $items, 'FAQ.json');

      $blog_id = DB::get('blog_id', 'FAQ.json');
      $meta = [
        'metafield' => [
          'id' => $items[$found]['metafield_id'],
          'value' => json_encode($data)
        ]
      ];
      Shopify::request('blogs/' . $blog_id . '/articles/' . $items[$found]['article_id'] . '/metafields/' . $items[$found]['metafield_id'], $meta, 'PUT');

      Helpers::success();

    } else {

      Helpers::error(404);

    }

  }

  public function delete()
  {

    $data = json_decode(file_get_contents('php://input'), true);
    $items = DB::get('faqs', 'FAQ.json');
    $found = array_search($data['id'], array_column($items, 'id'));

    if (!is_bool($found)) {
      
      $blog_id = DB::get('blog_id', 'FAQ.json');
      Shopify::request('blogs/' . $blog_id . '/articles/' . $items[$found]['article_id'], [], 'DELETE');

      unset($items[$found]);
      DB::put('faqs', $items, 'FAQ.json');

      Helpers::success();
      
    } else {

      Helpers::error(404);

    }

  }

  public function ask()
  {

    if (isset($_REQUEST['id']) && isset($_REQUEST['question']) && isset($_REQUEST['author']['name']) && isset($_REQUEST['author']['email'])) {

      if (!filter_var($_REQUEST['author']['email'], FILTER_VALIDATE_EMAIL)) {
        Helpers::error(400);
      }

      $items = DB::get('faqs', 'FAQ.json');
      $found = array_search($_REQUEST['id'], array_column($items, 'id'));

      if (!is_bool($found)) {

        $items[$found]['questions'][] = [
          'id' => uniqid(),
          'question' => $_REQUEST['question'],
          'active' => !$items[$found]['moderate']['questions'],
          'answers' => [],
          'author' => $_REQUEST['author'],
          'created_at' => date('Y-m-d\TH:i:s')
        ];

        DB::put('faqs', $items, 'FAQ.json');

        $blog_id = DB::get('blog_id', 'FAQ.json');
        $meta = [
          'metafield' => [
            'id' => $items[$found]['metafield_id'],
            'value' => json_encode($items[$found])
          ]
        ];
        Shopify::request('blogs/' . $blog_id . '/articles/' . $items[$found]['article_id'] . '/metafields/' . $items[$found]['metafield_id'], $meta, 'PUT');

      } else {

        $res = Shopify::request('products/' . $_REQUEST['product_id']);
        $res = json_decode($res, true);
        $product = $res['product'];
        $blog_id = DB::get('blog_id', 'FAQ.json');
        $id = 'faq-' . Helpers::slugify($product['title']);

        $article = [
          'article' => [
            'title' => $id
          ]
        ];

        $res = Shopify::request('blogs/' . $blog_id . '/articles', $article, 'POST');
        $res = json_decode($res, true);
        $article_id = $res['article']['id'];

        $data = [
          'id' => $id,
          'name' => $product['title'],
          'created_at' => date('Y-m-d\TH:i:s'),
          'moderate' => [
            'questions' => 1,
            'answers' => 1
          ],
          'questions' => [
            [
              'id' => uniqid(),
              'question' => $_REQUEST['question'],
              'active' => 0,
              'answers' => [],
              'author' => $_REQUEST['author'],
              'created_at' => date('Y-m-d\TH:i:s')
            ]
          ],
          'article_id' => $article_id
        ];

        $meta = [
          'metafield' => [
            'namespace' => 'sellfino',
            'key' => 'faq',
            'value' => json_encode($data),
            'value_type' => 'json_string'
          ]
        ];

        $res = Shopify::request('blogs/' . $blog_id . '/articles/' . $article_id . '/metafields', $meta, 'POST');
        $res = json_decode($res, true);
        $data['metafield_id'] = $res['metafield']['id'];

        $items[] = $data;
        DB::put('faqs', $items, 'FAQ.json');

        $prometa = [
          'product' => [
            'id' => $_REQUEST['product_id'],
            'tags' => (strlen($product['tags']) > 0 ? ', ' : '') . $id,
          ]
        ];

        Shopify::request('products/' . $product['id'], $prometa, 'PUT');

      }


      Helpers::success();

    } else {

      Helpers::error(400);

    }

  }

  public function answer()
  {

    if (isset($_REQUEST['id']) && isset($_REQUEST['answer']) && isset($_REQUEST['author']['name']) && isset($_REQUEST['author']['email'])) {

      if (!filter_var($_REQUEST['author']['email'], FILTER_VALIDATE_EMAIL)) {
        Helpers::error(400);
      }

      $items = DB::get('faqs', 'FAQ.json');
      $found = array_search($_REQUEST['id'], array_column($items, 'id'));

      if (!is_bool($found)) {

        $question_key = array_search($_REQUEST['question_id'], array_column($items[$found]['questions'], 'id'));

        if (!is_bool($question_key)) {

          $items[$found]['questions'][$question_key]['answers'][] = [
            'id' => uniqid(),
            'answer' => $_REQUEST['answer'],
            'author' => $_REQUEST['author'],
            'created_at' => date('Y-m-d\TH:i:s'),
            'active' => !$items[$found]['moderate']['answers']
          ];

          DB::put('faqs', $items, 'FAQ.json');

          $blog_id = DB::get('blog_id', 'FAQ.json');
          $meta = [
            'metafield' => [
              'id' => $items[$found]['metafield_id'],
              'value' => json_encode($items[$found])
            ]
          ];
          Shopify::request('blogs/' . $blog_id . '/articles/' . $items[$found]['article_id'] . '/metafields/' . $items[$found]['metafield_id'], $meta, 'PUT');

        }

        Helpers::success();

      } else {

        Helpers::error(404);

      }


    } else {

      Helpers::error(400);

    }

  }
  
}