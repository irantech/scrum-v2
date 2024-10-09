<template>
  <div>
      <adminIncludeCrumb name="مراحل اصلی"></adminIncludeCrumb>
      <adminBaseProgressList :baseProgressList="baseProgressList" :loadingBaseProgress="loadingProgress"
                             :sectionList="sectionList" :softwareList="softwareList"/>
  </div>
</template>

<script>
  import {mapState} from "vuex";

  export default  {
    name: 'base-progress',
    layout : 'admin' ,
    data() {
      return {
        loadingProgress : false
      }
    },
    created() {
      this.loadingProgress = true
      this.$store.dispatch('admin/baseProgress/getBaseProgressList').then(()=>this.loadingProgress = false)
      this.$store.dispatch('admin/section/LoadAdminSections')
      this.$store.dispatch('admin/software/getSoftwareList')
    },
    computed :{
      ...mapState('admin/baseProgress' , ['baseProgressList']),
      ...mapState('admin/section' , ['sectionList']),
      ...mapState('admin/software' , ['softwareList'])
    }

  }
</script>
