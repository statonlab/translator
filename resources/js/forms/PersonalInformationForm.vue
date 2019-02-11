<template>
    <form @submit.prevent="submit()" @keydown="form.keydown($event.target.name)">
        <div class="card-body">
            <div class="form-group limit-width">
                <label for="name" class="form-label">Name</label>
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

            <div class="form-group limit-width">
                <label for="email" class="form-label">Email</label>
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

            <div class="form-group limit-width mb-0">
                <label class="form-label" for="role">Role</label>
                <input type="text"
                       class="form-control disabled width-auto"
                       name="role"
                       id="role"
                       readonly
                       disabled
                       :value="user.role.name">
            </div>
        </div>
        <div class="card-footer border-0">
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
</template>

<script>
  import Form from './Form'

  export default {
    name: 'PersonalInformationForm',

    props: {
      user: {required: true, type: Object}
    },

    watch: {
      user(user) {
        this.form.setDefault(user)
      }
    },

    mounted() {
      this.form.setDefault(this.user)
    },

    data() {
      return {
        form: new Form({
          name : '',
          email: ''
        })
      }
    },

    methods: {
      async submit() {
        try {
          const {data} = this.form.put('/web/user')
          this.$emit('update', data)
        } catch (e) {
          console.error(e)
        }
      }
    }
  }
</script>

<style scoped>

</style>
