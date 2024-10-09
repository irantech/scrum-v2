<template>
  <List>
    <admin-contract-training-session-item
      v-for="trainingSession in trainingSessionList"
      :key="trainingSession.id"
      :trainingSession="trainingSession"
      :checklistContract="checklistContract"
      @openNewModal="newModal"
    />
  </List>
</template>
<script>
  export default  {
    name : 'training-session-list' ,
    props : ['trainingSessionList' , 'checklistContract'] ,
    methods : {
      newModal() {
        this.$emit('openNewSessionModal')
      }
    }
  }
</script>
