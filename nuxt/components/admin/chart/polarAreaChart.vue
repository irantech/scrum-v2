<script>
import { PolarArea } from "vue-chartjs";
export default {
  name : 'polar-area-chart',
  extends: PolarArea,
  props: ["data", "labels" , 'colors' , 'title'],
  mounted() {
    this.renderPolarChart ()
  },
  methods : {
    renderPolarChart() {
      this.renderChart({
        labels : this.chartLabels,
        datasets:
          [{
            backgroundColor: this.chartColors,
            data: this.chartData
          }]
        },
        {
          legend: {
            display: false
          },
        responsive: true,
        maintainAspectRatio: false ,
        max : 100 ,
        title: {
            display: true,
            text: this.chartTitle
          },
        scale: {
          ticks: {
            min: 0,
            max: 100
          }
        },
        tooltips: {
          callbacks: {
            label: function(tooltipItem, data) {
              return data['labels'][tooltipItem['index']] + ': ' + data['datasets'][0]['data'][tooltipItem['index']] + '%';
            }
          }
        }
        })
    }
  },
  computed: {

    chartData() {
      return this.data;
    },
    chartLabels() {
      return this.labels
    },
    chartColors() {
      return this.colors
    } ,
    chartTitle () {
      return this.title
    }
  },
  watch: {
    data: function() {
      //this.renderChart(this.data, this.options);
      this.renderPolarChart();
    } ,
  }
  // mounted () {
  //   this.renderChart({
  //     labels: ['Eating', 'Drinking', 'Sleeping', 'Designing'],
  //     datasets: [
  //       {
  //         label: 'My First dataset',
  //         backgroundColor: 'rgba(179,181,198,0.2)',
  //         pointBackgroundColor: 'rgba(179,181,198,1)',
  //         pointBorderColor: '#fff',
  //         pointHoverBackgroundColor: '#fff',
  //         pointHoverBorderColor: 'rgba(179,181,198,1)',
  //         data: [20, 13, 4, 5  , 100]
  //       },
  //     ]
  //   }, {responsive: true, maintainAspectRatio: false})
  // }
}

</script>
