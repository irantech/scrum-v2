<template>
  <div>
    <adminIncludeCrumb name="وظایف چک لیست ها" />
    <adminTaskTimeList :taskTimeList="taskTimeList"
                   :sectionLoading="taskLoading"
                   :checklists="checklists" :sectionList="sectionList"/>
  </div>

</template>

<script>
import {mapState} from "vuex";

export default {
  name : 'tasks' ,
  layout : 'admin',
  middleware : 'taskListAccess' ,
  data() {
    return {
      taskLoading : false
    }
  },
  created() {
    this.taskLoading = true
    this.$store.dispatch('admin/taskTime/LoadAdminTasks' ).then(res => this.sectionLoading = false)
    this.$store.dispatch('admin/checklist/getChecklists' )
    this.$store.dispatch('admin/section/LoadAdminSections' )
  },
  computed :{
    ...mapState('admin/taskTime' , ['taskTimeList']),
    ...mapState('admin/checklist' , ['checklists']),
    ...mapState('admin/section' , ['sectionList'])
  }
}
</script>
