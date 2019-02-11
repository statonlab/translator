<template>

</template>

<script>
  export default {
    name: 'PlatformFiles',

    mounted() {
      const id = this.$route.params.id

      this.loadPlatform(id)
    },

    data() {
      return {
        platform: null
      }
    },

    methods: {}
  }
</script>

<style scoped>

</style>
