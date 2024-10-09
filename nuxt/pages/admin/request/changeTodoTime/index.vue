<template>
  <div>
    <adminIncludeCrumb name="لیست درخواست های شما برای تعییر زمان چک لیست ها"/>
    <adminRequestStaff :staff_request="staff_request" :loading="loading"/>
  </div>
</template>

<script>
import {mapState} from "vuex";

export default {
  name : 'todo' ,
  layout : 'admin',
  data() {
    return{
      loading : false  ,
      requested_status : [
        'done'
      ]
    }
  },
  created() {
    this.loading = true
    this.$store.dispatch('admin/request/getStaffRequestList' ).then(response=>{
      this.loading = false
    })
  },
  computed :{
    staff_request() {
      return this.$store.state.admin.request.staff_request_list;
    }
  }
}
</script>
