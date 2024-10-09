<template>
  <div>
    <adminIncludeCrumb v-if="customerReport.name" :name="` گزارش قرارداد ${customerReport.name}`"/>
    <adminIncludeCrumb v-else :name="` گزارش قرارداد`"/>
    <adminReportCustomerSingle :customerReport="customerReport"/>
  </div>
</template>

<script>
import {mapState} from "vuex";

export default  {
  name : 'report-customers' ,
  layout : 'admin' ,
  created() {
    this.$store.dispatch('admin/reports/customer/getCustomerReport' , {
      id : this.$route.params.id
    })
  },
  computed : {
    ...mapState('admin/reports/customer' , ['customerReport']),
  }
}
</script>
