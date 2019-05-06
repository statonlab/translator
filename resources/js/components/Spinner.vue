<template>
    <div class="spinner-container d-flex justify-content-center align-items-center"
         :style="`background-color: ${background};${fixed ? 'position: fixed !important;' : ''}left: ${left};`">
        <svg class="circular">
            <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
        </svg>
        <p v-if="message !== ''" class="spinner-message">{{ message }}</p>
    </div>
</template>
<script>
  export default {
    props: {
      background: {default: 'rgba(255, 255, 255, 0.5)', type: String},
      message   : {default: '', type: String},
      fixed     : {default: false, type: Boolean},
      left      : {default: '250px', required: false, type: Boolean}
    }
  }
</script>

<style scoped lang="scss">
    @import "../../sass/variables";

    $width: 50px;

    .spinner-container {
        position: absolute;
        top: 0;
        bottom: 0;
        right: 0;
        z-index: 9999999;

        .circular {
            animation: rotate 2s linear infinite;
            position: absolute;
            top: 50%;
            left: 50%;
            margin-top: -($width / 2);
            margin-left: -($width / 2);
            width: $width;
            height: $width;
        }

        .path {
            stroke-dasharray: 1, 200;
            stroke-dashoffset: 0;
            animation: dash 1.5s ease-in-out infinite, color 6s ease-in-out infinite;
            stroke-linecap: round;
        }

        .spinner-message {
            position: absolute;
            top: 50%;
            margin-top: $width;
            font-weight: $font-weight-bold;
            width: 100%;
            text-align: center;
        }
    }

    @keyframes rotate {
        100% {
            transform: rotate(360deg);
        }
    }

    @keyframes dash {
        0% {
            stroke-dasharray: 1, 200;
            stroke-dashoffset: 0;
        }
        50% {
            stroke-dasharray: 89, 200;
            stroke-dashoffset: -35;
        }
        100% {
            stroke-dasharray: 89, 200;
            stroke-dashoffset: -124;
        }
    }

    @keyframes color {
        100%, 0% {
            stroke: $red;
        }
        40% {
            stroke: $blue;
        }
        66% {
            stroke: $green;
        }
        80%, 90% {
            stroke: $yellow;
        }
    }
</style>
