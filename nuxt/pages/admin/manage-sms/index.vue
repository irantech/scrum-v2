<template>
   <div>
     <adminIncludeCrumb name="مدیریت اس ام اس ها"/>
     <adminSmsTemplateList :smsTemplates="smsTemplates" :loading="loading"/>
   </div>
</template>

<script>

  import {mapState} from "vuex";

  export default {
    name : 'sms-templates',
    layout : 'admin',
    data(){
      return {
          loading : false
      }
    },
    created() {
      this.loading  = true
      this.$store.dispatch('admin/smsTemplate/LoadSmsTemplates')
          .then(res => this.loading = false)
    },
    computed : {
      ...mapState('admin/smsTemplate' , ['smsTemplates'])
    }
  }
</script>
