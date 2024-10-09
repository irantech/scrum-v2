import {Doughnut, mixins} from 'vue-chartjs'

const {reactiveProp} = mixins;
export default {
    extends: Doughnut,
    mixins: [reactiveProp],
    props: ['chartData', 'chartOptions'],
    data(){
        return {
            htmlLegend: null
        }
    },
    mounted() {
        this.htmlLegend = this.generateLegend()
        this.renderChart(this.chartData, this.chartOptions);
    },
}
