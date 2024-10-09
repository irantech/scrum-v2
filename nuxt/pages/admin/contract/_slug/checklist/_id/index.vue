<template>
  <div>
    <adminIncludeCrumb name="فرم چک لیست"/>
    <contract-title-checklist-form :checklistContract="checklistContract"
                                   :contractTitleChecklist="contractTitleChecklist" :loading="loading"
                                   :contractTitleChecklistSection="contractTitleChecklistSection" :reverseLoading="reverseLoading"
                                   :reverseChecklistProcess="reverseChecklistProcess"/>
  </div>
</template>

<script>
import {mapState} from "vuex";
import ContractTitleChecklistForm from "../../../../../../components/admin/contract/checklist/form";

export default {
  name : 'contract-checklist' ,
  components: {ContractTitleChecklistForm},
  layout: 'admin',
  data() {
    return {
      loading  : false ,
      reverseLoading : false,
      managers : []
    }
  },
  created() {
    this.loading = true
    this.reverseLoading = true

    this.$store.dispatch('admin/section/LoadAdminSections')

    this.$store.dispatch('admin/checklistContract/getReverseChecklistProcesses', {
      checklist_id: this.$route.params.id,
      contract_id: this.$route.params.slug
    }).then(res => {
      this.reverseLoading = false
    })
  },
  async fetch(){
    this.$store.dispatch('admin/checklistContract/getContractChecklist', {
      contractId: this.$route.params.slug,
      checklistId: this.$route.params.id
    }).then(res => {
      this.loading = false
      this.$store.dispatch('admin/checklistContract/getContractTitleChecklists' , {
        checklistContract : res.data.data.id
      })
    })
  },
  computed : {
    ...mapState('admin/checklistContract' , ['checklistContract']),
    ...mapState('admin/checklistContract' , ['contractTitleChecklist']),
    ...mapState('admin/checklistContract' , ['contractTitleChecklistSection']),
    ...mapState('admin/contract' , ['reverseChecklistProcess']),
    // ...mapState('admin/user' , ['managerList'])
  }
}
</script>
