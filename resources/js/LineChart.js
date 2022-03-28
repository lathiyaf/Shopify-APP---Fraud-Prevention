import { Line, mixins } from 'vue-chartjs'
const { reactiveProp } = mixins

export default {
    extends: Line,
    mixins: [reactiveProp],
    // props: ['options'],
    data(){
      return{
          options:{
              // title:{
              //     display: true,
              //     text: 'Site Visitors',
              //     position: 'top',
              // },
              legend: {
                  display: false
              },
              datalabels:{
                  enabled: false
              },
              responsive: true,
              maintainAspectRatio: false,
              spanGaps: false,
              tooltips: {
                  enabled: false,
              },
              scales: {
                  yAxes: [{
                      display: false,
                      gridLines: {
                          display: false
                      }
                  }],
                  xAxes: [{
                      display: false,
                      gridLines: {
                          display: false
                      }
                  }]
              }
          }
      }
    },
    mounted () {
        // this.chartData is created in the mixin.
        // If you want to pass options please create a local options object
        this.renderChart(this.chartData, this.options)
    }
}
