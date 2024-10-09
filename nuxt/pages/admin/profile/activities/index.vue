<template>
  <div>
    <adminIncludeCrumb name="لیست فعالیت های شما"/>
    <adminProfileActivityList :activities="activities"/>

  </div>
</template>

<script>
  import {mapState} from "vuex";

  export default {
    name : 'activity' ,
    layout : 'admin',
    data() {
      return{
        loading : false
      }
    },
    created() {
      this.loading = true
      this.$store.dispatch('auth/getActivityList').then(response=>{
        this.loading = false
      })
    },
    computed :{
        ...mapState('auth' , ['activities'])
    }
  }
</script>
