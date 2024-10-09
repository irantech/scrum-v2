<template>
  <div>
    <div v-if="signedBy">
      <img  :src="signedBy.signature" :alt="signedBy.name" width="40" height="40">
      <br>
      <span>{{signedBy.name}}</span>
    </div>
  </div>
</template>

<script>

export default {
  props : ['section'],
  name : 'sign-section',
  computed : {
    signedBy() {
      return  this.$store.getters['admin/checklistContract/getSignedBySection'](this.section.id , 4)
    }
  }

}
</script>
