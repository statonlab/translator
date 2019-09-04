<template>
    <div>
        <div class="mb-4">
            <form @submit.prevent class="row">
                <div class="form-group col-6 col-md-3">
                    <label for="platforms" class="form-label d-block mb-2">Platform</label>
                    <select name="platforms"
                            id="platforms"
                            class="form-control"
                            v-model="selectedPlatform">
                        <option v-for="platform in platforms" :value="platform.id">
                            {{ platform.name }}
                        </option>
                    </select>
                </div>
                <div class="form-group col-6 col-md-3">
                    <label for="languages" class="form-label mb-2 d-block">Language</label>
                    <select name="languages"
                            id="languages"
                            class="form-control"
                            v-model="selectedLanguage">
                        <option v-for="language in languages" :value="language.id">
                            {{ language.language }} ({{ language.language_code }})
                        </option>
                    </select>
                </div>
                <div class="form-group col-6 col-md-3">
                    <label for="needs_updating" class="form-label mb-2 d-block">Show</label>
                    <select name="needs_updating" id="needs_updating" class="form-control" v-model="show">
                        <option value="all">
                            Everything
                        </option>
                        <option value="both">
                            New lines and updated lines
                        </option>
                        <option value="needs_updating">
                            Only lines that need updating
                        </option>
                        <option value="new_only">
                            Only new lines
                        </option>
                    </select>
                </div>
                <div class="form-group col-6 col-md-3">
                    <label class="form-label d-block mb-2 d-flex">
                        <span>Progress</span>
                        <span class="ml-auto font-weight-normal">
                            {{ completed || '0' }} / {{ total || '0' }}
                        </span>
                    </label>
                    <progress-bar :value="progress" :show-value="true">
                        <small class="ml-auto text-muted">{{ wordCount }} words</small>
                    </progress-bar>
                </div>
            </form>
        </div>

        <div class="card" v-if="!loading && lines.length === 0">
            <div class="card-body" v-if="platforms.length === 0">
                Please
                <router-link to="/platforms">create a platform</router-link>
                first to be able to start translating.
            </div>
            <div class="card-body" v-if="platforms.length > 0 && languages.length === 0">
                Please
                <router-link to="/languages">create a language</router-link>
                first
                to be able to start translating.
            </div>
            <div class="card-body" v-if="languages.length > 0">
                There is no translatable text that matches your filtering criteria.
            </div>
        </div>

        <div v-for="line in lines">
            <translated-line :line="line" @save="updateProgress($event)" :key="line.id"/>
        </div>

        <pagination v-if="last_page > 1"
                    :page="page"
                    :count="last_page"
                    @next="next()"
                    @previous="previous()"
                    @click="goTo($event)"/>

        <Spinner v-if="loading"/>
    </div>
</template>

<script>
  import TranslatedLine from '../components/TranslatedLine'
  import ProgressBar from '../components/ProgressBar'
  import Pagination from '../components/Pagination'
  import Spinner from '../components/Spinner'

  export default {
    name      : 'Translate',
    components: {Spinner, Pagination, ProgressBar, TranslatedLine},
    data() {
      return {
        loading         : true,
        show            : 'both',
        platforms       : [],
        selectedPlatform: -1,
        languages       : [],
        selectedLanguage: -1,
        lines           : [],
        progress        : 0,
        total           : 0,
        page            : 1,
        last_page       : 1,
        completed       : 0,
        wordCount       : 0,
      }
    },

    watch: {
      selectedPlatform() {
        this.page = 1
        this.loadLanguages()
      },

      selectedLanguage() {
        this.page = 1
        this.loadLines()
      },

      show() {
        this.page = 1
        this.loadLines()
      },
    },

    mounted() {
      this.loadPlatforms()
    },

    methods: {
      async loadPlatforms() {
        try {
          const {data}   = await axios.get('/web/translation/platforms')
          this.platforms = data
          if (data.length) {
            this.selectedPlatform = data[0].id
          } else {
            this.selectedPlatform = -1
            this.loading          = false
          }
        } catch (e) {
          console.error(e)
        }
      },

      async loadLanguages() {
        if (this.selectedPlatform <= 0) {
          return
        }

        try {
          const {data}   = await axios.get('/web/translation/languages/' + this.selectedPlatform)
          this.languages = data
          if (data.length) {
            this.selectedLanguage = data[0].id
          } else {
            this.selectedLanguage = -1
            this.loading          = false
          }
        } catch (e) {
          console.error(e)
        }
      },

      async loadLines() {
        if (this.selectedLanguage <= 0) {
          return
        }

        this.loading = true
        try {
          const {data}   = await axios.get('/web/translation/lines/' + this.selectedLanguage, {
            params: this.getParams(),
          })
          this.lines     = data.data
          this.page      = data.current_page
          this.last_page = data.last_page
          this.updateProgress()

          if (data.total === 0 && data.current_page > 1) {
            this.goTo(1)
          }
        } catch (e) {
          console.error(e)
        }
        this.loading = false
      },

      getParams() {
        let params = {
          page: this.page,
        }

        switch (this.show) {
          case 'both':
            return {
              needs_updating: 1,
              new_only      : 1,
              ...params,
            }
            break
          case 'all':
            return params
            break
          case 'needs_updating':
            return {
              needs_updating: 1,
              ...params,
            }
            break
          case 'new_only':
            return {
              new_only: 1,
              ...params,
            }
            break
        }
      },

      async updateProgress(event) {
        try {
          const {data}   = await axios.get('/web/progress/language/' + this.selectedLanguage)
          this.progress  = data.progress
          this.completed = data.completed
          this.total     = data.total
          if(event) {
            this.lines = this.lines.map(l => {
              if(l.id === event.id) {
                return event
              }

              return l
            })
          }
          this.computeWordCount()
        } catch (e) {
          console.error(e)
        }
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
        this.loadLines()
        window.scrollTo(0, 0)
      },

      computeWordCount() {
        if (this.lines.length > 0) {
          this.wordCount = this.lines.reduce((a, b) => {
            return a + (b.value === null ? parseInt(b.serialized_line.value.length) : 0)
          }, 0)
          return
        }
        this.wordCount = 0
      },
    },
  }
</script>

<style scoped>

</style>
