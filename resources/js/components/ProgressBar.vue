<template>
    <div>
        <div class="progress">
            <div class="progress-bar"
                 :style="`width: ${parsed}%`"
                 role="progressbar"
                 :aria-valuenow="value"
                 aria-valuemin="0"
                 aria-valuemax="100"></div>
        </div>
        <small class="text-muted">{{ parsed }}% completed</small>
    </div>
</template>

<script>
  export default {
    name : 'ProgressBar',
    computed: {
      parsed() {
        return Math.floor(this.value)
      }
    },
    props: {
      value: {required: true, type: Number},
      show_value: {required: false, type: Boolean, default: false}
    }
  }
</script>

<style scoped>

</style>
