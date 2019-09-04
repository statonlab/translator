<template>
    <div>
        <div class="progress" :style="`height: ${height}`">
            <div class="progress-bar text-center"
                 :style="`width: ${parsed}%`"
                 role="progressbar"
                 :aria-valuenow="value"
                 aria-valuemin="0"
                 aria-valuemax="100">
                <small class="text-dark-shadow">{{ parsed }}%</small>
            </div>
        </div>

        <div class="d-flex">
            <slot></slot>
        </div>
    </div>
</template>

<script>
  export default {
    name    : 'ProgressBar',
    computed: {
      parsed() {
        return Math.floor(this.value)
      },
    },
    props   : {
      value     : {required: true, type: Number},
      show_value: {required: false, type: Boolean, default: false},
      height    : {required: false, type: String, default: '1rem'},
    },
  }
</script>

<style scoped>
    .text-dark-shadow {
        text-shadow: 0 0 2 #000;
    }
</style>
