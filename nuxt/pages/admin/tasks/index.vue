<template>
  <all-task :task_all="allTask" />
</template>


<script>
import allTask from "@/components/admin/tasks/allTask.vue";
import {mapState} from "vuex";
export default {
  name: "tasks",
  components: {allTask},
  data() {
    return {
      form: {

      }
    }
  },
  computed :{
    ...mapState('admin/task' , ['allTask']),
  },
  created() {
    this.getTaskData()
  },
  methods: {
    getTaskData() {
      this.task_loading = true
      this.$store.dispatch('admin/task/AllTask' , this.form).then(res => {
        console.log('res' , res)
        this.task_loading = false
      })
    },
  }
}
</script>

<style scoped>

</style>
