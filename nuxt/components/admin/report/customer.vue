<template>
  <div class="border p-3">
    <p class="font-weight-bold">
      <Icon type="ios-pricetag" />
      {{ customer.sign_date }}
    </p>
    <div class="row">
        <Alert type="error" class="text-center w-100 mx-2" v-if="customer.checklistContract.length === 0"> فعلا به این قرارداد چک لیستی اختصاص داده نشده </Alert>
        <customer-report-chart v-else  class="col-12" v-for="(checklistContract , index) in customer.checklistContract"
                                  :checklistContract="checklistContract" :key="index"/>

    </div>
  </div>
</template>

<script>
import CustomerReportChart from "./customerChart";
export default {
  name : 'report-customer-single' ,
  components: {CustomerReportChart},
  props : ['customer' , 'index'],
}
</script>
