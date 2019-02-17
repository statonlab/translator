<template>
    <form @submit.prevent="save()" @keydown="form.keydown($event)">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manage {{ notification.machine_name }}</h5>
                <button type="button" class="close" @click="$emit('close')" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" v-if="form.errors.has('machine_name')">
                    {{ form.errors.first('machine_name') }}
                </div>
                <div class="form-group">
                    <label for="title" class="form-label">Title</label>
                    <input type="text"
                           id="title"
                           name="title"
                           :class="['form-control', {'is-invalid': form.errors.has('title')}]"
                           v-model="form.title"
                           placeholder="E,g: Notify me when a new version is uploaded"
                    >
                    <small class="form-text text-danger"
                           v-if="form.errors.has('title')">
                        {{ form.errors.first('title') }}
                    </small>
                </div>

                <div class="form-group mb-0">
                    <label for="is_private" class="form-label">Visible to Users</label>
                    <select class="form-control" id="is_private" name="is_private" v-model="form.is_private">
                        <option value="0">Yes</option>
                        <option value="1">No</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-link" @click="$emit('close')">Close</button>
                <button type="submit" class="btn btn-primary" :disabled="loading">Save</button>
            </div>
        </div>
    </form>
</template>

<script>
  import Form from './Form'

  export default {
    name: 'NotificationRegistryForm',

    props: {notification: {required: true, type: Object}},

    mounted() {
      if (this.notification.title) {
        this.form.title = this.notification.title
      }
      this.form.is_private   = this.notification.is_private ? '1' : '0'
      this.form.machine_name = this.notification.machine_name
    },

    data() {
      return {
        loading: false,
        form   : new Form({
          title       : '',
          machine_name: '',
          is_private  : '0'
        })
      }
    },

    methods: {
      async save() {
        this.loading = true
        try {
          let response
          if (this.notification.id) {
            response = await this.form.put(`/web/subscription/${this.notification.id}`)
          } else {
            response = await this.form.post(`/web/subscriptions`)
          }

          this.$emit('save', response.data)
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
