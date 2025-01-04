<template>
  <all-task :task_all="allTask" :searchForm="form" @setSearchData="setSearchData" />
</template>


<script>
import allTask from "@/components/admin/tasks/allTask.vue";
import {mapState} from "vuex";
export default {
  name: "tasks",
  components: {allTask},
  data() {
    return {
      form : {
        referrer : '' ,
        receiver_delivery : '',
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
