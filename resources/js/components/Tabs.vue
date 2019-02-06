<template>
    <div>
        <div class="card border-0 shadow">
            <div class="card-header bg-white border-0 p-0">
                <ul class="nav nav-tabs">
                    <li v-for="tab in tabs" class="nav-item">
                        <a :href="tab.href"
                           :class="['nav-link', {'active' : tab.isActive}]"
                           @click="selectTab(tab)">
                            <ion-icon v-if="tab.icon ? tab.icon.length > 0 : false" :name="tab.icon"></ion-icon>
                            <span>{{ tab.name }}</span>
                        </a>
                    </li>
                </ul>
            </div>
            <slot></slot>
        </div>
    </div>
</template>

<script>
  export default {
    name: 'Tabs',

    data() {
      return {
        tabs: []
      }
    },

    created() {
    },

    methods: {
      selectTab(selectedTab) {
        this.tabs.forEach(tab => {
          tab.isActive = tab.name === selectedTab.name
        })
      }
    },

    mounted() {
      this.tabs = this.$children.concat([])

      this.tabs.forEach(tab => {
        if (this.$route.hash === tab.href) {
          this.selectTab(tab)
        }
      })
    }
  }
</script>

<style scoped>

</style>
