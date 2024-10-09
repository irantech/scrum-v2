<template>
  <Menu mode="horizontal" :theme="theme1" active-name="1">
    <MenuItem name="1" class="d-flex">
      <img class="img-fluid" src="~/assets/images/logo.svg" width="150px"></router-link>
    </MenuItem>
  </Menu>

</template>
<script>
export default {
  data () {
    return {
      theme1: 'dark'
    }
  }
}
</script>
