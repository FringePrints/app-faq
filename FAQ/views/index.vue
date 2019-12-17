<template>
  <div class="main-content">
    <div class="header bg-gradient-primary pb-6">
      <div class="container-fluid">
        <div class="header-body">
          <div class="row align-items-center py-4">
            <div class="col-lg-6 col-7">
              <h6 class="h2 text-white d-inline-block mb-0">Sellfino App Store</h6>
              <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                <ol class="breadcrumb breadcrumb-links breadcrumb-dark m-0 p-0 text-sm font-weight-600 nobg">
                  <li class="breadcrumb-item"><a href="" class="text-neutral" @click.prevent="$root.view = 'apps'"><i class="fas fa-home"></i></a></li>
                  <li class="breadcrumb-item active text-light">FAQ</li>
                </ol>
              </nav>
            </div>
            <div class="col-lg-6 col-5 text-right">
              <div class="loading text-sm font-weight-600 text-neutral" v-if="loading">Loading... <span class="badge ml-2"></span></div>
              <a href="#" class="btn btn-sm btn-neutral" :class="{ disabled: loading }">Settings</a>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <button type="button" class="btn mb-1" v-for="tab in tabs" :class="{ 'btn-secondary' : tab.handle != tabActive, 'btn-info pointer-disabled' : tab.handle == tabActive }" @click="load(tab.handle)">{{ tab.name }}</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="container-fluid mt--5" v-if="tabActive">
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              <div class="row align-items-center">
                <div class="col-6">
                  <h3 class="mb-0" v-if="tabActive == 'faqs'">List of available FAQs <a href="#" class="btn btn-sm btn-primary ml-4 position-absolute" @click.prevent="select('new')" :class="{ disabled: loading }">Add FAQ</a></h3>
                  <h3 class="mb-0" v-if="tabActive == 'questions'">List of questions added lately</a></h3>
                  <h3 class="mb-0" v-if="tabActive == 'answers'">List of answers added lately</a></h3>
                </div>
                <div class="col-6">
                  <form class="float-right" @submit.prevent="searchRun">
                    <div class="row align-items-center m-0">
                      <div class="form-group m-0">
                        <input type="text" class="form-control text-xs h-auto py-1" placeholder="Enter search phrase" v-model="search">
                      </div>
                      <button class="btn btn-primary ml-2 btn-sm" type="submit" :class="{ disabled: loading || saving }">Find</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <div class="table-responsive" v-if="tabActive == 'faqs'">
              <table class="table align-items-center table-flush" v-if="list.length">
                <thead class="thead-light">
                  <tr>
                    <th class="w-30">FAQ Name</th>
                    <th class="w-20">ID</th>
                    <th class="w-20">Total Q / A</th>
                    <th class="w-30">Created at</th>
                    <th class="w-10"></th>
                  </tr>
                </thead>
                <tbody class="list">
                  <tr v-for="(el, index) in list">
                    <th scope="row">
                      <div class="media align-items-center">
                        <div class="media-body">
                          <span class="name mb-0 text-sm">
                            <a href="" class="text-primary" @click.prevent="select(el)">{{ el.name }}</a>
                          </span>
                        </div>
                      </div>
                    </th>
                    <td>{{ el.id }}</td>
                    <td>{{ el.q }} / {{ el.a }}</td>
                    <td>{{ date_format(el.created_at) }}</td>
                    <td class="text-right">
                      <a class="btn btn-icon-only text-danger" @click.prevent="remove(el)" :class="{ disabled: loading }">
                        <i class="fas fa-trash-alt"></i>
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
                      <span v-else>There are no elements here</span>
                    </th>
                  </tr>
                </thead>
              </table>
            </div>

            <div class="table-responsive" v-if="tabActive == 'questions'">
              <table class="table align-items-center table-flush" v-if="list.length">
                <thead class="thead-light">
                  <tr>
                    <th class="w-30">Question</th>
                    <th class="w-20">FAQ</th>
                    <th class="w-20">Author's name / email</th>
                    <th class="w-20">Created at</th>
                    <th class="w-10"></th>
                  </tr>
                </thead>
                <tbody class="list">
                  <tr v-for="(el, index) in list">
                    <td>{{ el.question }}</td>
                    <th scope="row">
                      <div class="media align-items-center">
                        <div class="media-body">
                          <span class="name mb-0 text-sm">
                            <a href="" class="text-primary" @click.prevent="select(el.faq.id)">{{ el.faq.name }}</a>
                          </span>
                        </div>
                      </div>
                    </th>
                    <td>{{ el.author.name }} / {{ el.author.email }}</td>
                    <td>{{ date_format(el.created_at) }}</td>
                    <td class="text-right">
                      <a class="btn btn-icon-only" :class="{ 'text-muted' : !el.active, 'text-success' : el.active, disabled: loading || saving }" @click.prevent="manageQA(el)">
                        <i class="fas" :class="{ 'fa-toggle-off' : !el.active, 'fa-toggle-on' : el.active }"></i>
                      </a>
                      <a class="btn btn-icon-only text-danger" :class="{ disabled: loading || saving }" @click.prevent="manageQA(el, true)">
                        <i class="fas fa-trash-alt"></i>
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
                      <span v-else>There are no elements here</span>
                    </th>
                  </tr>
                </thead>
              </table>
            </div>

            <div class="table-responsive" v-if="tabActive == 'answers'">
              <table class="table align-items-center table-flush" v-if="list.length">
                <thead class="thead-light">
                  <tr>
                    <th class="w-20">Answer</th>
                    <th class="w-20">Question</th>
                    <th class="w-20">FAQ</th>
                    <th class="w-20">Author's name / email</th>
                    <th class="w-10">Created at</th>
                    <th class="w-10"></th>
                  </tr>
                </thead>
                <tbody class="list">
                  <tr v-for="(el, index) in list">
                    <td>{{ el.answer }}</td>
                    <td>{{ el.question }}</td>
                    <th scope="row">
                      <div class="media align-items-center">
                        <div class="media-body">
                          <span class="name mb-0 text-sm">
                            <a href="" class="text-primary" @click.prevent="select(el.faq.id)">{{ el.faq.name }}</a>
                          </span>
                        </div>
                      </div>
                    </th>
                    <td>{{ el.author.name }} / {{ el.author.email }}</td>
                    <td>{{ date_format(el.created_at) }}</td>
                    <td class="text-right">
                      <a class="btn btn-icon-only" :class="{ 'text-muted' : !el.active, 'text-success' : el.active, disabled: loading || saving }" @click.prevent="manageQA(el)">
                        <i class="fas" :class="{ 'fa-toggle-off' : !el.active, 'fa-toggle-on' : el.active }"></i>
                      </a>
                      <a class="btn btn-icon-only text-danger" :class="{ disabled: loading || saving }" @click.prevent="manageQA(el, true)">
                        <i class="fas fa-trash-alt"></i>
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
                      <span v-else>There are no elements here</span>
                    </th>
                  </tr>
                </thead>
              </table>
            </div>

          </div>
          <div class="card-footer py-4 nobg">
            <nav>
              <ul class="pagination justify-content-center mb-0">
                <li class="page-item" :class="{ disabled: page == 1 || loading }">
                  <a class="page-link" href="#" @click.prevent="prevPage">
                    <i class="fas fa-angle-left"></i>
                    <span class="sr-only">Previous</span>
                  </a>
                </li>
                <li class="page-item" :class="{ disabled: list.length < 50 || loading }">
                  <a class="page-link" href="#" @click.prevent="nextPage">
                    <i class="fas fa-angle-right"></i>
                    <span class="sr-only">Next</span>
                  </a>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
module.exports = {
  data: function() {
    return {
      loading: false,
      saving: false,
      tabActive: 'faqs',
      tabs: [
        { handle: 'faqs', name: 'FAQs' },
        { handle: 'questions', name: 'Latest Questions' },
        { handle: 'answers', name: 'Latest Answers' }
      ],
      list: [],
      page: 1,
      search: ''
   }
  },
  methods: {
    load: function(handle) {
      var self = this

      this.loading = true

      if (this.tabActive != handle) {
        this.page = 1
        this.search = ''
        this.tabActive = handle
        this.list = []
      }

      url = '/app/FAQ/' + handle + '?page=' + this.page + '&search=' + this.search
      params = {
        method: 'GET',
        headers: this.$root.fetchHeaders
      }

      fetch(url, params)
      .then(errorCheck)
      .then(function(res) {
        self.list = res
        self.loading = false
      })
      .catch((error) => {
        alert(error)
        self.loading = false
      })

    },
    select: function(item) {
      this.$root.viewPrevData = {
        page: this.page,
        search: this.search,
        tabActive: this.tabActive
      }

      if (item == 'new') {
        item = {
          name: '',
          created_at: current_date(),
          moderate: { questions: 1, answers: 1 },
          questions: []
        }
      }

      if (typeof item !== 'object' && item !== null) {
        item = {
          id: item
        }
      }

      this.$root.viewData = item
      this.$root.view = 'FAQ-edit'
    },
    remove: function(item) {
      var self = this

      r = confirm('Are you sure?')
      if (r == true) {
        this.loading = true

        url = '/app/FAQ/delete'
        params = {
          method: 'POST',
          headers: this.$root.fetchHeaders,
          body: JSON.stringify({ id: item.id })
        }

        fetch(url, params)
        .then(errorCheck)
        .then(function(res) {
          self.loading = false
          if (self.list.length == 1 && self.page > 1) {
            self.page--
          }
          self.load('faqs')
        })
        .catch((error) => {
          alert(error)
          self.loading = false
        })
      }
    },
    manageQA: function(item, remove = false) {
      var self = this

      if (remove) {
        r = confirm('Are you sure?')
        if (r != true) {
          return
        }
      } else {
        item.active = !item.active
      }

      this.saving = true

      data = { 
        faq: item.faq, 
        id: item.id,
        remove: remove
      }

      if (item.answers) {
        data.type = 'q'
      } else {
        data.type = 'a'
      }

      url = '/app/FAQ/manageQA'
      params = {
        method: 'POST',
        headers: this.$root.fetchHeaders,
        body: JSON.stringify(data)
      }

      fetch(url, params)
      .then(errorCheck)
      .then(function(res) {
        self.saving = false
        if (remove) {
          if (self.list.length == 1 && self.page > 1) {
            self.page--
          }
          self.load(self.tabActive)
        }
      })
      .catch((error) => {
        alert(error)
        self.saving = false
      })
    },
    searchRun: function() {
      this.page = 1
      this.load(this.tabActive)
    },
    nextPage: function() {
      this.page++
      this.load(this.tabActive)
    },
    prevPage: function() {
      this.page--
      this.load(this.tabActive)
    }
  },
  mounted: function() {
    if (this.$root.viewPrevData) {
      this.tabActive = this.$root.viewPrevData.tabActive
      this.page = this.$root.viewPrevData.page
      this.search = this.$root.viewPrevData.search
      this.$root.viewPrevData = null
    }

    this.load(this.tabActive)
  }
}
</script>