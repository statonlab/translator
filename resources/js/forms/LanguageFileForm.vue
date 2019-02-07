<template>
    <form @submit.prevent="">
        <div class="modal-content">
            <div class="modal-header"></div>
            <div class="modal-body"></div>
            <div class="modal-footer d-flex justify-content-between">
                <button class="btn btn-link" type="button" @click="$emit('close')">Close</button>
            </div>
        </div>
    </form>
</template>

<script>
  export default {
    name: 'LanguageFileForm'
  }
</script>

<style scoped>

</style>
