<template>
  <div>
    <adminIncludeCrumb name="گزارش اس ام اس" />
    <admin-sms-template-log-list
      :smsLogList="smsLogList"
      :perPage="perPage"
      :totalItems="totalItems"
      @setPage="getNewData"
      @setPerPage="getNewPerPageDta"
      @setSearchData="setSearchData"/>
  </div>
</template>

<script>

export default {
  name : 'sms-logs' ,
  layout : 'admin',
  data(){
    return {
      smsLogList : []  ,
      currentPage : 1 ,
      perPage : 40 ,
      totalItems : ''
    }
  },
  methods : {
    getAllSmsLogs() {
      this.$axios.post(`sms-logs?page=${this.currentPage}` , {
          'per_page' : this.perPage
      }).then(response => {
          this.smsLogList =  response.data.data.data
          this.totalItems = response.data.data.meta.total
        })
        .catch(error => {
          console.log(error)
        })
    },
    getNewPerPageDta(perPage){
      this.perPage = perPage
      this.currentPage = 1
      this.getAllSmsLogs()
    },
    getNewData(page){
      this.currentPage = page
      this.getAllSmsLogs()
    },
    setSearchData(formData) {
      this.getAllSmsLogs()
    },
  },
  created(){
    this.getAllSmsLogs()
  }
}
</script>
