<template>
    <aside class="sidebar shadow">
        <div class="logo text-center mb-3 d-flex align-items-center bg-dark shadow">
            <router-link to="/" class="font-weight-bold d-flex align-items-center">
                <!--<div class="logo-icon d-flex">-->
                    <!--<ion-icon name="ios-bowtie" class="text-dark"></ion-icon>-->
                <!--</div>-->

                <img src="/img/TranslateLogo.png" alt="Logo" class="img-fluid d-flex">
                <span class="d-inline-block ml-2 text-white">{{ appName }}</span>
            </router-link>
        </div>

        <div class="px-2 pb-4">
            <ul class="nav nav-sidebar flex-column">
                <li class="nav-item">
                    <router-link to="/" class="nav-link d-flex" :exact="true">
                        <ion-icon name="analytics"></ion-icon>
                        <span class="nav-text ml-2">Dashboard</span>
                    </router-link>
                </li>
                <li class="nav-item">
                    <router-link to="/notifications" class="nav-link d-flex">
                        <ion-icon name="notifications"></ion-icon>
                        <span class="nav-text ml-2">Notifications</span>
                        <ion-icon name="ios-arrow-back" class="ml-auto"></ion-icon>
                    </router-link>
                </li>
            </ul>
        </div>
    </aside>
</template>

<script>
  export default {
    name: 'Sidebar',

    data() {
      return {
        appName: window.app ? window.app.name : ''
      }
    }
  }
</script>

<style scoped>

</style>
