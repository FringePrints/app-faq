<p align="center"><a href="https://www.sellfino.com" target="_blank" rel="noopener noreferrer"><img width="200" src="https://www.sellfino.com/images/logo.png" alt="Sellfino logo"></a></p>

---

## FAQ
Let your customers ask questions and manage the answers.

## Demo & Screenshots
Check how this app works in the live store: [DEMO](https://sellfino.myshopify.com/products/grey-tie-faq)

<a href="https://sellfino.com/images/screens/faq/faq-1.jpg" target="_blank" rel="noopener noreferrer"><img width="24%" src="https://sellfino.com/images/screens/faq/faq-1.jpg"></a> <a href="https://sellfino.com/images/screens/faq/faq-2.jpg" target="_blank" rel="noopener noreferrer"><img width="24%" src="https://sellfino.com/images/screens/faq/faq-2.jpg"></a> <a href="https://sellfino.com/images/screens/faq/faq-3.jpg" target="_blank" rel="noopener noreferrer"><img width="24%" src="https://sellfino.com/images/screens/faq/faq-3.jpg"></a> <a href="https://sellfino.com/images/screens/faq/faq-4.jpg" target="_blank" rel="noopener noreferrer"><img width="24%" src="https://sellfino.com/images/screens/faq/faq-4.jpg"></a> <a href="https://sellfino.com/images/screens/faq/faq-5.jpg" target="_blank" rel="noopener noreferrer"><img width="24%" src="https://sellfino.com/images/screens/faq/faq-5.jpg"></a> <a href="https://sellfino.com/images/screens/faq/faq-6.jpg" target="_blank" rel="noopener noreferrer"><img width="24%" src="https://sellfino.com/images/screens/faq/faq-6.jpg"></a> <a href="https://sellfino.com/images/screens/faq/faq-7.jpg" target="_blank" rel="noopener noreferrer"><img width="24%" src="https://sellfino.com/images/screens/faq/faq-7.jpg"></a>

## Installation
- **1.** Add this app to Sellfino App Store.
- **2.** Add file from the repository `sellfino-faq.liquid` into the Sections folder in your theme. This is sample code you can modify to match your design. Stylesheet and javascript code you can move to corresponding theme files if you need.
- **3.** Open `product.liquid` template file and include this new section wherever you want: `{% section 'sellfino-faq' %}`
- **4.** *Customize* your theme, open the product page and select if you want to show FAQ even if there are no questions. If you want to allow your customers to send questions, check this option. If you have fixed-added questions, leave it unchecked.
- **5.** Add new FAQ. You can do this manually, inside the app settings or, if you checked *Show FAQ form always* in section customization, by asking the question.
- **6.** When you create the FAQ, new article is being added, where data from FAQ are saved as metafields. Thanks to that, you can access any FAQ from the whole website by reffering to this article like that: `articles[sellfinofaq/faq-test-name]` where `faq-test-name` is the ID of the FAQ. Sellfino FAQ blog is added automatically when you activate the app
- **7.** If you use our sample code `sellfino-faq.liquid` - to connect FAQ with the product, add tag to that product with the FAQ's ID, i.e. `faq-test-name`. 

## Email notifications
Coming soon.

## Sellfino Open Source Shopify App Store
This is the app for [Sellfino](https://github.com/sellfino/sellfino) platform.

#### Support and Contribution

Join our awesome community! Here is how you can connect with us:
- [Website](https://www.sellfino.com) - all info here + live chat
- [Discord](https://discordapp.com/invite/wrFnzZ3) - channels to discuss new ideas and ask for help
- [Messanger](https://m.me/104484064333760) - if you want to chat on Facebook
- [Email](mailto:contact@sellfino.com) - whenever we are out of touch, drop us a message


## Copyright
Copyright (c) 2019-present, Lucas Szarzynski
