<template>
  <div>
    <adminIncludeCrumb name="profile"/>
    <adminProfileInfo :user="user"/>
  </div>
</template>

<script>
  import {mapState} from "vuex";

  export default {
    name : 'profile' ,
    layout : 'admin',
    computed : {
      ...mapState('auth' , ['user'])
    }
  }
</script>
