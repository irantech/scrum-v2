<template>
      <adminContractDetail :contract="contract" :checklists="checklists" :loading="loading"/>
</template>

<script>
import {mapState} from 'vuex'

export default  {
  name : 'contract-view',
  layout: 'admin' ,
  data(){
    return{
      loading :  false
    }
  },
  async fetch({store , route}) {
      // await store.dispatch('admin/contract/getContractById' , { id : route.params.id })
  },
  created() {
    this.loading = true
    this.$store.dispatch('admin/contract/getContractById' , { id : this.$route.params.id })
      .then(res =>{
      this.loading = false
    })
    this.$store.dispatch('admin/checklist/getChecklists')
  },
  computed : {
    ...mapState('admin/contract', ['contract']),
    ...mapState('admin/checklist' , ['checklists'])
  }
}
</script>
