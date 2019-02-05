<template>
    <div>
        <div v-if="user !== null">
            <tabs>
                <tab name="Personal Information" :selected="true" icon="contact">
                    <PersonalInformationForm
                            :user="user"
                    />
                </tab>
                <tab name="Password" icon="lock">
                    <change-password-form/>
                </tab>
                <tab name="Notifications" icon="notifications"></tab>
            </tabs>
        </div>
    </div>
</template>

<script>
  import Tabs from '../components/Tabs'
  import Tab from '../components/Tab'
  import PersonalInformationForm from '../forms/PersonalInformationForm'
  import ChangePasswordForm from '../forms/ChangePasswordForm'

  export default {
    name: 'Profile',

    components: {ChangePasswordForm, PersonalInformationForm, Tab, Tabs},

    mounted() {
      setTimeout(() => this.$root.$emit('changeTitle', 'Profile'), 100)
      this.loadUser()
    },

    data() {
      return {
        user: null
      }
    },

    methods: {
      async loadUser() {
        try {
          const {data} = await axios.get('/web/user')
          this.user    = data
        } catch (e) {
          console.error(e)
        }
      }
    }
  }
</script>

<style scoped>

</style>
