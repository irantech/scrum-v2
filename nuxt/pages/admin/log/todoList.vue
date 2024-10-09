<template>
  <div>
    <adminIncludeCrumb name="گزارش کارهای انجام شده" />
    <adminLogTodo :logList="logList"/>
  </div>
</template>

<script>

export default {
  name : 'sms-logs' ,
  layout : 'admin',
  data(){
    return {
      logList : []
    }
  },
  methods : {
    getAllLogs() {
      this.$axios.get(`todoListLog` , ).then(response => {
        this.logList =  response.data.data
      })
        .catch(error => {
          console.log(error)
        })
    },
  },
  created() {
    this.getAllLogs()
  }

}
</script>
