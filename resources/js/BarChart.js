import { Bar, mixins } from 'vue-chartjs'
const { reactiveProp } = mixins

export default {
    extends: Bar,
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
                responsive: true,
                maintainAspectRatio: false,
                spanGaps: false,
                scales: {
                    yAxes: [{
                        display: false,
                        gridLines: {
                            display: false
                        }
                    }],
                    xAxes: [{
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
