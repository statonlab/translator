<template>
    <aside class="sidebar shadow">
        <div class="logo text-center mb-3 d-flex align-items-center bg-dark shadow"
             @click="$emit('change', 'Dashboard')">
            <router-link to="/" class="font-weight-bold d-flex align-items-center">
                <img src="/img/TranslateLogo.png" alt="Logo" class="img-fluid d-flex">
                <span class="d-inline-block ml-2 text-white">{{ appName }}</span>
            </router-link>
        </div>

        <form class="px-2 mb-3">
            <input class="form-control bg-light shadow-sm border-0"
                   type="search"
                   v-model="search"
                   placeholder="Search"
                   aria-label="Search">
        </form>

        <div class="px-2 pb-4">
            <ul class="nav nav-sidebar flex-column">
                <li v-for="link in filteredLinks"
                    class="nav-item"
                    @click="$emit('change', link)">
                    <router-link :to="link.path"
                                 :class="['nav-link', 'd-flex']"
                                 :exact="link.exact">
                        <ion-icon :name="link.icon"></ion-icon>
                        <span class="nav-text ml-2">{{ link.title }}</span>
                    </router-link>
                </li>
            </ul>

            <p class="text-white px-4" v-if="filteredLinks.length === 0">0 results found</p>
        </div>
    </aside>
</template>

<script>
  export default {
    name: 'Sidebar',

    data() {
      return {
        appName: window.app ? window.app.name : '',
        opened : '',
        search : '',
        links  : [
          {
            title: 'Dashboard',
            icon : 'analytics',
            path : '/',
            exact: true
          },
          {
            title: 'Translations',
            icon : 'filing',
            path : '/translate'
          },
          {
            title: 'Languages',
            icon : 'copy',
            path : '/languages'
          },
          {
            title: 'Users',
            icon : 'contacts',
            path : '/users'
          }
        ],

        filteredLinks: []
      }
    },

    mounted() {
      this.setInitialTitle()
      this.filteredLinks = this.links
    },

    watch: {
      search(term) {
        term = term.trim()
        if (term.length === 0) {
          this.filteredLinks = this.links
          return
        }

        this.filteredLinks = this.links.filter(link => link.title.indexOf(term) > -1)
      }
    },

    methods: {
      open(menu) {
        this.opened = menu
      },

      setInitialTitle() {
        for (let i in this.links) {
          if (!this.links.hasOwnProperty(i)) {
            return
          }

          const link = this.links[i]
          const path = window.location.pathname
          if ((link.exact && link.path === path) || (!link.exact && path.indexOf(link.path) > -1)) {
            this.$emit('change', link)
          }
        }
      }
    }
  }
</script>

<style scoped>

</style>
