<template>
  <div>
    <client-only>
      <adminIncludeCrumb :name="name"/>
      <adminContractList :contractLoading="contractLoading"/>
    </client-only>
  </div>
</template>

<script>
  import {mapState} from "vuex";

  export default {
    name :'contract',
    layout : 'admin' ,
    data() {
      return {
        name : 'لیست قراردادها' ,
        contractLoading :  false
      }
    } ,
    async fetch({store}) {
      await  store.dispatch('admin/section/LoadAdminSections')
    },

    created() {
      this.contractLoading = true
      this.$store.dispatch('admin/section/LoadAdminSections').then(re => {
        this.$store.dispatch('admin/contract/LoadAdminContracts')
          .then(res => this.contractLoading = false)
      })
    },

  }
</script>

<style>

</style>
