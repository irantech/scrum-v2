<template>
  <div>
    <div class="col-12 m-auto">
      <Card class="row mb-2">
      <Form  v-model="searchForm" class="row" ref="searchForm"
             @submit.native.prevent="search('searchForm')">
        <div class="col-12 col-md-4">
          <FormItem class="mb-2">
            <input type="text" placeholder="نام تسک" class="form-control" v-model="searchForm.title">
          </FormItem>
        </div>
        <div class="col-12 col-md-4">
          <FormItem class="mb-2">
            <Select v-model="searchForm.contract" filterable  placeholder="نام مشتری">
              <Option v-for="item in contractList" :value="item.id" :key="item.id">{{ item.customer ? item.customer.name : '' }}({{item.title}})</Option>
            </Select>
          </FormItem>
        </div>
        <div class="col-12 col-md-4">
          <FormItem class="mb-2">
            <client-only>
              <date-picker popover="bottom-right" auto-submit
                           format="jYYYY-jMM-jDD"
                           v-model="searchForm.start_date"
                           placeholder="تاریخ ایجاد- از" clearable />
            </client-only>
          </FormItem>
        </div>
        <div class="col-12 col-md-4">
          <FormItem class="mb-2">
            <client-only>
              <date-picker popover="bottom-right" format="jYYYY-jMM-jDD"  auto-submit
                           v-model="searchForm.end_date" placeholder=" تاریخ ایجاد - تا "
                           :min="searchForm.start_date"  clearable/>
            </client-only>
          </FormItem>
        </div>
        <div class="col-12 col-md-4">
          <FormItem class="mb-2">
            <client-only>
              <date-picker popover="bottom-right" auto-submit
                           format="jYYYY-jMM-jDD"
                           v-model="searchForm.start_delivery_date"
                           placeholder="تاریخ تحویل - از" clearable />
            </client-only>
          </FormItem>
        </div>
        <div class="col-12 col-md-4">
          <FormItem class="mb-2">
            <client-only>
              <date-picker popover="bottom-right" format="jYYYY-jMM-jDD"  auto-submit
                           v-model="searchForm.end_delivery_date" placeholder="تاریخ تحویل - تا:"
                           :min="searchForm.start_delivery_date"  clearable/>
            </client-only>
          </FormItem>
        </div>
        <div class="col-12 col-md-4">
          <FormItem class="mb-2">
                <Select search v-model="searchForm.has_delivery"  clearable placeholder="تایم تحویل">
                  <Option  value="1" >ندارد</Option>
                  <Option  value="2" >دارد</Option>
                </Select>
          </FormItem>
        </div>
        <div class="col-12 col-md-4">
          <FormItem class="mb-2">
            <Select search v-model="searchForm.status"  clearable placeholder="وضعیت تسک ها">
              <Option v-for="item in statusList" :value="item.title" :key="item.id"  >{{ item.value }}</Option>
            </Select>
          </FormItem>
        </div>
        <div class="col-12 col-md-4">
          <FormItem prop="section" class="mb-2">
            <Select v-model="searchForm.section_id" placeholder="انتخاب کنید">
              <Option v-for="(section , index) in sectionList"  :key="index" :value="section.id">{{ section.title }}</Option>
            </Select>
          </FormItem>
        </div>
        <div class="col-2">
          <Button type="success" :loading="searchLoading" html-type="submit">جستجو</Button>
        </div>
      </Form>
    </Card>
    </div>
    <div direction="vertical" type="flex" name="create" v-if="$store.getters['auth/can']('create-tasks')">
      <Button type="success" class="mb-2 p-2" long @click="createModal = true">افزودن تسک جدید</Button>
    </div>
    <Col span="24" class="px-2 my-1 d-flex justify-content-center" v-if="task_loading">
      <Spin size="large"> </Spin>
    </Col>
    <Modal v-model="createModal" title="افزودن تسک جدید" width="800" footer-hide>
      <Form :model="formCreate" label-position="top" :rules="ruleCreate" class="row" ref="formCreate">
        <FormItem label="عنوان" prop="title" class="col-12">
          <Input v-model="formCreate.title"></Input>
        </FormItem>


        <FormItem label="لینک طرح" class="col-6">
          <Input v-model="formCreate.theme_link"></Input>
        </FormItem>
        <FormItem label="لینک سایت" class="col-6">
          <Input v-model="formCreate.site_link"></Input>
        </FormItem>
        <FormItem label="قرارداد مشخص" prop="section_id" class="col-6">
          <Select v-model="formCreate.section_id" filterable label="مشتری">
            <Option v-for="item in contractList" :value="item.id" :key="item.id">{{ item.customer ? item.customer.name : '' }}({{item.title}}) {{item.contract_code}}</Option>
          </Select>
        </FormItem>
        <FormItem label="ارجاع به بخش" prop="section_id" class="col-6">
          <Select v-model="formCreate.section_id" filterable label="بخش">
            <Option v-for="item in limitedSectionList" :value="item.id" :key="item.id">{{ item.title }}</Option>
          </Select>
        </FormItem>
        <FormItem label="لیست لیبل ها" class="col-8">
          <Select v-model="formCreate.label_list" multiple label="لیبل">
            <Option v-for="item in taskLabelList" :value="item.id" :key="item.id">{{ item.title }}</Option>
          </Select>
        </FormItem>
        <FormItem label="چک زمان" class="col-4">
          <Checkbox v-model="formCreate.needChecking">این تسک نیاز به تایید تایم دارد </Checkbox>
        </FormItem>
        <FormItem label="توضیحات"  class="col-12">
          <Input v-model="formCreate.description" type="textarea" :autosize="{minRows: 2,maxRows: 5}" placeholder="Enter something..."></Input>
        </FormItem>
        <FormItem class="col-12">
          <Button type="primary" :loading="createLoading" @click="createNewTask('formCreate')">ارسال اطلاعات</Button>
        </FormItem>
      </Form>
    </Modal>
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
<script>
import {mapState} from "vuex";

export default {
  name : 'task-list' ,
  props : ['task_list' ,'sectionList' , 'contractList' , 'taskLabelList' , 'task_loading' , 'searchForm'] ,
  data () {
    return {
      detailModal : false,
      single_task_loading : false,
      createModal : false,
      searchLoading : false ,
      statusList : [
        {
          title : 'hold' ,
          value : 'در دست بررسی'
        },
        {
          title : 'running' ,
          value : 'در حال انجام'
        },
        {
          title : 'complete' ,
          value : 'آرشیو شده'
        },
        {
          title : 'cancel' ,
          value : 'کنسل شده'
        },
      ],
      formCreate: {
        title: '',
        description : '' ,
        theme_link  : '' ,
        site_link   : '' ,
        contract_id: '',
        label_list: '',
        section_id: '',
        needChecking : false
      },
      ruleCreate: {
        title: [
          { required: true, message: 'یک عنوان وارد کنید', trigger: 'change' }
        ],
        section_id: [
          { required: true, message: 'انتخاب بخش مورد نظر الزامی است', trigger: 'change' , type : 'integer'}
        ],
      },
      createLoading : false
    }
  },
  methods : {
    createNewTask(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.createLoading = true
          this.$store.dispatch('admin/task/createNewTask' , { form : this.formCreate })
            .then(response => {
                this.$Message.success(response.data.message);
                this.createModal = false
                this.createLoading = false
                this.handleReset(name)
              }
            )
            .catch(error => {
              console.log(error)
              this.createLoading = false
              this.$Message.error(error.response);
            })
        } else {
          this.$Message.error('ابتدا خطاهای خود را بررسی کنید.');
        }
      })
    },
    handleReset (name) {
      this.$refs[name].resetFields();
    } ,
    search() {
      this.$emit('setSearchData' , this.searchForm)
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
    today_date() {
      return  this.$moment().utc().format('YYYY-MM-DD')
    } ,
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
  },
}



</script>

<style >
.ivu-card-head {
  display: none;
}
.bg-warning-task{
  background: #FFFEC4;
}
.bg-dark-task{
  background: #F0ECE3;
}
.bg-secondary-text{
  background: #DBD0C0;
}
.bg-done-task{
  background: #aeebae;
}
.ivu-tabs .ivu-tabs-tabpane , .ivu-tabs-nav-scroll{
  direction: rtl !important;
}
.ivu-tabs-nav{
  float: right !important;
}
</style>
