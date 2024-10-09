<template>
  <div>
    <adminIncludeCrumb name="زبان ها" />
    <adminChecklistLanguageList :languageList="languageList" :languageLoading="languageLoading" />
  </div>
</template>

<script>
import {mapState} from "vuex";

export default {
  name : 'languages' ,
  layout : 'admin' ,
  data() {
    return {
      languageLoading : false
    }
  },
  created() {
    this.languageLoading = true
    this.$store.dispatch('admin/language/LoadLanguages').then(res=>{
      this.languageLoading = false
    })
  },
  computed : {
    ...mapState('admin/language' , ['languageList'])
  }
}
</script>
