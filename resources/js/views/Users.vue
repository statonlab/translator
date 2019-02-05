<template>
    <div>
        <div class="card border-0 shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center border-0">
                <form @submit="$event.preventDefault()">
                    <input type="search"
                           name="search"
                           id="user-search"
                           v-model="search"
                           autocomplete="off"
                           class="form-control shadow-none form-control-sm mr-1 w-full w-max-18 w-min-15"
                           placeholder="Search">
                </form>

                <div class="ml-auto">
                    <button class="d-flex btn btn-primary btn-sm font-weight-bold"
                            @click.prevent="addUserModal = true">
                        <ion-icon name="add"></ion-icon>
                        <span class="d-inline-block ml-1">New User</span>
                    </button>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table mb-0 table-middle">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Password</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="user in users">
                        <td class="icon-td">
                            <letter-icon
                                    :value="user.name"
                            />
                        </td>
                        <td>
                            <editable :value="user.name"
                                      :disabled="editing"
                                      @save="patch(user, 'name', $event)">
                                <div>
                                    <span class="d-block">{{ user.name }}</span>
                                    <small class="text-muted">Member since {{ user.registered_at }}</small>
                                </div>
                            </editable>

                        </td>
                        <td>
                            <editable
                                    :disabled="editing"
                                    :value="user.email"
                                    @save="patch(user, 'email', $event)">
                                {{ user.email }}
                            </editable>
                        </td>
                        <td>
                            <editable :value="user.role.name"
                                      type="select"
                                      :disabled="editing"
                                      :options="[{label: 'Admin', value: 'Admin'}, {label: 'User', value: 'User'}]"
                                      @save="patch(user, 'role', $event)">
                                {{ user.role.name }}
                            </editable>
                        </td>
                        <td>
                            <editable type="password"
                                      :disabled="editing"
                                      :value="''"
                                      @save="patch(user, 'password', $event)">
                                <div class="d-flex">
                                    <span v-for="i in 8">
                                        <!-- this is code for circle -->
                                        &#9679;
                                    </span>
                                </div>
                            </editable>
                        </td>
                        <td class="text-right">
                            <button type="button"
                                    @click="destroy(user)"
                                    class="btn btn-outline-danger btn-sm">
                                <ion-icon name="trash"></ion-icon>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <pagination :count="last_page"
                    :page="page"
                    @click="goTo($event)"
                    @previous="previous()"
                    @next="next()"/>

        <modal v-if="addUserModal" @close="addUserModal = false">
            <user-form @close="addUserModal = false"
                       @create="userCreated($event)"></user-form>
        </modal>
    </div>
</template>

<script>
  import Modal from '../components/Modal'
  import UserForm from '../forms/UserForm'
  import Editable from '../components/Editable'
  import Pagination from '../components/Pagination'
  import LetterIcon from '../components/LetterIcon'

  export default {
    name: 'Users',

    components: {LetterIcon, Pagination, Editable, UserForm, Modal},

    mounted() {
      this.loadUsers()
    },

    watch: {
      search() {
        this.loadUsers()
      }
    },

    data() {
      return {
        users       : [],
        addUserModal: false,
        search      : '',
        editing     : false,
        page        : 1,
        last_page   : 1,
        total       : 0
      }
    },

    methods: {
      async loadUsers() {
        try {
          const {data}   = await axios.get('/web/users', {
            params: {
              search: this.search,
              page  : this.page
            }
          })
          this.users     = data.data
          this.page      = data.current_page
          this.last_page = data.last_page
          this.total     = data.total
        } catch (e) {
          console.error(e)
        }
      },

      userCreated(response) {
        this.loadUsers()
        this.addUserModal = false
      },

      async destroy(user) {
        if (!confirm(`Are you sure you want to delete ${user.name}?`)) {
          return
        }

        try {
          await axios.delete(`/web/users/${user.id}`)
          this.loadUsers()
        } catch (e) {
          alert(e.response && e.response.status === 422 ? e.response.data.message : 'Unable to delete user')
          console.error(e)
        }
      },

      async patch(user, field, event) {
        this.editing = true
        try {
          let data       = {}
          data[field]    = event.value
          const response = await axios.patch(`/web/users/${user.id}`, data)
          this.loadUsers()
          event.done()
        } catch (e) {
          alert('Could not update field')
        }
        this.editing = false
      },

      next() {
        if (this.page < this.last_page) {
          this.goTo(this.page + 1)
        }
      },

      previous() {
        if (this.page > 1) {
          this.goTo(this.page - 1)
        }
      },

      goTo(page) {
        this.page = page
        this.loadUsers()
      }
    }
  }
</script>

<style scoped>
    .icon-td {
        width: 50px;
    }
</style>
