<template>
  <div>
    <adminIncludeCrumb name="لیست سطح دسترسی ها"/>
    <adminUserPermissionList :permissionList="permissionList"/>
  </div>
</template>

<script>

export  default  {
  name : 'user-permission' ,
  layout : 'admin',
  middleware : 'permissionListAccess',
  data(){
    return {
      permissionList  : []
    }
  },
  created() {
    this.$axios.get('permission')
      .then(response => {
        this.permissionList = response.data.data
      })
      .catch(error => console.log(error))
  },
}
</script>
