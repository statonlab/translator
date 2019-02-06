<template>
    <i :class="`icon ion-${id}`"></i>
</template>

<script>
  export default {
    name: 'IonIcon',

    props: {
      name: {required: true}
    },

    watch: {
      name(name) {
        this.make(name)
      }
    },

    data() {
      return {
        id: ''
      }
    },

    mounted() {
      this.make(this.name)
    },

    methods: {
      make(name) {
        if (name.indexOf('ios') === 0 || name.indexOf('md') === 0 || name.indexOf('logo') === 0) {
          this.id = name
          return
        }

        this.id = `md-${name}`
      }
    }
  }
</script>

<style scoped>

</style>
