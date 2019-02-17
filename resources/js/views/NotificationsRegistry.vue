<template>
    <div>
        <div class="card shadow border-0">
            <div class="card-header border-bottom-0">
                <h5 class="card-title">Manage Notifications</h5>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead>
                    <tr>
                        <th>Machine Name</th>
                        <th>Title</th>
                        <th>Visible to Users</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="notification in notifications">
                        <td>{{ notification.machine_name }}</td>
                        <td>{{ notification.title || 'Not Set' }}</td>
                        <td>{{ notification.is_private ? 'No' : 'Yes' }}</td>
                        <td>
                            <button type="button" class="btn btn-outline-primary btn-sm" @click.prevent="edit(notification)">
                                <ion-icon name="create"/>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <modal v-if="showEditModal" @close="showEditModal = false">
            <notification-registry-form
                    :notification="selectedNotification"
                    @close="showEditModal = false"
                    @save="edited()"
            />
        </modal>
    </div>
</template>

<script>
  import Modal from '../components/Modal'
  import NotificationRegistryForm from '../forms/NotificationRegistryForm'

  export default {
    name      : 'NotificationsRegistry',
    components: {NotificationRegistryForm, Modal},
    data() {
      return {
        notifications       : [],
        selectedNotification: null,
        showEditModal       : false
      }
    },

    mounted() {
      this.loadRegistry()
    },

    methods: {
      async loadRegistry() {
        try {
          const {data}       = await axios.get('/web/notifications-registry')
          this.notifications = data
        } catch (e) {
          console.error(e)
        }
      },

      edit(notification) {
        this.selectedNotification = notification
        this.showEditModal        = true
      },

      edited() {
        this.loadRegistry()
        this.showEditModal = false
      }
    }
  }
</script>

<style scoped>

</style>
