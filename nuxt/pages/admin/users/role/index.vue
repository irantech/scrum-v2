<template>
  <div>
      <adminIncludeCrumb name="لیست نقش کاربران"/>
      <adminUserRoleList :roleList="roleList" :permissionList="permissionList"
                         :roleLoading="roleLoading" :sectionList="sectionList"/>
  </div>
</template>

<script>
  import {mapState} from "vuex";

  export  default  {
    name : 'user-role' ,
    layout : 'admin',
    middleware : 'roleListAccess' ,
    data(){
      return {
        permissionList : []
      }
    },
    created() {
      this.roleLoading = true
      this.$store.dispatch('admin/user/role/getUserRoleList')
      .then(res =>{
        this.roleLoading = false
      })
      this.getPermissions();
      this.$store.dispatch('admin/section/LoadAdminSections')
    },
    computed :{
      ...mapState('admin/user/role' , ['roleList']) ,
      ...mapState('admin/section' , ['sectionList'])
    } ,
    methods :{
      getPermissions() {
        this.$axios.get('permission')
          .then(response => {
            this.permissionList = response.data.data
          })
          .catch(error => console.log(error))
      }
    }

  }
</script>
