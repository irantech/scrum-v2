<template>
  <div>
    <adminIncludeCrumb name="چک لیست ها" />
    <adminChecklistList :checklists="checklists"  :languageList="languageList" :sectionList="sectionList"/>
  </div>
</template>

<script>
  import {mapState} from "vuex";

  export default {
    name : 'checklists' ,
    layout : 'admin' ,
    created() {
      this.$store.dispatch('admin/checklist/getChecklists')
      this.$store.dispatch('admin/language/LoadLanguages')
      this.$store.dispatch('admin/section/LoadAdminSections')
    },
    computed : {
      ...mapState('admin/checklist' , ['checklists']) ,
      ...mapState('admin/language' , ['languageList']) ,
      ...mapState('admin/section' , ['sectionList'])
    }
  }
</script>
