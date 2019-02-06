<template>
    <out-click @outClick="outClick()">
        <div>
            <div class="editable" @click="show()">
                <slot v-if="!editing"></slot>
            </div>
            <form @submit.prevent="save()" v-if="editing">
                <div class="input-group">
                    <input v-if="type !== 'select'"
                           @keydown="error = ''"
                           :type="type === 'password' ? 'password' : 'text'"
                           :class="['form-control', {'is-invalid': error.length > 0}]"
                           name="Edit"
                           id="text-edit"
                           title="Edit"
                           v-model="content"
                           :disabled="disabled"
                           autofocus>
                    <select v-if="type === 'select'"
                            @click="error = ''"
                            name="edit"
                            id="select-edit"
                            v-model="content"
                            :class="['form-control', 'custom-select', {'is-invalid': error.length > 0}]">
                        <option v-for="option in options" :value="option.value">
                            {{ option.label }}
                        </option>
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary"
                                type="button"
                                :disabled="disabled"
                                @click="close()">
                            <ion-icon name="close"></ion-icon>
                        </button>
                        <button class="btn btn-outline-primary"
                                @click="save()"
                                :disabled="disabled"
                                type="button">Save
                        </button>
                    </div>
                </div>
                <small class="form-text text-danger" v-if="error.length > 0">{{ error }}</small>
            </form>
        </div>
    </out-click>
</template>

<script>
  import OutClick from './OutClick'

  export default {
    name      : 'Editable',
    components: {OutClick},
    props     : {
      value   : {required: true, type: [String, Number]},
      type    : {
        required : false,
        default  : 'text',
        validator: function (value) {
          // The value must match one of these strings
          return ['text', 'password', 'select'].indexOf(value) !== -1
        }
      },
      options : {required: false, default: () => []},
      disabled: {required: false, default: false}
    },

    data() {
      return {
        editing: false,
        content: '',
        error  : ''
      }
    },

    methods: {
      show() {
        this.content = this.value
        this.editing = true
        if (this.$refs.input) {
          this.$refs.input.focus()
        }
      },

      outClick() {
        if (this.content !== this.value) {
          return
        }

        this.editing = false
      },

      close() {
        this.editing = false

        this.$emit('close')
      },

      setError(message) {
        this.error = message
      },

      save() {
        this.$emit('save', {
          value: this.content,
          done : () => {
            this.close()
          },
          error: (message) => {
            this.setError(message)
          }
        })
      }
    }
  }
</script>

<style scoped>
    .editable:hover {
        text-decoration: underline dashed;
        cursor: pointer;
    }
</style>
