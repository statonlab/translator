<template>
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h3 class="h5 flex-grow-1">Translation Coverage</h3>
                        <div>
                            <select name="platform"
                                    v-model="platform"
                                    id="coverage-platform"
                                    class="form-control">
                                <option :value="-1">Select Platform</option>
                                <option v-for="platform in platforms" :value="platform.id">{{ platform.name }}</option>
                            </select>
                        </div>
                    </div>
                    <div v-if="platform !== -1 && series.length > 0">
                        <apex-chart type="radialBar"
                                    height="350"
                                    :series="series"
                                    :options="chartOptions"/>
                    </div>
                    <div v-if="platforms.length === 0 && !loading" class="text-muted mt-3">
                        Please
                        <router-link to="/platforms">create a platform</router-link>
                        first to start
                        translating language files.
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">

        </div>
    </div>
</template>

<script>
  import ApexChart from 'vue-apexcharts'
  import Colors from '../helpers/Colors'

  export default {
    name: 'Dashboard',

    components: {ApexChart},

    mounted() {
      this.loadPlatforms()
    },

    data() {
      return {
        loading     : true,
        platforms   : [],
        platform    : -1,
        series      : [],
        chartOptions: {
          colors     : [],
          plotOptions: {
            radialBar: {
              startAngle: 0,
              endAngle  : 275,
              track     : {},
              dataLabels: {
                name : {
                  fontSize: '22px'
                },
                value: {
                  fontSize: '16px'
                },
                total: {
                  show : true,
                  label: 'Coverage'
                }
              }
            }
          },
          labels     : []
        }
      }
    },

    watch: {
      platform(id) {
        this.loadPlatformProgress(id)
      }
    },

    methods: {
      async loadPlatforms() {
        try {
          const {data}   = await axios.get('/web/platforms/compressed')
          this.platforms = data

          if (data.length) {
            this.platform = data[0].id
          } else {
            this.loading = false
          }
        } catch (e) {
          console.error(e)
        }
      },

      async loadPlatformProgress(id) {
        this.loading = true
        try {
          const {data} = await axios.get(`/web/platform/${id}/progress`)
          const colors = [Colors.primary, Colors.success, Colors.info, Colors.secondary, Colors.warning, Colors.light]

          let series = []
          let labels = []

          data.forEach(language => {
            labels.push(language.language)
            series.push(Math.floor(language.progress))
          })

          let finalColors = colors.slice(0, labels.length > colors.length ? colors.length : labels.length)
          let options     = this.chartOptions

          options.colors = finalColors
          options.labels = labels

          this.series       = series
          this.chartOptions = options
        } catch (e) {
          console.error(e)
        }
        this.loading = false
      }
    }
  }
</script>

<style scoped>

</style>
