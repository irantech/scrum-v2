<template>
    <div class="h-100">
      <div class="card_c h-100" :style="`background: ${todo.change_time_reason ? '#219feb4d' : ''};`">
        <div>
          <span :class="`label_c ${labelColor}` ">{{ labelText }}</span>
          <span class="label_c label_c_info">{{ taskType }}</span>
          <span class="label_c label_c_red" v-if="todo.status != 'done' && todo.type == 'Task' && todo.task.delivery_time_base == today_date">امروز انجام شود</span>
          <span class="label_c label_c_red" v-if="todo.status != 'done' && todo.type == 'Task' && todo.task.delivery_time_base < today_date">زمان این پروژه تموم شده</span>
          <br>
          <span class="label_c label_c_gold">تاریخ ایجاد: {{ todo.created_time }}</span>
          <span class="label_c label_c_gold" v-if="todo.type == 'Task' && todo.task.delivery_time">تاریخ تحویل: {{ todo.task.delivery_time }}</span>
          <span class="label_c label_c_gold" v-if="todo.ending_time">تاریخ پایان: {{ todo.ending_time}}</span>

        </div>
        <p class="title_c" v-if="todo.type == 'Task'" @click="showModal = true">{{ todo.title }}</p>
        <a class="title_c" v-else :href="task_link">{{ todo.title }}</a>
        <a v-if="todo.requests.length == 0 && todo.status == 'done'" class="btn_c" @click="openChangeTimeModal()">تغییر زمان</a>
        <div class="style_1">
          <div v-if="todo.status == 'in_progress' " class="col-md-6 col-12 d-flex justify-content-center align-items-center flex-column">
            <div class="progress_bar_circle" :style="`background: conic-gradient(${progressColor} ${todo.progress_percent}%, #ccc 0deg);`">
              <div class="progress_bar_circle_div">
                {{Math.trunc(todo.progress_percent)}}%
              </div>
            </div>
            <div class="en-direction bg-danger pulse text-light px-2 py-1 border-radius float-left mt-3" v-if="todo.progress_extend_time != ''">
              {{ '(' + extend_date + ')'}}
            </div>
          </div>
          <div v-else-if="todo.status == 'done'" class="col-md-6 col-12 d-flex justify-content-center align-items-center flex-column">
            <div v-if="todo.status == 'done'" class="en-directionpulse text-light px-2 py-1 border-radius float-left" :style="`background:${statusColor}`">
              {{ statusText }}

              {{ '(' + difference_date + ')'}}
            </div>
          </div>
<!--          <div v-else-if="todo.status == 'stop'" class="col-md-12 col-12 d-flex justify-content-center align-items-center flex-column">-->
<!--            <div  class="en-directionpulse px-2 py-1 border-radius float-left">-->
<!--              این پروژه {{customer_hold_time}}  در دست مشتری است-->
<!--            </div>-->
<!--          </div>-->
        </div>
      </div>
      <Modal v-model="changeModel" title="تغییر زمان انجام کار" width="950" footer-hide>
        <Form ref="formValidate" :model="formValidate" label-position="top" :rules="ruleValidate">
          <div class="row">
            <div class="col-6">
              <date-picker auto-submit
                           type="datetime"
                           format="YYYY-MM-DD HH:mm:ss"
                           display-format="jYYYY-jMM-jDD  HH:mm:ss"
                           v-model="formValidate.starting_time"
                           placeholder="تاریخ شروع" clearable />
            </div>
            <div class="col-6">
              <date-picker  auto-submit
                            type="datetime"
                            format="YYYY-MM-DD HH:mm:ss"
                            display-format="jYYYY-jMM-jDD  HH:mm:ss"
                            v-model="formValidate.ending_time"
                            placeholder="تاریخ پایان" clearable />
            </div>
            <div class="col-12 mt-3">
              <FormItem prop="change_time_reason" label="دلیل تغییر انجام کار">
                <Input  v-model="formValidate.change_time_reason" placeholder="دلیل تغییر انجام کار"></Input>
              </FormItem>
            </div>
            <div class="col-12">
              <FormItem class="float-left">
                <Button type="primary" :loading="processLoading"
                        @click="changeTime( 'formValidate')">ثبت اطلاعات</Button>
              </FormItem>
            </div>
          </div>
        </Form>
      </Modal>
      <Modal v-if="todo.type == 'Task'" v-model="showModal" title="جزییات تسک" width="1000" footer-hide>
        <admin-task-detail :task="todo.task" :usertodo="true"/>
      </Modal>
    </div>
</template>

<script>
export  default {
  name : 'todo-item',
  props : ['todo'] ,
  data(){
    return {
      showModal : false ,
      changeModel: false ,
      processLoading: false ,
      formValidate : {
        starting_time : '' ,
        ending_time : '' ,
        change_time_reason : '' ,

      },
      ruleValidate: {
        starting_time: [
          { required: true,pattern:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/ ,  message: 'زمان شروع کار  را وارد کنید', trigger: 'change' }
        ],
        ending_time: [
          { required: true,pattern:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/ ,  message: ' زمان پایان کار را وارد کنید', trigger: 'change' }
        ],
        change_time_reason: [
          { required: true ,  message: 'دلیل تغییر ساعت انجام کار را وارد کنید', trigger: 'change' }
        ],
      },
    }
  },
  computed :{
    today_date() {
      return this.$moment().utc().format('YYYY-MM-DD')
    },
    progressColor(){
      if (this.todo.progress_percent <= 20) {
        return '#51e898';
      } else if (this.todo.progress_percent <= 75) {
        return '#ffe478';
      } else if (this.todo.progress_percent <= 100) {
        return '#eb5a46';
      }
    },
    labelColor(){
      switch (this.todo.status) {
        case 'started' :
          return 'label_c_red' ;
        case 'in_progress' :
          return 'label_c_yellow' ;
        case 'done':
          return 'label_c_green';
        case 'stop':
          return 'label_c_red';
      }
    },
    labelText() {
      switch (this.todo.status) {
        case 'started' :
          return 'شروع نشده' ;
        case 'in_progress' :
          return 'در صف انجام' ;
        case 'done':
          return 'انجام شد';
        case 'stop':
          return 'متوقف شده'
      }
    },
    statusColor(){
      switch (this.todo.todo_status) {
        case 'best' :
          return '#56F000' ;
        case 'on-time' :
          return '#FCE83A' ;
        case 'bad':
          return '#FFB302';
        case 'worst':
          return '#FF3838' ;
        default :
          return "gray";
      }
    },
    statusText() {
      switch (this.todo.todo_status) {
        case 'best' :
          return 'خوش قول' ;
        case 'on-time' :
          return 'سرتایم' ;
        case 'bad':
          return 'بدقول';
        case 'worst':
          return 'افتضاح'
      }
    },
    taskType() {
      switch (this.todo.type) {
        case 'ChecklistContract' :
          return 'چک لیست' ;
        default :
          return 'تسک' ;
      }
    },
    task_link() {
      switch (this.todo.type){
        case 'ChecklistContract' :
          return `/admin/contract-checklist/${this.todo.task_id}` ;
        default :
          return 'تسک' ;
      }
    },
    difference_date(){
      if(this.todo.difference_time) {
        let last_date = '';
        let last_time = '';

        let date = this.todo.difference_time.split(" ");
        let time =  date[1].split(':')

        last_date = date[0] + ' روز ' ;
        if(time[0] > 0 )
          last_time =  time[0] + ' ساعت '

        return last_date + last_time
      }
    },
    extend_date() {
      let last_date = '';
      let last_time = '';

      let date = this.todo.progress_extend_time.split(" ");
      let time =  date[1].split(':');

      last_date = date[0] + ' روز ' ;
      if(time[0] > 0 )
        last_time =  time[0] + ' ساعت '

      return last_date + last_time

    },
    customer_hold_time() {
      let last_time = '';
      if(this.todo.customer_hold_time) {
        let date = this.todo.customer_hold_time.split(" ")
        let day = date[0] ;
        let hour = date[1] ;
        console.log(day , hour)
        if( day  != 0 )
          last_time =  day + ' روز '
        last_time = last_time + ' ' + hour + 'ساعت'
        return last_time;
      }


    }
  },
  methods : {
    startTask() {
      this.$store.dispatch('admin/todo/updateToDoList'  , {
        id : this.todo.id ,
        form : {
          status : 'in_progress' ,
          task_type : this.todo.task_class ,
          task_id : this.todo.task_id
        }
      })
    } ,
    openChangeTimeModal() {
      this.formValidate.starting_time = this.todo.starting_time_base
      this.formValidate.ending_time = this.todo.ending_time_base
      this.formValidate.change_time_reason = this.todo.change_time_reason
      this.changeModel  = true
    },
    changeTime() {
      this.processLoading = true
      this.$store.dispatch('admin/todo/changeToDoListTime'  , {
        id : this.todo.id ,
        form : this.formValidate
      }).then(response => {
        this.processLoading = false
        this.$Message.warning(response.data.message);
        this.changeModel = false
      })
        .catch(error => {
          this.processLoading = false
          this.$Message.error(error.message);
        })
    } ,


  }
}
</script>


<style>
.title_card{
  font-size: 16px;
}
.task-options{
  display: flex;
  flex-direction: column;
  gap: 10px;
  font-size: 13px;
}
.gap-10{
  gap: 10px;
}
.progress_bar_circle{
  width: 130px;
  height: 130px;
  padding: 5px;
  border: 2px solid #fff;
  box-shadow: 0 0 10px #eaeaea;
  border-radius: 100%;
}
.progress_bar_circle_div{
  width: 100%;
  height: 100%;
  background: #fff;
  border-radius: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  text-align: center;
}

.pulse {
  animation: pulse-animation 2s infinite;
}
@keyframes pulse-animation {
  0% {
    box-shadow: 0 0 0 0px #FF3838;
  }
  100% {
    box-shadow: 0 0 0 20px rgba(0, 0, 0, 0);
  }
}
.en-direction{
  direction: ltr;
}
</style>
