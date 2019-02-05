<template>
    <div :class="['letter-icon d-flex align-items-center justify-content-center shadow-sm', {'letter-icon-sm': small}]"
         :title="value">
        <span>{{ letters }}</span>
    </div>
</template>

<script>
  export default {
    name: 'LetterIcon',

    props: {
      value : {required: true, type: String},
      format: {
        required: false, default: 'normal', validator(v) {
          return ['language', 'normal'].indexOf(v) > -1
        }
      },
      small : {required: false, default: false, type: Boolean}
    },

    computed: {
      letters() {
        if (this.format === 'language') {
          return this.value.substring(0, 2).toUpperCase()
        }

        var v = this.value.split(' ')
        if (v.length > 1) {
          return v[0].charAt(0) + v[v.length - 1].charAt(0)
        } else if (v.length === 1) {
          return v[0]
        }

        return ''
      }
    }
  }
</script>

<style scoped lang="scss">
    .letter-icon {
        width: 45px;
        height: 45px;
        background: rgb(206, 212, 218);
        background: linear-gradient(180deg, rgba(206, 212, 218, 1) 4%, rgba(108, 117, 125, 1) 86%);
        color: #fff;
        border-radius: 50%;
    }

    .letter-icon-sm {
        width: 35px;
        height: 35px;
        font-size: .9rem;
    }
</style>
