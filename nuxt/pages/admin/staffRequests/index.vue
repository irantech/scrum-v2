<template>
  <div>
    <adminIncludeCrumb name="درخواست های ارسال شده به شما "></adminIncludeCrumb>
    <adminRequestManager :request_list="admin_request_list" @setSearchData="setSearchData"
                         :form="form"  :loading="loading" :sectionList="sectionList"/>
  </div>
</template>

<script>
import {mapState} from "vuex";

export default  {
  name: 'admin_manager_request',
  layout : 'admin' ,
  data () {
    return {
      loading : false ,
      form : {
        section : '' ,
        has_confirmed : '' ,
        start_time : '' ,
        end_time : ''
      },
    }
  },
  created() {
    this.$store.dispatch('admin/section/LoadAdminSections')
    this.setFormData()
    this.getManagerRequestList();
  },
  methods : {
    setFormData() {
      this.form.start_time = this.$moment().subtract(1, 'months').format('jYYYY-jMM-jDD')
      this.form.end_time = this.$moment().utc().format('jYYYY-jMM-jDD')
    },
    getManagerRequestList() {
      this.loading = true
      this.$store.dispatch('admin/request/getAdminRequestList' , this.form).then(response=>{
        this.loading = false
      })
    },
    setSearchData(formData) {
      this.form = formData
      this.getManagerRequestList()
    }
  },
  computed :{
    ...mapState('admin/section' , ['sectionList']) ,
    ...mapState('admin/request' , ['admin_request_list']),
  }

}
</script>
