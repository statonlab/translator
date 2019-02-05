<template>
    <nav class="d-flex justify-content-between">
        <div>
            <a href="javascript:;"
               :class="['btn btn-primary mb-1 mr-1', {'disabled': page === 1}]"
               @click="$emit('previous')"
               :disabled="page === 1">
                Previous
            </a>
        </div>
        <nav aria-label="pageSelector">
            <ul class="pagination">
                <li v-for="i in count"
                    :class="['page-item', {'active': page === i}]">
                    <span class="page-link" v-if="page === i">
                        {{ i }}
                        <span class="sr-only">(current)</span>
                    </span>
                    <a href="#" class="page-link"
                       v-if="page !== i"
                       @click.prevent="$emit('click', i)">
                        {{ i }}
                    </a>
                </li>
            </ul>
        </nav>
        <div>
            <a href="javascript:;"
               :class="['btn btn-primary mb-1 ml-1', {'disabled': page === count}]"
               @click="$emit('next')"
               :disabled="page === count">
                Next
            </a>
        </div>
    </nav>
</template>

<script>

  export default {
    props: {
      page : {type: Number, required: false, default: 1},
      count: {type: Number, required: false, default: 1}
    },
    name : 'Pagination'
  }
</script>

<style scoped>

</style>
