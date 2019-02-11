<template>
    <form @submit.prevent="save()" @keydown="form.keydown($event)">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Language File</h5>
                <button type="button" class="close" @click="$emit('close')" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="app_version" class="form-label">
                        App Version
                    </label>
                    <input type="text"
                           :class="['form-control', {'is-invalid': form.errors.has('app_version')}]"
                           name="app_version"
                           v-model="form.app_version"
                           placeholder="E,g: v1.0.0"
                           id="app_version">
                    <small class="form-text text-danger" v-if="form.errors.has('app_version')">
                        {{ form.errors.first('app_version') }}
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label" for="language-file">Language File</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file"
                                   name="file"
                                   :class="['custom-file-input', {'is-invalid':form.errors.has('file')}]"
                                   id="language-file"
                                   ref="file"
                                   @change="fileChanged()">
                            <label class="custom-file-label" for="language-file">
                                {{ form.file.length ? form.file[0].name : 'Choose File' }}
                            </label>
                        </div>
                    </div>
                    <small class="form-text text-muted">
                        {{ progress > 0 ? `${progress}%` : 'Allowed formats: json.' }}
                    </small>
                    <small class="form-text text-danger" v-if="form.errors.has('file')">
                        {{ form.errors.first('file') }}
                    </small>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button :class="['btn', 'btn-link', {'disabled': loading}]"
                        type="button"
                        @click="$emit('close')"
                        :disabled="loading">Close
                </button>
                <button class="btn btn-primary" type="submit">Upload</button>
            </div>
        </div>
    </form>
</template>

<script>
  import Form from './Form'

  export default {
    name: 'LanguageFileForm',

    props: {platform: {required: true, type: Object}},

    data() {
      return {
        form    : new Form({
          app_version: '',
          file       : [],
          platform_id: -1
        }),
        progress: 0,
        loading : false
      }
    },

    mounted() {
      this.form.setAsFile('file')
      this.form.onProgressChange((e) => {
        this.updateProgress(e)
      })
      this.form.platform_id = this.platform.id
    },

    methods: {
      fileChanged() {
        this.form.file = this.$refs.file.files
      },

      async save() {
        this.loading  = true
        this.progress = 0
        try {
          const response = await this.form.post('/web/files')

          this.$emit('create', response.data)
        } catch (e) {
          console.error(e)
        }
        this.progress = 0
        this.loading  = false
      },

      updateProgress(event) {
        this.progress = Math.floor(event.loaded / event.total * 100)
      }
    }
  }
</script>

<style scoped>

</style>
