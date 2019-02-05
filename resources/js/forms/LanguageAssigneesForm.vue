<template>
    <form @submit.prevent="$emit('close')">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Users to {{ language.language }}</h5>
                <button type="button" class="close" @click="$emit('close')" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <input type="search" class="form-control" placeholder="Search" v-model="search">
                </div>
                <div class="d-flex justify-content-between p-2 align-items-center item-striped"
                     v-for="user in users">
                    <p class="mb-0">{{ user.name }}</p>
                    <button :class="['btn btn-sm', {'btn-outline-danger': isAssigned(user), 'btn-outline-success': !isAssigned(user), 'disabled': loading}]"
                            type="button"
                            @click="toggle(user)"
                            :disabled="loading">
                        {{ isAssigned(user) ? 'Remove' : 'Add'}}
                    </button>
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
  export default {
    name: 'LanguageAssigneesForm',

    props: {language: {type: Object, required: true}},

    data() {
      return {
        users  : [],
        search : '',
        loading: false
      }
    },

    mounted() {
      this.loadUsers()
    },

    watch: {
      search() {
        this.loadUsers()
      }
    },

    methods: {
      isAssigned(user) {
        return this.language.users.filter(u => u.id === user.id).length > 0
      },

      async toggle(user) {
        this.loading = true
        try {
          const response = await axios.post(`/web/language/${this.language.id}/user`, {
            user_id: user.id
          })

          this.$emit('toggle', {
            user,
            op: response.data.attached ? 'attach' : 'detach'
          })
        } catch (e) {
          const toggle = this.isAssigned(user) ? 'unassign' : 'assign'
          alert(`Unable to ${toggle} user`)
          console.error(e)
        }

        this.loading = false
      },

      async loadUsers() {
        try {
          const {data} = await axios.get('/web/users', {params: {limit: 10, search: this.search}})
          this.users   = data.data
        } catch (e) {
          console.error(e)
        }
      }
    }
  }
</script>

<style scoped>

</style>
