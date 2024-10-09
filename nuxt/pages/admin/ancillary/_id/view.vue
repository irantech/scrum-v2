<template>
      <adminAncillaryDetail :ancillary="ancillary" :contract="contract"/>
</template>


<script>
  import {mapState} from 'vuex'
  export default {
    name : 'ancillary-view' ,
    layout : 'admin',
    created() {
      this.$store.dispatch('admin/ancillary/getAncillaryById' , {id : this.$route.params.id})
        .then(response =>{
          this.getContractData(response.data.data.contract_id)
        })
    },
    methods :{
      getContractData(contractId) {
          this.$store.dispatch('admin/contract/getContractById' , { id : contractId })
      }
    },
    computed :{
      ...mapState('admin/ancillary' , ['ancillary']),
      ...mapState('admin/contract' , ['contract'])
    }
  }
</script>
