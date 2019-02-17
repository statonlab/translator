<template>
    <form @submit.prevent="">
        <div class="card-body">
            <div v-if="!loading && notifications.length === 0">
                There are no notifications that you can subscribe to.
            </div>
            <p v-if="notifications.length > 0">
                <b>Select notifications to subscribe to them</b>
            </p>
            <div class="form-group form-check" v-for="notification in notifications">
                <input type="checkbox"
                       class="form-check-input"
                       :id="`notification-${notification.id}`"
                       @change="toggle($event, notification)"
                       :checked="notification.subscribed"
                       :disabled="loading"
                       :title="notification.title">
                <label class="form-check-label" :for="`notification-${notification.id}`">
                    {{ notification.title }}
                </label>
            </div>
        </div>
    </form>
</template>

<script>
  export default {
    name: 'NotificationsForm',

    data() {
      return {
        loading      : true,
        notifications: []
      }
    },

    mounted() {
      this.loadNotifications()
    },

    methods: {
      async loadNotifications() {
        try {
          const {data}       = await axios.get('/web/subscriptions')
          this.notifications = data
        } catch (e) {
          console.error(e)
        }

        this.loading = false
      },

      async toggle(event, notification) {
        this.loading = true
        try {
          const {data} = await axios.post(`/web/subscription/${notification.id}`, {
            subscribe: event.target.checked ? 1 : 0
          })

          let word = data.subscribed ? 'subscribed' : 'unsubscribed'

          this.$notify({
            type: 'success',
            text: `You have been ${word} successfully!`
          })
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
