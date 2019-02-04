<template>
    <div>
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow">
                    <div class="card-header d-flex justify-content-between align-items-center border-0">
                        <input type="text"
                               class="form-control shadow-none form-control-sm mr-1 w-full w-max-18 w-min-15"
                               placeholder="Search">

                        <div class="ml-auto">
                            <button class="d-flex btn btn-primary btn-sm font-weight-bold"
                                    @click.prevent="addUserModal = true">
                                <ion-icon name="add"></ion-icon>
                                <span class="d-inline-block ml-1">New User</span>
                            </button>
                        </div>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Registered At</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="user in users">
                                <td>{{ user.name }}</td>
                                <td>{{ user.email }}</td>
                                <td>{{ user.role.name }}</td>
                                <td>{{ user.created_at }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <modal v-if="addUserModal" @close="addUserModal = false">
            <user-form @close="addUserModal = false"></user-form>
        </modal>
    </div>
</template>

<script>
  import Modal from '../components/Modal'
  import UserForm from '../forms/UserForm'

  export default {
    name      : 'Users',
    components: {UserForm, Modal},
    mounted() {
      this.loadUsers()
    },

    data() {
      return {
        users       : [],
        addUserModal: false
      }
    },

    methods: {
      async loadUsers() {
        try {
          const {data} = await axios.get('/web/users')
          this.users   = data
        } catch (e) {
          console.error(e)
        }
      }
    }
  }
</script>

<style scoped>

</style>
