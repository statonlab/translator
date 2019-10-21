<template>
    <form @submit.prevent="save()" @keydown="form.keydown($event)" autocomplete="off">
        <div class="card mb-4">
            <div class="card-body pb-2">
                <div class="d-flex align-items-start">
                    <div class="flex-grow-1 mr-1">
                        <input type="text"
                               name="value"
                               :class="['form-control', 'shadow-none', {'is-valid': saved, 'is-invalid': form.errors.has('value')}]"
                               placeholder="Translate the line here"
                               v-model="form.value"/>
                        <small class="form-text text-danger"
                               v-if="form.errors.has('value')">
                            {{ form.errors.first('value') }}
                        </small>
                    </div>
                    <div class="d-flex justify-content-end align-items-end">
                        <button type="submit"
                                :class="['btn', 'btn-primary', {'disabled': loading}]"
                                :disable="loading">Save
                        </button>
                    </div>
                </div>
                <div class="small text-muted mt-2" v-if="line.user || saved_at">
                    {{ saved_at ? 'Last saved at ' + saved_at.format('Do MMM YYYY HH:mm:ss') + '. ' : ''}}
                    {{ line.user ? 'Modified by: ' + line.user.name : '' }}
                </div>
            </div>
            <div class="card-footer">
                {{ line.serialized_line.value }}
            </div>
        </div>
    </form>
</template>

<script>
  import Form from '../forms/Form'
  import moment from 'moment'

  export default {
    name: 'TranslatedLine',

    props: {line: {required: true, type: Object}},

    watch: {
      line(line) {
        this.form.value = line.value
        this.saved_at   = moment(line.updated_at)
      }
    },

    data() {
      return {
        saved_at: moment(this.line.updated_at),
        form    : new Form({
          value: this.line.value
        }),
        loading : false,
        saved: false
      }
    },

    methods: {
      async save() {
        this.loading = true

        try {
          const {data} = await this.form.put('/web/translation/line/' + this.line.id)
          this.$emit('save', data)
          this.saved_at = moment()
          this.saved = true
        } catch (e) {
          console.error(e)
        }

        this.loading = false
      }
    }
  }
</script>

<style scoped>

</style>
