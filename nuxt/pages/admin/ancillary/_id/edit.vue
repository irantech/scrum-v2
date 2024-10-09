<template>
      <adminAncillaryEdit :softwareList="softwareList"/>
</template>

<script>
import {mapState} from 'vuex'
  export default {
    name : 'ancillary-edit' ,
    layout :'admin' ,
    created() {
      this.$store.dispatch('admin/software/getSoftwareList')
    },
    computed : {
        ...mapState('admin/software' ,['softwareList'])
    }
  }
</script>
