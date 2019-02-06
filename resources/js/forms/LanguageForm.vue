<template>
    <form @submit.prevent="submit()">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Language</h5>
                <button type="button" class="close" @click="$emit('close')" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" v-if="platforms.length === 0 && loading">
                <ion-icon name="refresh" class="is-spinning"></ion-icon>
                Loading platforms. Please wait.
            </div>
            <div class="modal-body" v-if="platforms.length === 0 && !loading">
                <p class="mb-0 text-danger">
                    You must create a platform first. Please visit the
                    <router-link to="/platforms">platforms</router-link>
                    page to create one.
                </p>
            </div>
            <div class="modal-body" v-if="platforms.length > 0 && !loading">
                <div class="form-group">
                    <label class="form-label" for="platform_id">Platform<sup class="text-danger">*</sup></label>
                    <select
                            name="platform_id"
                            id="platform_id"
                            :class="['form-control', {'is-invalid': form.errors.has('platform_id')}]"
                            v-model="form.platform_id"
                    >
                        <option v-for="platform in platforms" :value="platform.id">{{ platform.name }}</option>
                    </select>
                    <small class="text-danger form-text" v-if="form.errors.has('language')">
                        {{ form.errors.first('language')}}
                    </small>
                </div>

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

                <div class="form-group mb-0">
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

    mounted() {
      this.loadPlatforms()
    },

    data() {
      return {
        loading  : false,
        platforms: [],
        form     : new Form({
          language     : '',
          image        : '',
          language_code: '',
          platform_id  : ''
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
      },

      async loadPlatforms() {
        this.loading = true

        try {
          const {data} = await axios.get('/web/platforms', {
            limit: 100
          })

          this.platforms = data.data

          if (this.platforms.length > 0) {
            this.form.platform_id = this.platforms[0].id
          }
        } catch (e) {
          alert('Unable to load platforms. Please try to refresh the page.')
          console.error(e)
        }

        this.loading = false
      }
    }
  }
</script>

<style scoped>

</style>
