<script>
import {Pie} from 'vue-chartjs'

export default {
  extends: Pie,
  props: ["data", "labels" , 'colors'],
  mounted() {
    this.renderPieChart ()
  },
  methods : {
    renderPieChart() {
      this.renderChart({
        labels: this.chartLabels,
        datasets: [
          {
            backgroundColor: this.chartColors,
            data: this.chartData
          }
        ]
      }, {responsive: true, maintainAspectRatio: false})
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
    }
  },
  watch: {
    data: function() {
      //this.renderChart(this.data, this.options);
      this.renderPieChart();
    } ,
  }
}
</script>

