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
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="platform in platforms">
                        <td>
                            <editable
                                    @save="patch(platform, 'name', $event)"
                                    :value="platform.name">
                                {{ platform.name }}
                            </editable>
                        </td>
                        <td>{{ platform.languages_count }}</td>
                        <td class="text-right">
                            <button class="btn btn-outline-danger btn-sm ml-auto"
                                    type="button"
                                    @click="destroy(platform)">
                                <ion-icon name="trash"/>
                            </button>
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
    </div>
</template>

<script>
  import Modal from '../components/Modal'
  import PlatformForm from '../forms/PlatformForm'
  import Editable from '../components/Editable'
  import Errors from '../forms/Errors'

  export default {
    name      : 'Platforms',
    components: {Editable, PlatformForm, Modal},
    mounted() {
      this.loadPlatforms()
    },

    data() {
      return {
        search         : '',
        showCreateModal: false,
        platforms      : []
      }
    },

    watch: {
      search() {
        this.loadLanguages()
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
      }
    }
  }
</script>

<style scoped>

</style>
