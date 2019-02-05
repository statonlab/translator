<template>
    <form @submit.prevent="submit()">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Language</h5>
                <button type="button" class="close" @click="$emit('close')" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label" for="language">Language Name<sup class="text-danger">*</sup></label>
                    <input type="text"
                           :class="['form-control', {'is-invalid': form.errors.has('language')}]"
                           name="language"
                           id="language"
                           v-model="form.language"
                           placeholder="Ex: English or Spanish">
                    <small class="text-danger form-text" v-if="form.errors.has('language')">
                        {{ form.errors.first('language')}}
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label" for="language_code">Language Code<sup class="text-danger">*</sup></label>
                    <input type="text"
                           :class="['form-control', {'is-invalid': form.errors.has('language_code')}]"
                           name="language_code"
                           id="language_code"
                           v-model="form.language_code"
                           placeholder="Ex: en-US or es-MX">
                    <small class="form-text text-muted">
                        Language code in small letters followed by a dash then the country code in capital letters.
                    </small>
                    <small class="text-danger form-text" v-if="form.errors.has('language_code')">
                        {{ form.errors.first('language_code')}}
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label" for="image">Image Path</label>
                    <input type="text"
                           :class="['form-control', {'is-invalid': form.errors.has('image')}]"
                           name="image"
                           id="image"
                           v-model="form.image"
                           placeholder="Ex: /img/my-flag.png">
                    <small class="text-danger form-text" v-if="form.errors.has('image')">
                        {{ form.errors.first('image')}}
                    </small>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-link" @click="$emit('close')">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </div>
    </form>
</template>

<script>
  import Form from './Form'

  export default {
    name: 'LanguageForm',

    data() {
      return {
        form: new Form({
          language     : '',
          image        : '',
          language_code: ''
        })
      }
    },

    methods: {
      async submit() {
        try {
          const response = await this.form.post('/web/languages')
          this.$emit('create', response)
        } catch (e) {
          console.error(e)
        }
      }
    }
  }
</script>

<style scoped>

</style>
