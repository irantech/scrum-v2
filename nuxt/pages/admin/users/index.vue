<template>
  <div>
    <client-only>
      <adminIncludeCrumb name="مدیریت کاربران"/>
      <adminUserList :userList="userList" :roleList="roleList" :userLoading="userLoading"/>
    </client-only>
  </div>
</template>

<script>
  import {mapState} from "vuex";

  export default {
    name : 'users' ,
    layout  : 'admin' ,
    middleware : 'userListAccess',
    data(){
      return{
        userLoading : false
      }
    },
    created() {
      this.userLoading = true
      this.$store.dispatch('admin/user/getUserList')
        .then(respone =>{
          this.userLoading = false
        })
      this.$store.dispatch('admin/user/role/getUserRoleList')
    },
    computed: {
      ...mapState('admin/user' , ['userList']) ,
      ...mapState('admin/user/role' , ['roleList'])
    }
  }
</script>
