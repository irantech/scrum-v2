<template>
  <div>
    <adminIncludeCrumb name="وضعیت برگشت چک لیست ها" />
    <adminChecklistReverseList :checklist_reverse="checklist_reverse"
                               :tasks="tasks"
                               :data_get_loading="data_get_loading"
                               :form="form"
                               :sectionList="sectionList"
                               :userList="userList"
                               @setSearchData="setSearchData"/>
  </div>
</template>

<script>
import {mapState} from "vuex";

export default {
  name : 'checklist_reverse_list' ,
  layout : 'admin' ,
  data(){
    return  {
      checklist_reverse : [] ,
      tasks : [] ,
      data_get_loading : false ,
      form : {
        contract_title : '' ,
        section : '' ,
        start_date : '' ,
        end_date : '' ,
        type : ''
      },
    }
  },
  created() {
    this.$store.dispatch('admin/section/LoadAdminSections')
    this.$store.dispatch('admin/user/getUserList')

    this.setFormData()
    this.getReverseChecklistReverse();
  },
  methods : {
    getReverseChecklistReverse() {
      this.data_get_loading = true
      this.$axios.post('subTask' , this.form)
        .then(response => {
          this.checklist_reverse = response.data.data;
          // this.tasks = response.data.tasks;
        })
        .catch(error => reject(error)).finally(() => {
        this.data_get_loading = false
      })
    },
    setSearchData(form_data){
      this.form = form_data
      this.getReverseChecklistReverse();
    },
    setFormData() {
      this.form.start_date = this.$moment().subtract(1, 'months').format('jYYYY-jMM-jDD')
      this.form.end_date = this.$moment().utc().format('jYYYY-jMM-jDD')
    },
  },
  computed : {
    ...mapState('admin/section' , ['sectionList']) ,
    ...mapState('admin/user' , ['userList']) ,
  }
}
</script>
