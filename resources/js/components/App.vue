<template>
    <div class="app-container">
        <sidebar @change="activeLink = $event"/>
        <div class="main-content">
            <navbar :title="activeLink.title"></navbar>
            <div class="container py-4">
                <router-view></router-view>
            </div>
        </div>
    </div>
</template>

<script>
  import Sidebar from './Sidebar'
  import Navbar from './Navbar'
  export default {
    name: 'App',

    components: {Navbar, Sidebar},

    watch: {
      activeLink(link) {
        document.title = `${link.title} | ${window.app.name}`
      }
    },

    data() {
      return {
        activeLink: ''
      }
    }
  }
</script>

<style scoped>

</style>
