<template>
  <div class="col-11 m-auto">

    <Card>
        <p slot="title">{{ customerReport.name }}</p>
        <adminReportCustomer v-for="(customer , index) in customerReport.contracts"
                               :customer="customer" :key="index"/>
    </Card>


  </div>
</template>

<script>
export default {
  name : 'report-customer-single' ,
  props : ['customerReport'],
}
</script>
