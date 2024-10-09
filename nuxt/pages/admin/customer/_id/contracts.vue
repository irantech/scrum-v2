<template>
  <div>
      <adminIncludeCrumb :name="title"></adminIncludeCrumb>
      <adminCustomerContractList :contract_list="contract_list"/>
  </div>
</template>


<script>

  export default  {
    name  : 'customer-contracts' ,
    layout : 'admin'  ,
    data() {
      return{
        title : ` لیست قراردادها ` ,
        contract_list  : []
      }
    },
    created() {
      this.$axios.get(`customer/${this.$route.params.id}/contracts`).then( response =>{
        this.contract_list = response.data.data
      }).catch(error => console.log(error))
    },
  }
</script>
