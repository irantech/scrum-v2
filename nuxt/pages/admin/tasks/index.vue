<template>
  <div>

    <all-task :task_all="allTask.data_contract" :not_assign_tasks="allTask.data_tasks_not_assign" :searchForm="form" @setSearchData="setSearchData" />
  </div>
</template>


<script>

// start_date : 'this.$moment().subtract(90, 'days').format('jYYYY-jMM-jDD')' ,
//   end_date : this.$moment().utc().format('jYYYY-jMM-jDD') ,

import allTask from "@/components/admin/tasks/allTask.vue";
import {mapState} from "vuex";
export default {
  name: "tasks",
  layout : 'admin',
  components: {allTask},
  data() {
    return {
      form : {
        referrer : '' ,
        receiver_delivery : '',
        start_date : '' ,
        end_date : '' ,
        flag: 'given_time'
      },
    }
  },
  computed :{
    ...mapState('admin/task' , ['allTask']),

  },
  created() {
    this.getTaskData()
    this.$store.dispatch('admin/user/getUserList' )
  },
  methods: {
    getTaskData() {
      this.task_loading = true
      this.$store.dispatch('admin/task/AllTask' , this.form).then(res => {
        console.log('res' , res)
        this.task_loading = false
      })
    },
    setSearchData(form_data){
      this.form = form_data
      this.getTaskData();
    },
  }
}
</script>

<style scoped>

</style>
