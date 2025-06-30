<script>
import {mapState} from "vuex";

export default {
  name: "singel",
  props : ['task_loading','task_list','contractList','sectionList','taskLabelList'] ,

  data() {
    return {
      detailModal : false,
      single_task_loading : false,

    }

  },
  methods:{
    limitedSectionList(){
      let list = [];
      if(this.sectionList && this.sectionList.length > 0) {
        this.sectionList.forEach(x => {
          if (x.order != '2') {
            list.push(x)
          }
        })
      }
      return list
    },
    getSingleTask(task_id){
      console.log(task_id)
      this.detailModal = true
      this.single_task_loading = true
      this.$store.dispatch('admin/task/LoadSingleTask' , {id : task_id} ).finally(() => {
        this.single_task_loading = false
      })
    },
  },

  computed : {
    ...mapState('admin/task' , ['singleTask']),
    archiveTasks() {
      return  this.task_list.filter(task => task.status == 'complete');
    },
    doneTasks() {
      return  this.task_list.filter(task => task.status == 'done');
    },

    notSetTaskDeliveryTime() {
      return this.task_list.filter(task => task.status != 'complete' && task.status != 'done' &&  !task.delivery_time);
    },
    pastUndoneTasks() {
      return this.task_list.filter(task => task.status != 'complete' && task.status != 'done' && this.today_date > task.delivery_time_base);
    },
    doingTasks() {
      return this.task_list.filter(task =>  task.status != 'complete' && task.status != 'done' && this.today_date <= task.delivery_time_base);
    },
  },
}
</script>

<template>
  <div>
    <div v-if="!task_loading">
      <Card class="w-100 my-2 bg-warning-task" v-if="notSetTaskDeliveryTime.length > 0">
        <p class="mb-1">تسک هایی که هنوز تایم داده نشده </p>
        <div class="row px-2" >
          <adminTaskItem v-for="(task , index) in notSetTaskDeliveryTime" :key="`${index}k`" @showModal="getSingleTask" :task="task"
                         :limitedSectionList="limitedSectionList"
                         :contractList="contractList" :sectionList="sectionList" :taskLabelList="taskLabelList"/>
        </div>
      </Card>
      <Card class="w-100 my-2 bg-dark-task" v-if="pastUndoneTasks.length > 0">
        <p class="mb-1">تسک هایی که تایم های آن گذشته است </p>
        <div class="row px-2" >
          <adminTaskItem v-for="(task , index) in pastUndoneTasks" :key="`${index}k`" :task="task"
                         :limitedSectionList="limitedSectionList"  @showModal="getSingleTask"
                         :contractList="contractList" :sectionList="sectionList" :taskLabelList="taskLabelList"/>
        </div>
      </Card>
      <Card class="w-100 my-2" v-if="doingTasks.length > 0">
        <p class="mb-1">تسک های در حال انجام</p>
        <div class="row px-2" >
          <adminTaskItem v-for="(task , index) in doingTasks" :key="`${index}k`" :task="task"
                         :limitedSectionList="limitedSectionList"  @showModal="getSingleTask"
                         :contractList="contractList" :sectionList="sectionList" :taskLabelList="taskLabelList"/>
        </div>
      </Card>
      <Card class="w-100 my-2 bg-done-task" v-if="doneTasks.length > 0">
        <p class="mb-1">تسک های انجام شده</p>
        <div class="row px-2" >
          <adminTaskItem v-for="(task , index) in doneTasks" :key="`${index}k`" :task="task"
                         :limitedSectionList="limitedSectionList"  @showModal="getSingleTask"
                         :contractList="contractList" :sectionList="sectionList" :taskLabelList="taskLabelList"/>
        </div>
      </Card>
      <Card class="w-100 my-2 bg-secondary-text" v-if="archiveTasks.length > 0">
        <p class="mb-1">تسک های آرشیو شده</p>
        <div class="row px-2" >
          <adminTaskItem v-for="(task , index) in archiveTasks" :key="`${index}k`" :task="task"
                         :limitedSectionList="limitedSectionList"  @showModal="getSingleTask"
                         :contractList="contractList" :sectionList="sectionList" :taskLabelList="taskLabelList"/>
        </div>
      </Card>
      <!--      <Tabs type="card" style="direction: ltr">-->

      <!--        <TabPane label="همه" >-->
      <!--                <Card class="w-100 my-2 bg-warning-task" v-if="notSetTaskDeliveryTime.length > 0">-->
      <!--                  <p class="mb-1">تسک هایی که هنوز تایم داده نشده </p>-->
      <!--                  <div class="row px-2" >-->
      <!--                    <adminTaskItem v-for="(task , index) in notSetTaskDeliveryTime" :key="`${index}k`" @showModal="getSingleTask" :task="task"-->
      <!--                                   :limitedSectionList="limitedSectionList"-->
      <!--                                   :contractList="contractList" :sectionList="sectionList" :taskLabelList="taskLabelList"/>-->
      <!--                  </div>-->
      <!--                </Card>-->
      <!--                <Card class="w-100 my-2 bg-dark-task" v-if="pastUndoneTasks.length > 0">-->
      <!--                  <p class="mb-1">تسک هایی که تایم های آن گذشته است </p>-->
      <!--                  <div class="row px-2" >-->
      <!--                  <adminTaskItem v-for="(task , index) in pastUndoneTasks" :key="`${index}k`" :task="task"-->
      <!--                                 :limitedSectionList="limitedSectionList"  @showModal="getSingleTask"-->
      <!--                                 :contractList="contractList" :sectionList="sectionList" :taskLabelList="taskLabelList"/>-->
      <!--                  </div>-->
      <!--                </Card>-->
      <!--                <Card class="w-100 my-2" v-if="doingTasks.length > 0">-->
      <!--                  <p class="mb-1">تسک های در حال انجام</p>-->
      <!--                  <div class="row px-2" >-->
      <!--                    <adminTaskItem v-for="(task , index) in doingTasks" :key="`${index}k`" :task="task"-->
      <!--                                 :limitedSectionList="limitedSectionList"  @showModal="getSingleTask"-->
      <!--                                 :contractList="contractList" :sectionList="sectionList" :taskLabelList="taskLabelList"/>-->
      <!--                  </div>-->
      <!--                </Card>-->
      <!--                <Card class="w-100 my-2 bg-done-task" v-if="doneTasks.length > 0">-->
      <!--                  <p class="mb-1">تسک های در حال انجام</p>-->
      <!--                  <div class="row px-2" >-->
      <!--                    <adminTaskItem v-for="(task , index) in doneTasks" :key="`${index}k`" :task="task"-->
      <!--                                   :limitedSectionList="limitedSectionList"  @showModal="getSingleTask"-->
      <!--                                   :contractList="contractList" :sectionList="sectionList" :taskLabelList="taskLabelList"/>-->
      <!--                  </div>-->
      <!--                </Card>-->
      <!--                <Card class="w-100 my-2 bg-secondary-text" v-if="archiveTasks.length > 0">-->
      <!--                  <p class="mb-1">تسک های آرشیو شده</p>-->
      <!--                  <div class="row px-2" >-->
      <!--                    <adminTaskItem v-for="(task , index) in archiveTasks" :key="`${index}k`" :task="task"-->
      <!--                                 :limitedSectionList="limitedSectionList"-->
      <!--                                 :contractList="contractList" :sectionList="sectionList" :taskLabelList="taskLabelList"/>-->
      <!--                  </div>-->
      <!--                </Card>-->
      <!--        </TabPane>-->
      <!--        <TabPane label="هنوز تایم داده نشده" v-if="notSetTaskDeliveryTime.length > 0">-->
      <!--          <div class="row px-2" >-->
      <!--            <adminTaskItem v-for="(task , index) in notSetTaskDeliveryTime" :key="`${index}k`" @showModal="getSingleTask" :task="task"-->
      <!--                           :limitedSectionList="limitedSectionList"-->
      <!--                           :contractList="contractList" :sectionList="sectionList" :taskLabelList="taskLabelList"/>-->
      <!--          </div>-->
      <!--        </TabPane>-->
      <!--        <TabPane label="تایم های گذشته" v-if="pastUndoneTasks.length > 0">-->
      <!--          <div class="row px-2" >-->
      <!--            <adminTaskItem v-for="(task , index) in pastUndoneTasks" :key="`${index}k`" :task="task"-->
      <!--                           :limitedSectionList="limitedSectionList"  @showModal="getSingleTask"-->
      <!--                           :contractList="contractList" :sectionList="sectionList" :taskLabelList="taskLabelList"/>-->
      <!--          </div>-->
      <!--        </TabPane>-->
      <!--        <TabPane label="در حال انجام"  v-if="doingTasks.length > 0">-->
      <!--          <div class="row px-2" >-->
      <!--            <adminTaskItem v-for="(task , index) in doingTasks" :key="`${index}k`" :task="task"-->
      <!--                           :limitedSectionList="limitedSectionList"  @showModal="getSingleTask"-->
      <!--                           :contractList="contractList" :sectionList="sectionList" :taskLabelList="taskLabelList"/>-->
      <!--          </div>-->
      <!--        </TabPane>-->
      <!--        <TabPane label="انجام شده"  v-if="doneTasks.length > 0">-->
      <!--          <div class="row px-2" >-->
      <!--            <adminTaskItem v-for="(task , index) in doneTasks" :key="`${index}k`" :task="task"-->
      <!--                           :limitedSectionList="limitedSectionList"  @showModal="getSingleTask"-->
      <!--                           :contractList="contractList" :sectionList="sectionList" :taskLabelList="taskLabelList"/>-->
      <!--          </div>-->
      <!--        </TabPane>-->
      <!--        <TabPane label="آرشیو شده"  v-if="archiveTasks.length > 0">-->
      <!--          <div class="row px-2" >-->
      <!--            <adminTaskItem v-for="(task , index) in archiveTasks" :key="`${index}k`" :task="task"-->
      <!--                           :limitedSectionList="limitedSectionList"-->
      <!--                           :contractList="contractList" :sectionList="sectionList" :taskLabelList="taskLabelList"/>-->
      <!--          </div>-->
      <!--        </TabPane>-->
      <!--      </Tabs>-->

    </div>
    <Modal v-model="detailModal" title="جزییات تسک" width="1000" footer-hide>
      <Col span="24" class="px-2 my-1 d-flex justify-content-center" v-if="single_task_loading">
        <Spin size="large"> </Spin>
      </Col>
      <admin-task-detail v-if="!single_task_loading && singleTask" :task="singleTask"/>
    </Modal>
  </div>

</template>

<style scoped>

</style>
