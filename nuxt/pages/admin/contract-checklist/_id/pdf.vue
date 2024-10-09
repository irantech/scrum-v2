<template>
  <div>
    <adminContractChecklistPdf    :checklistContract="checklist_contract"
                                   :contractTitleChecklist="contractTitleChecklist"
                                   :signedSections="signedSections"
                                   :section_list="section_list"
                                   :contractTitleChecklistSection="contractTitleChecklistSection"
                                  :sumLoading="sumLoading"/>
  </div>
</template>

<script>
import {mapState} from "vuex";
export default {
  name : 'contract-checklist-pdf' ,
  layout: 'admin',
  data() {
    return {
      sumLoading : false
    }
  },
  async asyncData({ app ,route ,store}) {

    const checklist_contract = await app.$axios.get(`checklist-contract/${route.params.id}`);
    const section_list = await app.$axios.get(`section`);
    await store.dispatch('admin/checklistContract/getContractTitleChecklists' , {
      checklistContract : route.params.id
    })
    return { checklist_contract: checklist_contract.data.data, section_list: section_list.data };
  },
  created() {
    this.sumLoading = true
    this.$store.dispatch('admin/checklistContract/sumChecklistProcessDuration', {
      checklistContract : this.$route.params.id
    }).then(res => {
      this.sumLoading = false
    })
  },
  computed : {
    ...mapState('admin/checklistContract' , ['contractTitleChecklist']),
    ...mapState('admin/checklistContract' , ['contractTitleChecklistSection']),
    ...mapState('admin/checklistContract' , ['signedSections']),
    ...mapState('admin/checklistContract' , ['checklist_process_duration']),
  }
}
</script>
