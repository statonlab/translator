<template>
    <form @submit.prevent="submit()">
        <div class="card-body">
            <div class="form-group limit-width">
                <label for="old_password" class="form-label">Old Password</label>
                <input type="password"
                       :class="['form-control', {'is-invalid': form.errors.has('old_password')}]"
                       name="old_password"
                       id="old_password"
                       v-model="form.old_password"
                       placeholder="Password">
                <small class="form-text text-danger" v-if="form.errors.has('old_password')">
                    {{ form.errors.first('old_password') }}
                </small>
            </div>

            <div class="form-group limit-width">
                <label for="password" class="form-label">Password</label>
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

            <div class="form-group limit-width mb-0">
                <label for="password_confirmation" class="form-label">Password Confirmation</label>
                <input type="password"
                       :class="['form-control', {'is-invalid': form.errors.has('password_confirmation')}]"
                       name="password_confirmation"
                       id="password_confirmation"
                       v-model="form.password_confirmation"
                       placeholder="Password Confirmation">
                <small class="form-text text-danger" v-if="form.errors.has('password_confirmation')">
                    {{ form.errors.first('password_confirmation') }}
                </small>
            </div>
        </div>
        <div class="card-footer border-0">
            <button class="btn btn-primary" type="submit">Update Password</button>
        </div>
    </form>
</template>

<script>
  import Form from './Form'

  export default {
    name: 'ChangePasswordForm',

    data() {
      return {
        form: new Form({
          old_password         : '',
          password             : '',
          password_confirmation: ''
        })
      }
    },

    methods: {
      async submit() {
        try {
          await this.form.patch('/web/user/password')
          this.form.reset()
        } catch (e) {
          if (!(e.response && e.response.status === 422)) {
            alert('Unable to update password')
          }
        }
      }
    }
  }
</script>

<style scoped>

</style>
