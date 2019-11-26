<template>
  <div class="main-content">
    <div class="header bg-gradient-primary" :class="{ 'pb-6' : !loading }">
      <div class="container-fluid">
        <div class="header-body">
          <div class="row align-items-center py-4">
            <div class="col-lg-6 col-7">
              <h6 class="h2 text-white d-inline-block mb-0">Sellfino App Store</h6>
              <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                <ol class="breadcrumb breadcrumb-links breadcrumb-dark m-0 p-0 text-sm font-weight-600 nobg">
                  <li class="breadcrumb-item"><a href="" class="text-neutral" @click.prevent="$root.view = 'apps'"><i class="fas fa-home"></i></a></li>
                  <li class="breadcrumb-item"><a href="" class="text-neutral" @click.prevent="$root.view = 'FAQ-index'">FAQ</a></li>
                  <li class="breadcrumb-item active text-light">Edit</li>
                </ol>
              </nav>
            </div>
            <div class="col-lg-6 col-5 text-right">
              <div class="loading text-sm font-weight-600 text-neutral" v-if="loading || saving">Loading... <span class="badge ml-2"></span></div>
              <a href="" class="btn btn-sm btn-neutral" v-if="!loading" :class="{ disabled: saving }" @click.prevent="save">Save</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="container-fluid mt--6" v-if="!loading">
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              <h3 class="mb-0">General <p class="m-0"><small>Set basic settings</small></p></h3>
            </div>
            <div class="card-body pt-0">
              <div class="row">
                <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                    <label for="item-name" class="form-control-label">Name</label>
                    <input type="text" id="item-name" class="form-control" v-model="item.name">
                  </div>
                </div>
                <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                    <label for="item-moderate-questions" class="form-control-label">Moderate questions</label>
                    <select id="item-moderate-questions" class="form-control" v-model="item.moderate.questions">
                      <option value="1">Yes</option>
                      <option value="0">No</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                    <label for="item-moderate-answers" class="form-control-label">Moderate answers</label>
                    <select id="item-moderate-answers" class="form-control" v-model="item.moderate.answers">
                      <option value="1">Yes</option>
                      <option value="0">No</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="container-fluid mt-5 mb-5" v-if="!loading">
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              <h3 class="mb-0">Questions and Answers <a href="#" class="btn btn-sm btn-primary ml-4 position-absolute" :class="{ disabled: loading }" @click.prevent="add">Add Question</a> <p class="m-0"><small>Manage content of the FAQ</small></p></h3>
            </div>
            <div class="table-responsive">
              <table class="table align-items-center table-flush" v-if="item.questions.length">
                <thead class="thead-light">
                  <tr>
                    <th class="w-30">Question</th>
                    <th class="w-20">Author's name</th>
                    <th class="w-20">Author's email</th>
                    <th class="w-20">Created at</th>
                    <th class="w-10"></th>
                  </tr>
                </thead>
                <tbody class="list" is="draggable" element="tbody" v-model="item.questions" draggable=".question" handle=".drag">
                  <tr v-for="(question,index) in item.questions" class="question" :key="index">
                    <td>
                      <input type="text" class="form-control" v-model="question.question">
                    </td>
                    <td>
                      <input type="text" class="form-control" v-model="question.author.name">
                    </td>
                    <td>
                      <input type="text" class="form-control" v-model="question.author.email">
                    </td>
                    <td>
                      <input type="text" class="form-control" v-model="question.created_at">
                    </td>
                    <td class="text-right">
                      <a class="btn btn-icon-only" :class="{ 'text-muted' : !question.active, 'text-success' : question.active }" @click.prevent="toggle(question)">
                        <i class="fas" :class="{ 'fa-toggle-off' : !question.active, 'fa-toggle-on' : question.active }"></i>
                      </a>
                      <a class="btn btn-icon-only text-primary" @click.prevent="selectedQ = question">
                        <i class="fas fa-comments"></i>
                      </a>
                      <a class="btn btn-icon-only text-danger" @click.prevent="remove(question)" :class="{ disabled: loading }">
                        <i class="fas fa-trash-alt"></i>
                      </a>
                      <a class="btn btn-icon-only text-light drag" :class="{ disabled: loading }" style="cursor:move">
                        <i class="fas fa-arrows-alt-v"></i>
                      </a>
                    </td>
                  </tr>
                </tbody>
              </table>
              <table class="table align-items-center table-flush" v-else>
                <thead class="thead-light">
                  <tr>
                    <th>
                      <span v-if="loading">Loading... Please wait</span>
                      <span v-else>There are no questions here</span>
                    </th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <component is="inc-FAQ-modal" :question="selectedQ" />

  </div>
</template>

<script>
module.exports = {
  data: function() {
    return {
      loading: true,
      saving: false,
      item: this.$root.viewData,
      selectedQ: null
    }
  },
  methods: {
    save: function() {
      var self = this, item = JSON.parse(JSON.stringify(this.item))

      if (item.name == '') {
        self.$root.showToast('Name is required', true)
        return
      }

      this.saving = true

      if (item.apply == 'product') {
        item.variants = []
      }

      url = '/app/FAQ/update'
      params = {
        method: 'POST',
        headers: this.$root.fetchHeaders,
        body: JSON.stringify(item)
      }

      fetch(url, params)
      .then(errorCheck)
      .then(function(res) {
        self.saving = false
        self.$root.showToast('FAQ saved')
        
        if (res.id) {
          self.item.id = res.id
          self.item.article_id = res.article_id
          self.item.metafield_id = res.metafield_id
        }
      })
      .catch((error) => {
        alert(error)
        self.saving = false
      })
    },
    add: function() {
      this.item.questions.push({
        id: uniqid(),
        question: '',
        active: true,
        answers: [],
        author: { name: '', email: '' },
        created_at: current_date()
      })
    },
    toggle: function(question) {
      question.active = !question.active
    },
    remove: function(question) {
      index = this.item.questions.indexOf(question)
      this.item.questions.splice(index, 1)
    }
  },
  mounted: function() {
    var self = this

    if (this.item.id) {

      url = '/app/FAQ/faq?id=' + this.item.id
      params = {
        method: 'POST',
        headers: this.$root.fetchHeaders
      }

      fetch(url, params)
      .then(errorCheck)
      .then(function(res) {
        self.loading = false
        self.item = res
      })
      .catch((error) => {
        alert(error)
        self.loading = false
      })

    } else {
      this.loading = false
    }
  }
}
</script>

<style>
  .answer {
    min-height: 82px;
  }
</style>