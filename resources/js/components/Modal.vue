<template>
    <transition name="fade">
    <div class="modal show" tabindex="-1" role="dialog" style="display: block">
        <div class="modal-dialog" role="document" style="" ref="modal">
            <slot></slot>
        </div>
    </div>
    </transition>
</template>

<script>
  export default {
    name: 'Modal',

    mounted() {
      document.addEventListener('click', this.outClick)
    },

    destroyed() {
      document.removeEventListener('click', this.outClick)
    },

    methods: {
      outClick(e) {
        if(this.$refs.modal.contains(e.target)) {
          return
        }

        this.$emit('close')
      }
    }
  }
</script>

<style scoped>

</style>
