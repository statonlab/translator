<template>
    <form action="">
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
                    <input type="text" class="form-control" name="name" id="name" v-model="form.name" placeholder="Name">
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input type="text" class="form-control" name="email" id="email" v-model="form.email" placeholder="Email">
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="text" class="form-control" name="password" id="password" v-model="form.password" placeholder="Password">
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Password Confirmation</label>
                    <input type="text" class="form-control" name="password_confirmation" id="password_confirmation" v-model="form.password_confirmation" placeholder="Password Confirmation">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click="$emit('close')">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
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
          name: '',
          email: '',
          password: '',
          password_confirmation: ''
        })
      }
    }
  }
</script>

<style scoped>

</style>
