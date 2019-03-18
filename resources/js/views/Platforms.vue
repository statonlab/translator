<template>
    <div>
        <div class="card border-0 shadow">
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
                        <ion-icon name="add"></ion-icon>
                        <span class="d-inline-block ml-1">New Platform</span>
                    </button>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-middle mb-0">
                    <thead>
                    <tr>
                        <th>Platform Name</th>
                        <th>Number of Languages</th>
                        <th>Number of Files</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="platform in platforms">
                        <td>
                            <editable
                                    @save="patch(platform, 'name', $event)"
                                    :value="platform.name">
                                <div class="mb-1"><strong>{{ platform.name }}</strong></div>
                                <progress-bar :value="platform.progress"/>
                            </editable>
                        </td>
                        <td>{{ platform.languages_count }}</td>
                        <td>
                            <router-link :to="`/platform/${platform.id}/files`">
                                {{ platform.files_count }}
                            </router-link>
                        </td>
                        <td>
                            <div class="d-flex justify-content-end">
                                <a class="btn btn-outline-info btn-sm mr-2"
                                   target="_blank"
                                   :href="`/download/${platform.id}`">
                                    <span class="icon d-inline-flex mr-2">
                                        <ion-icon name="cloud-download"></ion-icon>
                                    </span>
                                    <span>Download</span>
                                </a>
                                <button class="btn btn-outline-primary btn-sm mr-2" @click="addFile(platform)">
                                    <span class="icon d-inline-flex mr-2">
                                        <ion-icon name="cloud-upload"></ion-icon>
                                    </span>
                                    <span>Language File</span>
                                </button>
                                <button class="btn btn-outline-danger btn-sm"
                                        type="button"
                                        @click="destroy(platform)">
                                    <ion-icon name="trash"/>
                                </button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <modal v-if="showCreateModal" @close="showCreateModal = false">
            <platform-form
                    @close="showCreateModal = false"
                    @create="platformCreated()"
            />
        </modal>

        <modal v-if="showLanguageForm" @close="showLanguageForm = false">
            <language-file-form
                    @create="fileUploaded($event)"
                    :platform="platform"
                    @close="showLanguageForm = false"
            />
        </modal>
    </div>
</template>

<script>
  import Modal from '../components/Modal'
  import PlatformForm from '../forms/PlatformForm'
  import Editable from '../components/Editable'
  import Errors from '../forms/Errors'
  import LanguageFileForm from '../forms/LanguageFileForm'
  import ProgressBar from '../components/ProgressBar'

  export default {
    name      : 'Platforms',
    components: {ProgressBar, LanguageFileForm, Editable, PlatformForm, Modal},
    mounted() {
      this.loadPlatforms()
    },

    data() {
      return {
        search          : '',
        showCreateModal : false,
        platforms       : [],
        showLanguageForm: false,
        platform        : null
      }
    },

    watch: {
      search() {
        this.loadPlatforms()
      }
    },

    methods: {
      async loadPlatforms() {
        try {
          const {data} = await axios.get('/web/platforms', {
            search: this.search,
            limit : 100
          })

          this.platforms = data.data
        } catch (e) {
          console.error(e)
        }
      },

      platformCreated() {
        this.loadPlatforms()

        this.showCreateModal = false
      },

      async destroy(platform) {
        if (!confirm(`Are you sure you want to delete ${platform.name}?`)) {
          return
        }

        try {
          await axios.delete(`/web/platform/${platform.id}`)
          this.loadPlatforms()
        } catch (e) {
          alert('Unable to delete platform')
          console.error(e)
        }
      },

      async patch(platform, field, event) {
        try {
          let data    = {}
          data[field] = event.value
          await axios.patch(`/web/platform/${platform.id}`, data)
          this.loadPlatforms()
          event.done()
        } catch (e) {
          if (e.response && e.response.status === 422) {
            const errors = new Errors(e.response.data)
            if (errors.has(field)) {
              event.error(errors.first(field))
            }
          } else {
            alert('Unable to update platform')
          }
        }
      },

      addFile(platform) {
        this.platform         = platform
        this.showLanguageForm = true
      },

      fileUploaded(file) {
        this.showLanguageForm = false
        this.loadPlatforms()
      }
    }
  }
</script>

<style scoped>

</style>
