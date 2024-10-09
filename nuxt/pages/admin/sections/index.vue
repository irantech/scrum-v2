<template>
  <div>
      <adminIncludeCrumb name="بخش ها" />
      <adminSectionList :sectionList="sectionList" :sectionLoading="sectionLoading"/>
  </div>

</template>

<script>
import {mapState} from "vuex";

export default {
  name : 'sections' ,
  layout : 'admin',
  data() {
    return {
      sectionLoading : false
    }
  },
  created() {
    this.sectionLoading = true
    this.$store.dispatch('admin/section/LoadAdminSections' ).then(res => this.sectionLoading = false)
  },
  computed :{
    ...mapState('admin/section' , ['sectionList'])
  }
}
</script>
