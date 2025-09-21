<template>
  <div>
    <adminIncludeCrumb name="تسک ها" />


    <adminTaskList :task_list="taskList" :iranTechTask="iranTechTask" :sectionList="sectionList"
                   :contractList="contractList" :userList="userList"
                   :searchForm="form" @setSearchData="setSearchData"
                   :taskLabelList="taskLabelList" :task_loading="task_loading"/>
  </div>

</template>

<script>
import {mapState} from "vuex";

export default {
  name : 'task' ,
  layout : 'admin',
  data() {
    return {
      task_loading : true ,
      form : {
        start_delivery_date : '' ,
        end_delivery_date : '',
        start_date : '' ,
        end_date : '' ,
        contract  : '' ,
        title : '' ,
        has_delivery : '' ,
        status : '',
        section_id : '',
        user_id : ''
      },
    }
  },
  created() {
    this.setFormData();
    this.getTaskData();
    this.$store.dispatch('admin/section/LoadAdminSections' )
    this.$store.dispatch('admin/task/LoadTaskLabelList' )
    this.$store.dispatch('admin/contract/LoadAdminContracts' )
    this.$store.dispatch('admin/user/getUserList' )
  },
  computed :{
    ...mapState('admin/task' , ['taskList']),
    ...mapState('admin/task' , ['iranTechTask']),
    ...mapState('admin/task' , ['iranTechError']),
    ...mapState('admin/section' , ['sectionList']),
    ...mapState('admin/task' , ['taskLabelList']),
    ...mapState('admin/contract' , ['contractList']),
    ...mapState('admin/user' , ['userList']) ,
  },
  methods:{
    getTaskData() {
      this.task_loading = true
      this.$store.dispatch('admin/task/LoadTaskList' , this.form).then(res => {
        console.log('res' , res)
        this.task_loading = false
      })
    },
    setFormData() {
      // this.form.start_date = this.$moment().subtract(15, 'days').format('jYYYY-jMM-jDD')
      // this.form.end_date = this.$moment().utc().format('jYYYY-jMM-jDD')
    },
    setSearchData(form_data){
      this.form = form_data
      this.getTaskData();
    },
  },
}
</script>
