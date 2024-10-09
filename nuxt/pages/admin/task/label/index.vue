<template>
  <div>
    <adminIncludeCrumb name="لیبل تسک ها" />
    <adminTaskLabelList :task_label_list="taskLabelList" :task_label_loading="task_label_loading"/>
  </div>

</template>

<script>
import {mapState} from "vuex";

export default {
  name : 'task-label' ,
  layout : 'admin',
  data() {
    return {
      task_label_loading : false
    }
  },
  created() {
    this.task_label_loading = true
    this.$store.dispatch('admin/task/LoadTaskLabelList' ).then(res => this.task_label_loading = false)
  },
  computed :{
    ...mapState('admin/task' , ['taskLabelList'])
  }
}
</script>
