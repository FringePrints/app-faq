<template>
  <div v-if="question">
    <div class="modal fade" style="display:block" :class="{ show: question }">
      <div class="modal-dialog modal- modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-body p-0">
            <div class="card bg-secondary border-0 mb-0">
              <button type="button" class="close" @click="$parent.selectedQ = null">
                <span>×</span>
              </button>
              <div class="card-header bg-transparent pb-1">
                <div class="mt-0 mb-0 h3">Question:</div>
                <div class="mb-3 h5">{{ question.question }}</div>
              </div>
              <div class="card-body">
                <draggable v-model="question.answers" draggable=".row" handle=".drag">
                  <div class="row" v-for="(answer, index) in question.answers" :key="index">
                    <div class="col-9">
                      <div class="form-group">
                        <label class="text-muted"><small>Answer</small></label>
                        <textarea class="form-control text-xs h-auto" v-model="answer.answer"></textarea>
                      </div>
                    </div>
                    <div class="col-3 text-right">
                      <label class="btn-block">&nbsp;</label>
                      <a class="btn btn-icon-only" :class="{ 'text-muted' : !answer.active, 'text-success' : answer.active }" @click.prevent="toggle(answer)">
                        <i class="fas" :class="{ 'fa-toggle-off' : !answer.active, 'fa-toggle-on' : answer.active }"></i>
                      </a>
                      <a class="btn btn-icon-only text-danger" @click.prevent="remove(answer)">
                        <i class="fas fa-trash-alt"></i>
                      </a>
                      <a class="btn btn-icon-only text-light drag" style="cursor:move">
                        <i class="fas fa-arrows-alt-v"></i>
                      </a>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="text-muted"><small>Author's name</small></label>
                        <input type="text" class="form-control text-xs h-auto" v-model="answer.author.name">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="text-muted"><small>Author's email</small></label>
                        <input type="text" class="form-control text-xs h-auto" v-model="answer.author.email">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="text-muted"><small>Created at</small></label>
                        <input type="text" class="form-control text-xs h-auto" v-model="answer.created_at">
                      </div>
                    </div>
                  </div>
                  <div class="row py-lg-2" v-if="!question.answers.length">
                    <div class="col-12">
                      <div class="alert alert-primary bg-gradient-primary" role="alert">
                        <span class="alert-inner--icon mr-2"><i class="fa fa-info-circle"></i></span>
                        <span class="alert-inner--text font-italic">No answers found</span>
                      </div>
                    </div>
                  </div>
                </draggable>
              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-end">
            <div>
              <button type="button" class="btn btn-primary" @click.prevent="add">Add answer</button>
              <button type="button" class="btn btn-secondary" @click="$parent.selectedQ = null">Close</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-backdrop fade" :class="{ show: question }"></div>
  </div>
</template>

<script>
module.exports = {
  props: ['question'],
  methods: {
    toggle: function(answer) {
      answer.active = !answer.active
    },
    add: function() {
      this.question.answers.push({
        id: uniqid(),
        answer: '',
        author: { name: '', email: '' },
        active: true,
        created_at: current_date()
      })
    },
    remove: function(answer) {
      index = this.question.answers.indexOf(answer)
      this.question.answers.splice(index, 1)
    }
  }
}
</script>