<template>
    <div class="app-container">
        <sidebar/>
        <div class="main-content">
            <navbar></navbar>
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
    components: {Navbar, Sidebar}
  }
</script>

<style scoped>

</style>
