<template>
  <div>
    <adminIncludeCrumb name="موارد چک لیست " />
    <contract-title-checklist-assign
      :contractTitleChecklistSection="contractTitleChecklistSection"
      :contractChecklistSectionLoading="contractChecklistSectionLoading"
      :checklistContract="checklistContract"/>
  </div>
</template>

<script>
import {mapState} from 'vuex'
import ContractTitleChecklistAssign from "../../../../components/admin/contract/checklist/assign";

export default  {
  name : 'contract-checklist-assign',
  components: {ContractTitleChecklistAssign},
  layout: 'admin',
  data() {
    return{
      checklistLoading  : false ,
      contractChecklistSectionLoading : false
    }
  },
  created() {
    this.$store.dispatch('admin/checklistContract/getContractChecklist' , {
      checklistContract: this.$route.params.id
    })
    // this.checklistLoading = true
    this.contractChecklistSectionLoading = true
    // this.$store.dispatch('admin/checklist/getChecklist' , {id : this.$route.params.id})
    //   .then(res => {
    //     this.checklistLoading = false
    //   })
    this.$store.dispatch('admin/user/getUserList')
    this.$store.dispatch('admin/section/LoadAdminSections')
    // this.$store.dispatch('admin/contract/getContractById' , { id : this.$route.params.slug})
    this.$store.dispatch('admin/checklistContract/getContractTitleChecklists' ,  {
      checklistContract: this.$route.params.id}).then(res => this.contractChecklistSectionLoading = false)
  },
  computed :{
    // ...mapState('admin/checklist' , ['checklist']) ,
    // ...mapState('admin/contract' , ['contract']),
    ...mapState('admin/checklistContract' , ['contractTitleChecklistSection']),
    ...mapState('admin/checklistContract' , ['checklistContract'])
  }
}
</script>
