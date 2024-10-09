<template>
  <div>
      <adminIncludeCrumb name="زیرمجموعه"/>
      <adminBaseProgressSingle :baseProgress="baseProgress" :sectionList="sectionList" />
  </div>
</template>

<script>
  import {mapState} from "vuex";

  export default  {
    name : 'baseProgress_single',
    layout : 'admin' ,
    created()
    {
        this.$store.dispatch('admin/baseProgress/getBaseProgress' , {id : this.$route.params.id})
        this.$store.dispatch('admin/section/LoadAdminSections')
    },
    computed : {
      ...mapState('admin/baseProgress' , ['baseProgress']),
      ...mapState('admin/section' , ['sectionList'])
    }
  }
</script>
