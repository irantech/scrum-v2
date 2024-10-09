<template>
  <div class="wrapper-header">
    <Menu mode="horizontal" :theme="theme1" active-name="1">
      <Icon type="md-menu" class="text-light d-md-none d-sm-block" @click="showMenu" style="font-size: 25px"/>
      <MenuItem name="1" class="d-flex" to="/">
        <img class="img-fluid d-none d-md-block" src="~/assets/images/logo.svg" width="150px">
        <img class="img-fluid d-md-none d-sm-block" src="~/assets/images/logo-svg.svg">
      </MenuItem>
<!--      <button ref="showMenu" class="navbar-toggler" type="button" >-->
<!--        <span  class="navbar-toggler-icon"></span>-->
<!--      </button>-->
      <Submenu name="5" class="d-flex align-items-center float-left">
        <template slot="title">
          <Badge dot v-if="user.unread_notify > 0">
            <img v-if="user.avatar" :src="`https://api.ladyscarf.ir/uploads/image/${user.avatar}`" alt="avatar" width="40" height="40" style="border-radius: 50%">
            <img v-else src="~/assets/images/avatar.png" alt="avatar" width="40" height="40" style="border-radius: 50%">
          </Badge>
          <span v-else>
            <img v-if="user.avatar" :src="`https://api.ladyscarf.ir/uploads/image/${user.avatar}`" alt="avatar" width="40" height="40" style="border-radius: 50%">
            <img v-else src="~/assets/images/avatar.png" alt="avatar" width="40" height="40" style="border-radius: 50%">
          </span>


          {{ user.userName }}
         <span v-if="user.role && user.role.section">( {{user.role.section.title}} ) </span>
        </template>
          <MenuItem name="5-1" to="/admin/profile">
            <Icon type="ios-contact" />
            پروفایل
          </MenuItem>
          <MenuItem name="5-3" to="/admin/profile/todos">
            <Icon type="ios-notifications" />
            <Badge :count="user.unread_notify" class="notify-badge">
              لیست کارهای شما
            </Badge>
          </MenuItem>
          <MenuItem name="5-4" to="/admin/profile/activities">
            <Icon type="ios-paper" />
            لیست فعالیت ها
          </MenuItem>
          <MenuItem name="5-5" to="/logout">
            <Icon type="md-exit" />
            خروج
          </MenuItem>

      </Submenu>
    </Menu>
  </div>
</template>
<script>
import {mapState} from "vuex";

export default {
  data () {
    return {
      theme1: 'dark' ,
      menu : false
    }
  } ,
  computed : {
    ...mapState('auth' , ['user'])
  },
  methods : {
    showMenu() {
      this.menu = !this.menu
      this.$emit('sideMenu' , this.menu)
    }
  }
}
</script>

<style>
.ivu-select-dropdown {
    top: 35px !important;
}
</style>
