<template>
  <div>
    <adminIncludeCrumb name="فرم چک لیست"/>
    <adminContractChecklistForm    :checklistContract="checklist_contract"
                                   :contractTitleChecklist="contractTitleChecklist"
                                   :signedSections="signedSections"
                                   :section_list="section_list"
                                   :reverseChecklistProcess="reverseChecklistProcess"
                                   :reverseLoading="reverseLoading"
                                   :customer_hold="customer_hold"
                                   :contractTitleChecklistSection="contractTitleChecklistSection" :sumLoading="sumLoading"/>
  </div>
</template>

<script>
import {mapState} from "vuex";
export default {
  name : 'contract-checklist' ,
  middleware : 'incompleteProfile',
  layout: 'admin',
  data() {
    return {
      reverseLoading : false ,
      sumLoading : false
    }
  },
  async asyncData({ app ,route ,store}) {

    const checklist_contract = await app.$axios.get(`checklist-contract/${route.params.id}`);
    const section_list = await app.$axios.get(`section`);
    await store.dispatch('admin/checklistContract/getContractTitleChecklists' , {
      checklistContract : route.params.id
    })
    const customer_hold = await app.$axios.get(`getCustomerHold/${route.params.id}`);
    return { checklist_contract: checklist_contract.data.data, section_list: section_list.data , customer_hold : customer_hold.data.data };
  },
  created() {
    this.reverseLoading = true
    this.sumLoading = true
    this.$store.dispatch('admin/checklistContract/getReverseChecklistProcesses', {
      checklistContract : this.$route.params.id
    }).then(res => {
      this.reverseLoading = false
    })
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
    ...mapState('admin/checklistContract' , ['reverseChecklistProcess']),
    ...mapState('admin/checklistContract' , ['checklist_process_duration']),
  }
}
</script>
