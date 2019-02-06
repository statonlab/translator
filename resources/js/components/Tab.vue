<template>
    <div v-show="isActive">
        <slot></slot>
    </div>
</template>

<script>
  export default {
    name: 'Tab',

    props: {
      name    : {required: true, type: String},
      selected: {required: false, default: false, type: Boolean},
      icon    : {required: false, type: String, default: ''}
    },

    data() {
      return {
        isActive: false
      }
    },

    mounted() {
      this.isActive = this.selected
    },

    computed: {
      href() {
        return '#' + this.name.toLowerCase().replace(/ /g, '-')
      }
    }
  }
</script>

<style scoped>

</style>
