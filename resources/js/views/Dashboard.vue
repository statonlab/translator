<template>
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-body pb-0">
                    <h3 class="h5">Translation Coverage</h3>
                    <apex-chart type="radialBar" height="350" :series="series" :options="chartOptions"></apex-chart>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow">
                <div class="card-body pb-0">
                    <h3 class="h5">New Words Added Per Month</h3>
                    <apex-chart type="bar" :series="barSeries" :options="barChartOptions"></apex-chart>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  import ApexChart from 'vue-apexcharts'
  import Colors from '../helpers/Colors'

  export default {
    name: 'Dashboard',

    components: {ApexChart},

    data() {
      return {
        barChartOptions: {
          colors: [Colors.primary, Colors.success],
          chart : {
            // id: 'vuechart-example'
          },
          xaxis : {
            categories: [
              'Mar, 2018',
              'Apr, 2018',
              'May, 2018',
              'Jun, 2018',
              'Jul, 2018',
              'Aug, 2018',
              'Sept, 2018',
              'Oct, 2018',
              'Nov, 2018'
            ]
          }
        },
        barSeries      : [{
          name: 'English Words',
          data: [30, 40, 35, 50, 49, 60, 70, 91, 100]
        },
          {
            name: 'Spanish Words',
            data: [33, 10, 36, 98, 11, 24, 70, 82, 99]
          }],
        series         : [100, 55, 67, 83],
        chartOptions   : {
          colors     : [Colors.primary, Colors.success, Colors.info, Colors.secondary],
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
          labels     : ['English', 'Spanish', 'French', 'Dutch']
        }
      }
    }
  }
</script>

<style scoped>

</style>
