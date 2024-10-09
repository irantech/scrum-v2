<script>
    import {Doughnut, mixins} from 'vue-chartjs'
    const {reactiveProp} = mixins;
    export default {
        extends: Doughnut,
        mixins: [reactiveProp],
        props: ['chartData', 'chartOptions'],
        data(){
        },
        mounted() {
            this.renderChart(this.chartData, this.chartOptions);
        },
    }

</script>

<style>

</style>
