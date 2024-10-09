<template>
    <div>
      <adminIncludeCrumb name="عناوین چک لیست ها"/>
      <adminTitleChecklistList :titleChecklists="titleChecklists" :titleChecklistLoading="titleChecklistLoading" :sectionList="sectionList"/>
    </div>
</template>

<script>
  import {mapState} from "vuex";

  export default {
    name : 'titleChecklists'  ,
    layout : 'admin' ,
    data() {
      return {
          titleChecklistLoading : false
      }
    },
    created() {
      this.titleChecklistLoading = true
      this.$store.dispatch('admin/section/LoadAdminSections')
      this.$store.dispatch('admin/titleChecklist/getTitleChecklists')
      .then(res => {
        this.titleChecklistLoading = false
      })
    },
    computed : {
      ...mapState('admin/titleChecklist' , ['titleChecklists']),
      ...mapState('admin/section' , ['sectionList']),
    }
  }
</script>
