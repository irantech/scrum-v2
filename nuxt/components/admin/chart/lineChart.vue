<script>
import { Line } from "vue-chartjs";

export default {
  extends: Line,
  props: ["data", "labels"],
  mounted() {
    this.renderLineChart ()
  },
  computed: {
    chartData() {
      return this.data;
    },
    chartLabels() {
      return this.labels
    }
  },
  methods: {
    renderLineChart() {
      this.renderChart({
          labels: this.chartLabels,
          datasets: [
            {
              label: "تعداد",
              data: this.chartData,
              backgroundColor: "#4bc0c0",
              borderColor: "#4bc0c0",
              borderWidth: 2,
              fill: false
            }
          ]
        },
        {
          responsive: true,
          maintainAspectRatio: false ,
          title: {
            display: true,
            text: "تعداد قراردادها در هر ماه",
            fontSize: 24,
            fontColor: "#6b7280",
          },
          tooltips: {
            backgroundColor: "#4bc0c0",
          },
          scales: {
            xAxes: [
              {
                gridLines: {
                  display: true,
                },
              },
            ],
            yAxes: [
              {
                ticks: {
                  beginAtZero: true,
                  max: 20,
                  min: 0,
                  stepSize: 1,
                },
                gridLines: {
                  display: true,
                },
              },
            ],
          },
        })
    },
  },

  watch: {
    data: function() {
      //this.renderChart(this.data, this.options);
      this.renderLineChart();
    } ,
  }
};
</script>
