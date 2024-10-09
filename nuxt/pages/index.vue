<template>
  <Tutorial/>
</template>

<script>
export default {
  name: 'home-page' ,
  middleware : 'auth',
  beforeRouteEnter(to, from, next ) {
    next('/admin')
  },
}
</script>
