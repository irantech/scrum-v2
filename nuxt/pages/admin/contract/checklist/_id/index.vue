<template>
  <div>
    <crumb name="فرم چک لیست"/>
    <contract-title-checklist-form :checklistContract="checklistContract"
                                   :contractTitleChecklist="contractTitleChecklist"
                                   :contractTitleChecklistSection="contractTitleChecklistSection"/>
  </div>
</template>

<script>
import {mapState} from "vuex";

import Crumb from "../../../../../components/admin/include/crumb";
import ContractTitleChecklistForm from "../../../../../components/admin/contract/checklist/form";

export default {
  name : 'contract-checklist' ,
  components: {ContractTitleChecklistForm, Crumb},
  layout: 'admin',
  data() {
    return {
      loading  : false ,
      reverseLoading : false,
      managers : []
    }
  },
  created() {
    this.reverseLoading = true
    this.$store.dispatch('admin/section/LoadAdminSections')
    this.$store.dispatch('admin/checklistContract/getContractChecklist', {
      checklistContract: this.$route.params.id})

    this.$store.dispatch('admin/checklistContract/getContractTitleChecklists' , {
      checklistContract : this.$route.params.id
    })



    // this.$store.dispatch('admin/checklistContract/getReverseChecklistProcesses', {
    //   checklistContract : this.$route.params.id
    // }).then(res => {
    //   this.reverseLoading = false
    // })
  },
  computed : {
    ...mapState('admin/checklistContract' , ['checklistContract']),
    ...mapState('admin/checklistContract' , ['contractTitleChecklist']),
    ...mapState('admin/checklistContract' , ['contractTitleChecklistSection']),
    // ...mapState('admin/checklistContract' , ['reverseChecklistProcess']),
    // ...mapState('admin/user' , ['managerList'])
  }
}
</script>
