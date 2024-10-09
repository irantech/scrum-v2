<template>
  <div>
    <adminIncludeCrumb name="جلسات آموزش" />
     <admin-training-session-list :trainingSessionList="trainingSessionList" />
  </div>
</template>

<script>

export default {
  name : 'training-session' ,
  layout : 'admin',
  data(){
    return {
      trainingSessionList : []
    }
  },
  methods : {
    getAllTrainingSession() {
      this.$axios.get(`trainingSession/all`)
        .then(response => {
          this.trainingSessionList =  response.data.data
        })
        .catch(error => {
          console.log(error)
        })
    }
  },
  created(){
    this.getAllTrainingSession()
  }
}
</script>
