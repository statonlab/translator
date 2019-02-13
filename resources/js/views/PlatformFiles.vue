<template>
    <div>
        <div class="card border-0 shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center border-0">
                <form @submit="$event.preventDefault()">
                    <input type="search"
                           v-model="search"
                           class="form-control shadow-none form-control-sm mr-1 w-full w-max-18 w-min-15"
                           placeholder="Search">
                </form>

                <div class="ml-auto">
                    <button class="d-flex btn btn-primary btn-sm font-weight-bold"
                            @click.prevent="showCreateModal = true">
                        <span class="icon mr-2">
                            <ion-icon name="cloud-upload"></ion-icon>
                        </span>
                        <span class="d-inline-block">New File</span>
                    </button>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <p class="mb-0 p-4 text-muted" v-if="!loading && !files.length">
                    This platform has no files yet.
                </p>
                <table class="table mb-0 table-striped table-middle" v-if="!loading && files.length">
                    <thead>
                    <tr>
                        <th>Version</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="file in files">
                        <td>{{ file.app_version }}</td>
                        <td>
                            <div class="d-flex justify-content-end">
                                <a :href="`/download/file/${file.id}`"
                                   class="btn btn-sm btn-outline-primary mr-2"
                                   target="_blank">
                                    <span class="icon">
                                        <ion-icon name="cloud-download"/>
                                    </span>
                                    <span>Download File</span>
                                </a>
                                <button class="btn btn-sm btn-outline-danger"
                                        @click="destroy(file)">
                                    <span class="icon">
                                        <ion-icon name="trash"/>
                                    </span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <pagination
                :page="page"
                :count="last_page"
        />

        <modal v-if="showCreateModal" @close="showCreateModal = false">
            <language-file-form
                    :platform="platform"
                    @create="fileCreated()"
                    @close="showCreateModal = false"
                    @next="next()"
                    @previous="previous()"
                    @click="goTo($event)"
            />
        </modal>
    </div>
</template>

<script>
  import LanguageFileForm from '../forms/LanguageFileForm'
  import Modal from '../components/Modal'
  import Pagination from '../components/Pagination'

  export default {
    name      : 'PlatformFiles',
    components: {Pagination, Modal, LanguageFileForm},
    mounted() {
      const id = this.$route.params.id

      this.loading = true
      this.loadFiles(id)
    },

    watch: {
      search() {
        this.loadFiles(this.platform.id)
      }
    },

    data() {
      return {
        loading        : false,
        platform       : {},
        files          : [],
        search         : '',
        showCreateModal: false,
        page           : 1,
        total          : 0,
        last_page      : 0
      }
    },

    methods: {
      async loadFiles(id) {
        if (typeof id === 'undefined') {
          id = this.platform.id
        }

        try {
          const {data}   = await axios.get(`/web/platform/${id}/files`, {
            params: {
              search: this.search.trim(),
              page  : this.page
            }
          })
          this.platform  = data.platform
          this.files     = data.files.data
          this.page      = data.files.current_page
          this.total     = data.files.total
          this.last_page = data.files.last_page
          this.$root.$emit('changeTitle', `${this.platform.name} Language Files`)
        } catch (e) {
          console.error(e)
        }
        this.loading = false
      },

      fileCreated() {
        this.showCreateModal = false
        this.loadFiles()
      },

      next() {
        if (this.page < this.last_page) {
          this.goTo(this.page + 1)
        }
      },

      previous() {
        if (this.page > 1) {
          this.goTo(this.page - 1)
        }
      },

      goTo(page) {
        this.page = page
        this.loadFiles()
      },

      async destroy(file) {
        if (!confirm(`Are you sure you want to delete file for version ${file.app_version}?`)) {
          return
        }

        try {
          await axios.delete(`/web/file/${file.id}`)
          this.loadFiles()
        } catch (e) {
          console.error(e)
        }
      }
    }
  }
</script>

<style scoped>

</style>
