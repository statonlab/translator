<template>
    <form @submit.prevent="">
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1 mr-1">
                        <textarea class="form-control" placeholder="Translate the string"></textarea>
                    </div>
                    <div class="d-flex justify-content-end align-items-end">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                the text for the translatable string in English
            </div>
        </div>
    </form>
</template>

<script>
  export default {
    name: 'TranslatedLine'
  }
</script>

<style scoped>

</style>
