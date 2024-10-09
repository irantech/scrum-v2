<template>
  <div class="col-12">
    <div class="row">
      <div class="col-12 d-flex flex-column justify-content-center">
        <p class="mb-3">فیلتر وضعیت کارهای شما</p>
        <Card class="mb-2">
          <Form  v-model="form" class="row" ref="searchForm"
                 @submit.native.prevent="search('searchForm')">
              <div class="col-4">
                <Form>
                  <FormItem class="mb-2">
                    <input type="text" placeholder="نام پروژه" class="form-control" v-model="form.title">
                  </FormItem>
                </Form>
              </div>
              <div class="col-4">
                <Form>
                  <FormItem class="mb-2">
                    <Select search v-model="form.type"  clearable placeholder="نوع تسک">
                      <Option v-for="item in typeList" :value="item.name" :key="item.name">{{ item.title }}</Option>
                    </Select>
                  </FormItem>
                </Form>
              </div>
              <div class="col-4">
                <Form>
                  <FormItem class="mb-2">
                    <Select search v-model="form.status" multiple  clearable placeholder="وضعیت کارها">
                      <Option v-for="item in statusList" :value="item.name" :key="item.name">{{ item.title }}</Option>
                    </Select>
                  </FormItem>
                </Form>
              </div>
              <div class="col-4">
              <Form>
                <FormItem class="mb-2">
                  <Select search v-model="form.has_delivery"  clearable placeholder="تایم تحویل">
                    <Option  value="1" >ندارد</Option>
                    <Option  value="2" >دارد</Option>
                  </Select>
                </FormItem>
              </Form>
            </div>
              <div class="col-4">
                <client-only>
                  <date-picker popover="bottom-right" :max="todayDate"  auto-submit
                               format="jYYYY-jMM-jDD"
                               v-model="form.start_time"
                               placeholder="کارها از تاریخ " clearable />
                </client-only>
              </div>
              <div class="col-4">
                <client-only>
                  <date-picker popover="bottom-right" format="jYYYY-jMM-jDD"  auto-submit
                               v-model="form.end_time" placeholder="تا تاریخ"
                               :min="form.start_time"  clearable/>
                </client-only>
              </div>
              <div class="col-4">
                <client-only>
                  <date-picker popover="bottom-right" format="jYYYY-jMM-jDD"  auto-submit
                               v-model="form.start_delivery_time" placeholder="زمان تحویل"
                               clearable/>
                </client-only>
              </div>
              <div class="col-4">
              <client-only>
                <date-picker popover="bottom-right" format="jYYYY-jMM-jDD"  auto-submit
                             v-model="form.end_delivery_time" placeholder="زمان تحویل"
                             :min="form.start_delivery_time"  clearable/>
              </client-only>
            </div>
              <div class="col-4">
                <Form>
                  <FormItem class="mb-2">
                    <Select search v-model="form.done_task_status"  clearable placeholder="وضعیت انجام کارها">
                      <Option v-for="item in taskStatusList" :value="item.name" :key="item.name">{{ item.title }}</Option>
                    </Select>
                  </FormItem>
                </Form>
              </div>

              <div class="col-2">
                <Button type="success"  html-type="submit">جستجو</Button>
              </div>
          </Form>
      </Card>
      </div>

<!--      <div class="col-6">-->
<!--        <adminChartTodoLineChart :data="Object.values(status_count)" :labels="Object.keys(status_count)" :height="300" />-->
<!--      </div>-->
    </div>
    <Col span="24" class="px-2 my-1 d-flex justify-center" v-if="loading">
      <Spin size="large"> </Spin>
    </Col>
    <div v-else-if="todoList.length > 0 " class="row">
        <div class="col-6 mt-4" v-for="todo in inProgressToDos" :key="todo.id" >
          <adminProfileToDoItem  :todo="todo" @getSingleTask="getSingleTask" />
        </div>
      <Divider orientation="left" v-if="doneToDOs.length > 0">کارهای تکمیل شده شما</Divider>
        <div class="col-6 mt-4" v-for="todo in doneToDOs" :key="todo.id" >
          <adminProfileToDoItem  :todo="todo" @getSingleTask="getSingleTask"/>
        </div>

      </div>
    <div v-else>
      <Alert type="warning">لیست کارهای شما با این فیلتر خالیست</Alert>
    </div>

    <Modal v-model="showModal" title="جزییات تسک" width="1000" footer-hide>
      <Col span="24" class="px-2 my-1 d-flex justify-content-center" v-if="single_task_loading">
        <Spin size="large"> </Spin>
      </Col>
      <admin-task-detail v-if="!single_task_loading && singleTask" :task="singleTask" @closeDetailModel="showModal = false"/>
    </Modal>
  </div>
</template>

<script>

  import {mapState} from "vuex";

  export  default {
    name : 'todo-list' ,
    props : ['todoList' , 'loading' , 'status_count'  , 'form'] ,
    data() {
      return {
        showModal : false ,
        single_task_loading : false,
        typeList  : [
          {
            name : 'task' ,
            title : 'تسک'
          },
          {
            name : 'checklist' ,
            title : 'چک لیست'
          },
        ],
        statusList : [
          {
            name : 'in_progress',
            title : 'در صف انجام' ,
            type : 'warning'

          },
          {
            name : 'done' ,
            title : 'انجام شد ',
            type : 'success'
          }
        ] ,
        taskStatusList : [
          {
            name : 'best' ,
            title : 'خوش قول'
          },
          {
            name : 'on-time',
            title : 'سروقت'

          },
          {
            name : 'bad' ,
            title : 'بد قول',
          } ,
          {
            name: 'worst',
            title: 'افتضاح'
          }
        ] ,
        taskStatus : null,
        startTodoDate :null ,
        endTodoDate :null ,
        doneTaskStatus :null ,
      }
    },
    computed :{
      ...mapState('admin/task' , ['singleTask']),
      todayDate() {
        return  this.$moment().utc().format('jYYYY-jMM-jDD')
      },
      // allTodos() {
      //   return this.$store.getters['admin/todo/filterTodo'](this.taskStatus ,
      //     this.startTodoDate ? this.$moment(this.startTodoDate).format("YYYY-MM-DD") : '' ,
      //     this.endTodoDate ? this.$moment(this.endTodoDate).format("YYYY-MM-DD") : '' , this.doneTaskStatus )
      // } ,
      requestedStatusList(){
        if(this.requested_status.includes('done')) {
          return false ;
        }else {
          return true ;
        }
      },
      doneToDOs() {
        return  this.todoList.filter(function(x) { return x.status == 'done'; })
      },
      inProgressToDos(){
        return  this.todoList.filter(function(x) { return x.status != 'done'; })
      }
    },
    methods : {
      getSingleTask(task_id) {
        this.showModal = true
        this.single_task_loading = true
        this.$store.dispatch('admin/task/LoadSingleTask' , {id : task_id} ).finally(() => {
          this.single_task_loading = false
        })
      },
      markAsRead(id) {
        this.$store.dispatch('auth/markAsRead' , {id : id})
      }  ,
      search() {
        this.$emit('setSearchData' , this.form)
      }
    }
  }
</script>


<style>
.mark-btn:hover {
  background : #BDECF3 !important;
  border-radius: 3px;
}
.mark-btn:hover Button {
  background : #BDECF3 !important;
}
.unread-box {
  border-radius: 3px;
  border-right: 5px solid red !important;
  background: rgba(244, 183, 183, 0.01) none repeat scroll 0% 0%;
}
.read-btn{
  width: 20px;
  border-radius: 50%;
  height: 23px !important;
  padding: 0 10px !important; border:1px solid rgb(123, 104, 104) !important
}
.read-title{
  background: #fff;
  padding-right: 10px !important;
  margin-bottom: 7px;
  font-size: 12px
}
</style>
