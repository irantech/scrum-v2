<template>
  <div>
    <Card class="mb-2">
      <nuxt-link :to="`/admin/contract-checklist/${checklistContract.id}`">
        <polar-area-chart style="height: 250px !important;" v-if="totalComplete > 0 && checklistContract.checklist" :labels="chart_data.labels" :data="chart_data.data"
                          :colors="chart_data.backgroundColor" :title="checklistContract.checklist.title"/>
        <p class="text-center" v-if="totalComplete > 0"> پیشرفت: {{totalComplete}}%</p>
        <p class="text-center" v-else-if="checklistContract.checklist">
          {{ checklistContract.checklist.title }}
          <br>
          در صف انجام
        </p>
      </nuxt-link>
    </Card>

  </div>
</template>

<script>
  import PolarAreaChart from "../chart/polarAreaChart";
  export default {
    components: {PolarAreaChart},
    props: ['checklistContract'],
    name: 'customer-report-chart',
    data() {
      return {
        chart_options: [],
        chart_data: [],
        labels: [],
        bgColors: [],
        values: [],
        totalComplete: 0
      }
    },
    methods: {
      setChartData() {
        this.labels = []
        this.bgColors = []
        this.values = []
        this.totalComplete = 0
        this.ancillaryData.titleChecklistUser.forEach((base, index) => {
          // if (typeof base.description != 'undefined') {
            let titlePivot = base.section.title;
            this.labels.push(titlePivot);
            this.values.push(base.average * 100);
            this.bgColors.push(base.section.color);
            this.totalComplete += ( base.average * 100 );
        });
        if(this.ancillaryData.titleChecklistUser.length > 0 ){
          this.totalComplete = this.totalComplete / this.values.length;
          this.totalComplete   = this.totalComplete.toFixed(2)
        }

        this.chart_data = {
            labels: this.labels,
            data: this.values,
            backgroundColor: this.bgColors,
        }
      }
    },
    computed: {
      ancillaryData(){
        return this.checklistContract
      },
    },
    watch : {
      checklistContract() {
        this.setChartData()
      }
    },
    mounted() {
      this.setChartData()
    }
  }
</script>
