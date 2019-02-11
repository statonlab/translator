<template>
    <div>
        <slot></slot>
    </div>
</template>

<script>
  export default {
    name: 'OutClick',

    mounted() {
      const onClick = (event) => {
        if (event.target === this.$el || this.$el.contains(event.target)) {
          return
        }
        this.$emit('outClick', event)
      }

      document.addEventListener('click', onClick)

      this.$once('hook:destroyed', () => {
        document.removeEventListener('click', onClick)
      })
    }
  }
</script>

<style scoped>

</style>
