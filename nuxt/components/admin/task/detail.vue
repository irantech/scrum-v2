<template>
  <div>
    <Card>
      <Tooltip content="زمان تحویل پروژه" placement="top" v-if="$store.getters['auth/can']('set-task-times') && !usertodo">
        <Icon size="20" type="ios-time" @click="deliveryTimeModal = true"/>
      </Tooltip>
      <Tooltip content="زمان پروژه" placement="top" v-if="$store.getters['auth/can']('set-task-times')  && !usertodo">
        <Icon size="20" type="ios-timer" @click="setTimeModal = true"/>
      </Tooltip>
      <Tooltip content="آرشیو" placement="top" v-if="!usertodo">
        <Icon size="20" type="md-archive" @click="deleteModal = true"/>
      </Tooltip>
      <Tooltip content="ارجاع به بخش" placement="top" v-if="!usertodo">
        <Icon size="20" type="ios-return-left" @click="changeSectionModal = true"/>
      </Tooltip>
      <div>
        <div class="d-flex justify-content-between my-1">
          <div>
            <Tag v-for="(label , index) in task.label_list" :key="`${index}k`"  :color="`${label.color}`" @click="labelModal = true">{{label.title}}</Tag>
            <Tooltip content="تغییر لیبل ها" placement="top">
              <Icon size="20" type="md-clipboard" v-if="!usertodo" @click="labelModal = true"/>
            </Tooltip>
          </div>
          <div class="text-left">
            <Tooltip content="زمان ایجاد">
              <Tag color="cyan">{{ task.created_at }}</Tag>
            </Tooltip>
            <Tooltip v-if="task.delivery_time" content="زمان تحویل" >
              <Tag color="green">{{ task.delivery_time }}</Tag>
            </Tooltip>
            <Tooltip v-if="task.total_task_day || task.total_task_time" content="مدت زمان انجام" >
                <Button @click="timeModal = true" type="warning" ghost style="height: 22px"><span v-if="task.total_task_day != 0 ">{{ task.total_task_day }} روز</span>
                  <span v-if="task.total_task_time != 0">{{ task.total_task_time }}</span> </Button>
              </Tooltip>
            <Tag color="purple">{{ taskStatus }}</Tag>

          </div>
        </div>
        <h3 class="font-weight-bold"> {{ task.title }}</h3>
        <div class="d-flex my-1">
          <div>
            <Tooltip v-if="task.user_owner"  :content="task.user_owner.userName">
              <Avatar :src="`${$env.UPLOAD_URL}/${task.user_owner.avatar}`"  />
            </Tooltip>
            <Tooltip v-for="(user , index) in task.user_list" :key="`${index}m`"  v-if="task.user_owner && user.userName != task.user_owner.userName" :content="user.userName">
              <Avatar  :src="`${$env.UPLOAD_URL}/${user.avatar}`"  />
            </Tooltip>
          </div>
        </div>
         <div class="row">
            <div class="col-3" v-if="task.site_link">
                <span>لینک سایت : </span>
                <a :href="`${task.site_link}`">
                  {{task.site_link}}
                   <Icon type="ios-link" />
                </a>
            </div>
            <div class="col-3" v-if="task.theme_link">
              <span>لینک طرح : </span>
              <a :href="`${task.theme_link}`">
                {{task.site_link}}
                <Icon type="ios-link" />
              </a>
              </div>
            <div class="col-3" v-if="task.contract" >
              <span>لینک قرارداد : </span>
              <nuxt-link :to="`/admin/contract/${task.contract.id}/view`">
                <Icon type="md-link" />
              </nuxt-link>
            </div>
          </div>

         <div class="my-2" v-if="task.description">
           <h3 class="font-weight-bold">
             <Icon type="md-list" />
              توضیحات
           </h3>
           <p class="mt-2">
             {{task.description}}
           </p>
         </div>
         <div class="mt-3" v-if="task.last_todo_list && task.last_todo_list.description">
           <p class="font-weight-bold">
               توضیحات  {{task.last_todo_list.user.name}} برای شما:
             <span class="mt-2">
               {{task.last_todo_list.description}}
             </span>
           </p>


         </div>
      </div>
    </Card>
    <Modal v-model="deliveryTimeModal" width="1000" footer-hide   title="تایم تحویل این تسک را مشخص کنید">
      <admin-task-delivery :task="task" @closeDeliveryModel="deliveryTimeModal = false" />
    </Modal>
    <Modal v-model="labelModal" width="360" footer-hide   title="لیبل را تغییر دهید">
      <adminTaskLabelChange :task="task" @closeLabelModal="labelModal = false"/>
    </Modal>
    <Modal v-model="setTimeModal" width="500" footer-hide>
      <admin-task-duration :task="task" @closeSetTimeModel="setTimeModal = false"/>
    </Modal>
    <Modal v-model="showAssignModal" width="500" footer-hide   title="فرد مورد نظر رو برای این موارد اختصاص دهید">
      <admin-task-assign :task="task" @closeAssignModel="showAssignModal = false"/>
    </Modal>
    <admin-task-subtask-list class="mt-3" v-if="task.not_assigned_sub_task && task.not_assigned_sub_task.length != 0 || task.sub_task.length != 0 " :task="task"  :usertodo="usertodo" />
    <Button class="mt-3" type="info" @click="showForm = true"  v-if="$store.getters['auth/can']('create-tasks') && !usertodo">افزودن مورد جدید</Button>
    <Button class="mt-3"  type="success" @click="showAssignModal = true" v-if="$store.getters['auth/can']('assign_tasks') && ((task.not_assigned_sub_task && task.not_assigned_sub_task.length != 0 )|| task.sub_task.length != 0 ) && !usertodo">اختصاص دادن به کارمند</Button>
    <Form v-if="showForm" ref="formInline" :model="formInline" :rules="ruleInline" class="border parent-padding-box col-12">
      <div class="row align-items-end parent-cols">
        <div class="padding-box col-lg-2">
          <FormItem prop="status" class="mb-0">
            <Select placeholder="وضعیت برکشت"  v-model="formInline.status">
              <Option v-for="item in status_list" :value="item.id" :key="item.id">{{ item.title }}</Option>
            </Select>
          </FormItem>
        </div>
        <div class="padding-box col-lg-9">
          <ScrumInput ref="scrumInput" :setRef="`task-${task.id}`" :multiple="multiple"
                      @bodyData="setBodyData"
                      @setFileCount="fileCount" @submitForm="createSubTask('formInline')"
                      @update="updateChecklistReverse"></ScrumInput>
        </div>
        <div class="padding-box col-lg-1 parent-btn2">
          <Button type="primary" :loading="reverse_loading"
                  @click="createSubTask('formInline')">ثبت</Button>
        </div>
      </div>
    </Form>
    <Modal v-model="changeSectionModal" width="360" footer-hide   title="به بخش مورد نظر ارجاع دهید">
      <admin-task-section :task="task" @closeSectionModel="changeSectionModal = false" @closeDetailModel="closeDetailModal()"/>
    </Modal>
    <Modal v-model="timeModal" width="360" footer-hide   title="جزییات تایم های هر بخش">
        <div v-for="(time , index) in task.task_time" :key="index" class="border p-2">
          <p>بخش : {{time.section.title}}</p>
          <div class="my-1">
            <span>زمان :</span>
            <span v-if="time.task_day_duration != 0 ">{{time.task_day_duration}} روز</span>
            <span v-if=" time.task_time_duration != 0 "  class="mx-2">{{time.task_time_duration}}</span>
          </div>
          <div class="my-1">
            <span>زمان تلرانس :</span>
            <span v-if="time.interval_day_duration != 0 ">{{time.interval_day_duration}} روز</span>
            <span v-if="time.interval_time_duration != 0" class="mx-2">{{time.interval_time_duration}}</span>
          </div>
        </div>
    </Modal>
    <Modal v-model="deleteModal" width="360">
      <p slot="header" style="color:#f60;text-align:center">
        <Icon type="ios-information-circle"></Icon>
        <span>آرشیو تسک انتخاب شده</span>
      </p>
      <div style="text-align:center">
        <p>آیا از آرشیو این تسک اطمینان دارید؟</p>
      </div>
      <div slot="footer">
        <Button type="error" size="large" long :loading="deleteLoading" @click="removeTask()">آرشیو</Button>
      </div>
    </Modal>
  </div>
</template>

<script>
import ScrumInput from "../../tools/scrumInput.vue";
export default {
  name : 'task-detail' ,
  components : {ScrumInput},
  props : ['task' , 'usertodo'] ,
  data() {
    return{
      deleteLoading : false ,
      timeModal : false,
      deleteModal : false,
      labelModal : false ,
      changeSectionModal : false ,
      showAssignModal : false,

      showForm : false ,
      multiple : true ,
      deliveryTimeModal : false ,
      status_list : [
        {
          id    : 'offer' ,
          title : 'پیشنهاد'
        } ,
        {
          id    : 'error' ,
          title : 'ایراد'
        } ,
        {
          id : 'periodical',
          title: 'ادواری'
        }
      ],
      formInline : {
        status  : '' ,
        body : ''
      } ,
      section_list : [
        {
          id : '3',
          title  : 'برنامه نویس'
        },
        {
          id : '2',
          title  : 'گرافیک'
        }
      ],
      ruleInline : {
        status: [
          { required: true , type : 'string', message: 'انتخاب وضعیت مهم است.', trigger: 'blur' }
        ],
        body: [
          { required: true , type : 'string', message: 'وارد کردن متن اجباری است.', trigger: 'blur' }
        ],
      },

      reverse_loading : false,
      file_count : 0,
      counter : 0 ,
      setTimeModal : false ,


    }
  },
  computed : {
    taskStatus(){
      switch (this.task.status) {
        case 'hold' :
          return 'در حال بررسی' ;
        case 'running' :
          return 'در حال انجام' ;
        case 'complete' :
          return 'آرشیو شده' ;
        case 'done' :
          return 'انجام شده' ;
      }
    },
    today_date() {
      return this.$moment().utc().format('YYYY-MM-DD')
    },

  },
  methods :{
    removeTask() {
      this.deleteLoading = true
      this.$store.dispatch('admin/task/removeTask' , { id : this.task.id})
        .then(response => {
          this.$Message.success(response.data.message)
          this.deleteLoading = false
          this.deleteModal = false
        })
        .catch(error => {
          this.deleteLoading  = false
          this.$Message.error(error.response.message)
        })
    } ,
    closeDetailModal() {
      this.$emit('closeDetailModel')
    },
    fileCount(value) {
      this.file_count = value
    },
    setBodyData(value) {
      this.formInline.body = value
    },
    createSubTask(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.reverse_loading = true
          this.$axios.post( `subTask/create` , {
            body    : this.formInline.body ,
            status  : this.formInline.status,
            task    : this.task.id
          }).then(res=>{
            if(this.file_count === 0 ){
              this.$store.commit('admin/task/ADD_TASK_SUBTASK' , {
                newSubTask : res.data.data , taskId : this.task.id
              })

              this.handleReset(name);
            }
            else {
              this.checklist_reverse_id = res.data.data.id
              this.$refs.scrumInput.setValue({
                checklist_reverse_id  : this.task.id ,
                active : true
              });
            }
          })
        }
        else {
          this.$Message.error('ابتدا خطاهای خود را بررسی کنید.');
        }
      })
    },
    updateChecklistReverse(file_path) {
      this.$axios.put(`subTask/${this.checklist_reverse_id}` , {
        file : file_path
      }).then(res => {
        this.counter = this.counter + 1
        if(this.counter === this.file_count){
          this.$store.commit('admin/task/ADD_TASK_SUBTASK' , {
            newSubTask : res.data.data ,
            taskId : this.task.id})
          this.handleReset('formInline')
        }
      })
    },
    handleReset (name) {
      this.reverse_loading = false
      this.counter = 0
      this.$refs[name].resetFields();
      this.$refs.scrumInput.resetData()
      this.$store.commit('admin/checklistContract/SET_ACTIVE_REVERSE' , 'notAny')
    },

  } ,
  created() {
    console.log('hello')
  }

}
</script>


<style>

@media (max-width: 576px) {
  .padding-box{
    padding: 0;
  }

  .parent-cols{
    padding: .5rem !important;
    border-radius: 8px;
    gap: 10px;
  }
  .parent-btn2{
    display: flex;
    align-items: center;
    justify-content: left;
  }
  .ivu-radio-wrapper{
    margin-bottom: 0;
  }
  .parent-padding-box{
    margin-top: 10px;
    border-radius: 8px;
  }
  .reply-option{
    flex-direction: column;
  }
  .parent-box-name-data>span{
    font-size: 10px;
  }
}


</style>
