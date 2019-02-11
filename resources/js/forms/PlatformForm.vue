<template>
    <form @submit.prevent="submit()">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Platform</h5>
                <button type="button" class="close" @click="$emit('close')" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-0">
                    <label for="platform-name" class="form-label">Platform Name</label>
                    <input type="text"
                           v-model="form.name"
                           :class="['form-control', {'is-invalid': form.errors.has('name')}]"
                           name="name"
                           id="platform-name"
                           placeholder="Example: MyApp or com.myapp">
                    <small class="form-text text-danger" v-if="form.errors.has('name')">
                        {{ form.errors.first('name') }}
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
    name: 'PlatformForm',

    data() {
      return {
        form: new Form({
          name: ''
        })
      }
    },

    methods: {
      async submit() {
        try {
          const response = await this.form.post('/web/platforms')
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
