<template>

</template>

<script>
  export default {
    name : 'admin-logout',
    methods: {
      doLogout() {
        if (this.$cookies.get('token') == null) {
          this.$router.push({path: '/login'});
        } else {
          this.$axios.get('logout')
            .then(() => {
              this.$store.commit('auth/DELETE_LOGGED_OUT_UESR')
              this.$cookies.remove('token')
              this.$cookies.remove('role')
              this.$router.push({path: 'login'});
            })
            .catch(()=>{
              this.$cookies.remove('token')
              this.$cookies.remove('role')
              this.$router.push({path: 'login'});
            });
        }
      },
    },
    mounted() {
      this.doLogout();
    }
  }
</script>
