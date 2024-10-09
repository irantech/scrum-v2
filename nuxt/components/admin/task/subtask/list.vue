<template>
  <div>
      <Card class="border" v-if="task.sub_task.length > 0">
        <Tabs type="card" style="direction: ltr">
          <TabPane v-for="(user , index) in  task.sub_task" :label="user.name" :key="index" style="direction: rtl">
            <CheckboxGroup v-model="selected_item" @on-change="tellMeTheTruth()">
              <subTask v-for="(sub , index) in user.subTask" :task_id="task.id" :user="user" :usertodo="usertodo"
                     :key="index" :subTask="sub" :level="1"/>
            </CheckboxGroup>
          </TabPane>
        </Tabs>
      </Card>

      <CheckboxGroup v-model="selected_item" @on-change="tellMeTheTruth()">
        <subTask v-for="(sub , index) in task.not_assigned_sub_task" :task_id="task.id" :usertodo="usertodo"
                 :key="index" :subTask="sub" :level="1"/>
      </CheckboxGroup>
  </div>

</template>

<script>
import subTask from "./item";
export default {
  name : 'sub-task-list' ,
  props : ['task'  , 'usertodo'] ,
  components : {subTask},
  data() {
    return{
      selected_item : []
    }
  },
  methods : {
    tellMeTheTruth() {
      this.$store.commit('admin/task/SET_SELECTED_SUB_TASK_LIST' , this.selected_item)
    }
  },
}
</script>


