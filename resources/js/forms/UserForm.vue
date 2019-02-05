<template>
    <form @submit.prevent="submit" @keydown="form.errors.clear($event.target.name)">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create User</h5>
                <button type="button" class="close" @click="$emit('close')" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label" for="name">Name</label>
                    <input type="text"
                           :class="['form-control', {'is-invalid': form.errors.has('name')}]"
                           name="name"
                           id="name"
                           v-model="form.name"
                           placeholder="Name">
                    <small class="form-text text-danger" v-if="form.errors.has('name')">
                        {{ form.errors.first('name') }}
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input type="text"
                           :class="['form-control', {'is-invalid': form.errors.has('email')}]"
                           name="email"
                           id="email"
                           v-model="form.email"
                           placeholder="Email">
                    <small class="form-text text-danger" v-if="form.errors.has('email')">
                        {{ form.errors.first('email') }}
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Role</label>
                    <select name="role"
                            :class="['form-control', {'is-invalid': form.errors.has('role')}]"
                            id="role"
                            v-model="form.role">
                        <option value="User">User</option>
                        <option value="Admin">Admin</option>
                    </select>
                    <small class="form-text text-danger" v-if="form.errors.has('role')">
                        {{ form.errors.first('role') }}
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password"
                           :class="['form-control', {'is-invalid': form.errors.has('password')}]"
                           name="password"
                           id="password"
                           v-model="form.password"
                           placeholder="Password">
                    <small class="form-text text-danger" v-if="form.errors.has('password')">
                        {{ form.errors.first('password') }}
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Password Confirmation</label>
                    <input type="password"
                           class="form-control"
                           name="password_confirmation"
                           id="password_confirmation"
                           v-model="form.password_confirmation"
                           placeholder="Password Confirmation">
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
    name: 'UserForm',

    data() {
      return {
        form: new Form({
          name                 : '',
          email                : '',
          password             : '',
          password_confirmation: '',
          role                 : 'User'
        })
      }
    },

    methods: {
      async submit() {
        try {
          const response = await this.form.post('/web/users')
          this.$emit('create', response)
          this.form.reset()
        } catch (e) {
          console.error(e)
        }
      }
    }
  }
</script>

<style scoped>

</style>
