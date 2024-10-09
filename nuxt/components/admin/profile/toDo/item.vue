<template>
  <div class="h-100">
    <div class="card_c h-100">
          <div>
            <span :class="`label_c ${labelColor}` ">{{ labelText }}</span>
            <span class="label_c label_c_info">{{ taskType }}</span>
            <span class="label_c label_c_red" v-if="todo.status != 'done' && todo.type == 'Task' && todo.task.delivery_time_base == today_date">امروز انجام شود</span>
            <span class="label_c label_c_red" v-if="todo.status != 'done' && todo.type == 'Task' && todo.task.delivery_time_base < today_date">زمان این پروژه تموم شده</span>

            <br>

            <span class="label_c label_c_gold">تاریخ ایجاد: {{ todo.created_time }}</span>
            <span class="label_c label_c_green" v-if="todo.type == 'Task' && todo.task.delivery_time">تاریخ تحویل: {{ todo.task.delivery_time }}</span>
            <span class="label_c label_c_gold" v-if="todo.ending_time">تاریخ پایان: {{ todo.ending_time}}</span>

          </div>
          <p class="title_c" v-if="todo.type == 'Task'" @click="showSingleTask()">{{ todo.title }}</p>
          <a class="title_c" v-else-if="todo.type == 'ChecklistContract'" :href="task_link" >{{ todo.title }}</a>
          <p class="title_c" v-else @click="getTrainingSession()">{{ todo.title }}</p>

          <span v-if="todo.description">({{ todo.description }})</span>
          <button v-if="todo.requests.length == 0 && todo.status == 'done'" :disabled="disabledButton"   class="btn_c" @click="requestChangeTime()">درخواست تغییر تایم</button>
          <div class="style_1">
            <div v-if="todo.status == 'in_progress'" class="col-md-6 col-12 d-flex justify-content-center align-items-center flex-column">
              <div v-if="todo.progress_percent != ''" class="progress_bar_circle" :style="`background: conic-gradient(${progressColor} ${todo.progress_percent}%, #ccc 0deg);`">
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
<!--            <div v-else-if="todo.status == 'stop'" class="col-md-12 col-12 d-flex justify-content-center align-items-center flex-column">-->
<!--              <div  class="en-directionpulse px-2 py-1 border-radius float-left">-->
<!--                  این پروژه {{customer_hold_time}}  در دست مشتری است-->
<!--              </div>-->
<!--            </div>-->
          </div>
        </div>
        <Modal v-model="requestModal" title=" درخواست تغییر زمان " width="500" footer-hide>
          <Form ref="formValidate" :model="formValidate" :rules="ruleValidate" label-position="top"
                @submit.native.prevent="sendRequest('formValidate')">
            <p>درخواست شما برای مدیرتان ارسال میشود و  پس از بررسی ایشان و اعلام نتیجه در همین صفحه میتوانید پیگیری کنید.</p>
            <div class="row">
              <div class="col-12">
                <FormItem label="تاریخ شروع" prop="checklist_id">
                  <client-only>
                    <date-picker auto-submit
                                 format="YYYY-MM-DD HH:mm:ss" display-format="jYYYY-jMM-jDD HH:mm:ss"
                                 v-model="formValidate.starting_time" type="datetime"
                                 placeholder="تاریخ شروع انجام کار" clearable />
                  </client-only>
                </FormItem>
              </div>
              <div class="col-12">
                <FormItem label="تاریخ پایان" prop="section_id" disabled>
                  <client-only>
                    <date-picker auto-submit
                                 format="YYYY-MM-DD HH:mm:ss" display-format="jYYYY-jMM-jDD HH:mm:ss"
                                 v-model="formValidate.ending_time" type="datetime"
                                 placeholder="تاریخ پایان انجام کار" clearable />
                  </client-only>
                </FormItem>
              </div>
              <div class="col-12">
                <FormItem label="دلیل این درخواست" prop="reason">
                  <Input v-model="formValidate.reason" type="textarea" :autosize="{minRows: 2,maxRows: 5}" placeholder="توضیحات را وارد کنید..."></Input>
                </FormItem>
              </div>
              <div class="col-12">
                <FormItem>
                  <Button type="primary" :loading="requestLoading" html-type="submit">ویرایش اطلاعات</Button>
                </FormItem>
              </div>
            </div>

          </Form>
        </Modal>

        <Modal v-if="todo.type == 'TrainingSession'" v-model="showTrainingModal" title="جزییات جلسه" width="1000" footer-hide>
          <Button class="mb-1" v-if="todo.status != 'done'" type="success" @click="updateTask()">افزودن به انجام شده ها</Button>
          <table class="table table-hover table-bordered table-striped">
            <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">تاریخ</th>
              <th scope="col">ساعت</th>
              <th scope="col">برگزارکننده</th>
              <th scope="col">مشتری</th>
              <th scope="col">مدت زمان برگزاری</th>
              <th scope="col">آنلاین/حضوری</th>
              <th scope="col">محل برگزاری</th>
              <th scope="col">آدرس</th>
            </tr>
            </thead>
            <tbody>
            <tr>
              <th scope="row">{{index}}</th>
              <td>{{ todo.task.session_date }}</td>
              <td>{{ todo.task.session_time }}</td>
              <td>{{ todo.task.user.name }}</td>
              <td><nuxt-link :to="`/admin/contract-checklist/${todo.task.checklist_contract.id}`">{{ todo.task.checklist_contract.contract.customer.name }}</nuxt-link></td>
              <td>{{ todo.task.duration }}</td>
              <td :colspan="todo.task.location_status == 'online' ? 2 : 0">{{ todo.task.location_status == 'online' ? 'آنلاین' : 'حضوری'}}</td>
              <td v-if="todo.task.location_status != 'online'" >{{ todo.task.location_place != 'in' ? 'ایران تکنولوژی' : 'محل مشتری'}}</td>
              <td>{{ todo.task.address }}</td>
            </tr>
            </tbody>
          </table>
          <table class="table table-hover table-bordered table-striped">
            <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">نام نماینده </th>
              <th scope="col">شماره موبایل</th>
              <th scope="col">لینک اجتماعی اول </th>
              <th scope="col">لینک اجتماعی دوم</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(contributor , index) in activeContributors" :key="contributor.id">
              <th scope="row">{{ ++index}}</th>
              <td>{{ contributor.name }}</td>
              <td>{{ contributor.mobile }}</td>
              <td>{{ contributor.social_link1 }}</td>
              <td>{{ contributor.social_link1  }}</td>
            </tr>
            </tbody>
          </table>
        </Modal>
  </div>

</template>

<script>

  import {mapState} from "vuex";

  export  default {
      name : 'todo-item',
      props : ['todo'] ,
      data() {
        return {
          showTrainingModal : false ,
          disabledButton : false ,
          requestModal : false ,
          requestLoading : false,
          formRequest : '' ,
          formValidate: {
            starting_time: '',
            ending_time : '',
            reason :''
          },
          ruleValidate: {
            starting_time: [
              { required: true,  message: 'فیلد زمان شروع الزامی است.', trigger: 'change' }
            ],
            ending_time : [
              { required : true   ,message: 'فیلد زمان پایان الزامی است.', trigger: 'change' }
            ],
            reason : [
              { required : true , type : 'string' , message: 'فیلد دلیل این تغییر الزامی است.', trigger: 'change' }
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
              return 'شروع' ;
            case 'in_progress' :
              return 'در صف انجام' ;
            case 'done':
              return 'انجام شد';
            case 'stop':
              return 'متوقف شده';
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
              return '#FF3838'
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
            case 'Task' :
              return 'تسک' ;
            default :
              return 'جلسه آموزش' ;
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

        } ,
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
        showSingleTask() {
          this.$emit('getSingleTask' , this.todo.task.id)
        },
        getTrainingSession(){
          this.showTrainingModal = true
          this.activeContributors = JSON.parse(this.todo.task.contributors)
        },
        updateTask() {
            this.$store.dispatch('admin/todo/updateToDoList'  , {
              id : this.todo.id ,
              form : {
                  status : 'done' ,
                  task_type : this.todo.task_class ,
                  task_id : this.todo.task_id
              }
            })
        },
        requestChangeTime() {
          this.requestModal = true
          this.formValidate.starting_time = this.todo.starting_time_base
          this.formValidate.ending_time = this.todo.ending_time_base
        } ,
        sendRequest(name) {
          this.$refs[name].validate((valid) => {
            if (valid) {
              this.requestLoading = true
              let reason = {
                'reason': this.formValidate.reason,
                'starting_time': this.formValidate.starting_time,
                'ending_time': this.formValidate.ending_time
              }
              this.formRequest = {
                'reason': JSON.stringify(reason),
                'request_id': this.todo.id,
                'request_type': 'ToDoList'
              }
              this.$axios.post('staffRequest', this.formRequest)
                .then(response => {
                  this.$Message.success(response.data.message);
                  this.requestModal = false
                  this.disabledButton = true
                  this.handleReset(name)
                })
                .catch(error => {
                  this.$Message.error(error.response.data.message)
                })
                .finally(() => this.requestLoading = false)
            }else {
              this.$Message.error('ابتدا خطاهای خود را بررسی کنید.');
            }
          })
        },
        handleReset (name) {
          this.$refs[name].resetFields();
        },
      }
  }
</script>

