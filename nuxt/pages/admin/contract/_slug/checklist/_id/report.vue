<template>
  <div>
    <adminIncludeCrumb name="گزارش روند این چک لیست"/>
    <adminContractChecklistList :checklistProcessList="checklistProcessList"/>
  </div>
</template>

<script>
import {mapState} from "vuex";

export default {
  name : 'report-checklist' ,
  layout: 'admin',
  data() {
    return {
      loading  : false ,
    }
  },
  created() {
    this.loading = true
    this.$store.dispatch('admin/contract/getAllChecklistProcesses' ,  {
      contract_id : this.$route.params.slug,
      checklist_id : this.$route.params.id
    }).then(res =>  {
      this.loading = false
    })
  },
  computed : {
    ...mapState('admin/contract' , ['checklistProcessList'])
  }
}
</script>
