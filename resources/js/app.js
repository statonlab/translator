/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap'
import Vue from 'vue'

// Plugins
import VueRouter from 'vue-router'

// Screens
import App from './components/App'
import Dashboard from './views/Dashboard'
import Users from './views/Users'
import Translate from './views/Translate'
import Languages from './views/Languages'
import Profile from './views/Profile'
import Platforms from './views/Platforms'
import IonIcon from './components/IonIcon'
import PlatformFiles from './views/PlatformFiles'

// Vue.config.ignoredElements = [/^ion-/]

// Register VueRouter
Vue.use(VueRouter)

// Each route should map to a component. The "component" can
// either be an actual component constructor created via
// `Vue.extend()`, or just a component options object.
// We'll talk about nested routes later.
const routes = [
  {path: '/', component: Dashboard},
  {path: '/users', component: Users},
  {path: '/translate', component: Translate},
  {path: '/languages', component: Languages},
  {path: '/profile', component: Profile},
  {path: '/platforms', component: Platforms},
  {path: '/platform/{id}/files', component: PlatformFiles}
]

// 3. Create the router instance and pass the `routes` option
// You can pass in additional options here, but let's
// keep it simple for now.
const router = new VueRouter({
  mode: 'history',
  routes
})

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('ion-icon', IonIcon)

new Vue({
  router,
  components: {
    App
  }
}).$mount('#app')
