<template xmlns="http://www.w3.org/1999/html">
    <div class="col-12 todo">
      <Card class="mb-2">
        <Form  v-model="form" class="row" ref="searchForm"
               @submit.native.prevent="search('searchForm')">
            <div class="col-12 col-md-4">
                <FormItem class="mb-2">
                  <Select search v-model="section"  clearable placeholder=" بخش ">
                    <Option v-for="(item , index) in this.$store.state.admin.todo.todo_section_list" :value="item.role.section.id" :key="index">{{ item.role.section.title }}</Option>
                  </Select>
                </FormItem>
            </div>
            <div class="col-12 col-md-4">
                <FormItem class="mb-2">
                  <Select search v-model="taskStatus"  clearable placeholder="وضعیت کارها">
                    <Option v-for="item in statusList" :value="item.name" :key="item.name">{{ item.title }}</Option>
                  </Select>
                </FormItem>
            </div>
            <div class="col-12 col-md-4">
                <FormItem class="mb-2">
                  <Select search v-model="username"  clearable placeholder="نام همکاران">
                    <Option v-for="item in this.$store.state.admin.todo.todo_user_list" :value="item.id" :key="item.id">{{ item.fullName }}</Option>
                  </Select>
                </FormItem>
            </div>

            <div class="col-12 col-md-3 mb-2">
              <client-only>
                <date-picker popover="bottom-right" :max="todayDate"  auto-submit
                             format="jYYYY-jMM-jDD"
                             v-model="startDate"
                             placeholder="تاریخ شروع قرارداد" clearable />
              </client-only>
            </div>
            <div class="col-12 col-md-3">
              <client-only>
                <date-picker popover="bottom-right" format="jYYYY-jMM-jDD"  auto-submit
                             v-model="endDate" placeholder="تاریخ پایان قرارداد"
                             :min="startDate"  clearable/>
              </client-only>
            </div>
            <div class="col-12 col-md-3 mb-2">
              <client-only>
                <date-picker popover="bottom-right" :max="todayDate"  auto-submit
                             format="jYYYY-jMM-jDD"
                             v-model="form.startTodoDate"
                             placeholder="کارها از تاریخ " clearable />
              </client-only>
            </div>
            <div class="col-12 col-md-3">
              <client-only>
                <date-picker popover="bottom-right" format="jYYYY-jMM-jDD"  auto-submit
                             v-model="form.endTodoDate" placeholder="تا تاریخ"
                             :min="form.startTodoDate"  clearable/>
              </client-only>
            </div>
            <div class="col-2">
              <Button type="success"  html-type="submit">جستجو</Button>
            </div>
        </Form>
      </Card>
      <Col span="24" class="px-2 my-1 d-flex justify-center" v-if="loading">
        <Spin size="large"> </Spin>
      </Col>
      <Collapse v-else v-model="openSection">
        <Panel  v-if="(section!== ''  && section == item.role.section.id ) || section == '' || section == undefined"
                v-for="(item , index) in this.$store.state.admin.todo.todo_section_list"
               :key="index" :style="`background: ${item.role.section.color}`">
          <div class="badge-box d-flex">
            <div class="py-1 px-2 mx-2"
                 v-for="status in doneTaskStatus"  :key="status.name" >
              <Tooltip :content="status.title" placement="top">
                <Badge :status="status.type" :text="`${countDoneStatusSection(status.name , item.role.section.id)}`" />
                {{status.title}}
              </Tooltip>
            </div>
            <div class="py-1 px-2 mx-2"
                 v-for="status in statusList"  :key="status.name" >
              <Tooltip :content="status.title" placement="top">
                <Badge :status="status.type" :text="`${countSection(status.name , item.role.section.id)}`" />
                {{status.title}}
              </Tooltip>
            </div>
          </div>

          <span>{{ item.role.section.title }}</span>
          <template slot="content">

            <Collapse v-model="openParts">
              <Panel v-for="(user , index) in allUserTodos"
                     v-if="user.role.section && user.role.section.id == item.role.section.id"
                     :key="index" :name="user.id.toString()">
                <div class="badge-box d-flex">
                  <div v-for="status in doneTaskStatus" :key="status.name"
                       class="py-1 px-2 mx-2">
                    <Tooltip :content="status.title" placement="top">
                      <Badge :status="status.type"  :text="`${countDoneStatusTask(status.name , user.todo_lists)}`" />
                      {{status.title}}
                    </Tooltip>
                  </div>
                  <div v-for="status in statusList" :key="status.name"
                       class="py-1 px-2 mx-2">
                    <Tooltip :content="status.title" placement="top">
                      <Badge :status="status.type"  :text="`${countTasks(status.name , user.todo_lists)}`" />
                      {{status.title}}
                    </Tooltip>
                  </div>
                </div>
                <span>{{ user.fullName }}</span>

                <template slot="content">
                  <div class="row">
                    <div class="col-6 mt-4" v-for="(todo , counter) in user.todo_lists" :key="counter" >
                          <adminUserTodoItem  :todo="todo" />
                    </div>
                  </div>
                </template>
              </Panel>

            </Collapse>

          </template>
        </Panel>
      </Collapse>



    </div>
</template>


<script>

  export default {
      name : 'all-user-todolist' ,
    props : [ 'form'  , 'loading'],
      data() {
        return {
          openParts :  '' ,
          openSection : '',
          section : null ,
          taskStatus : null,
          username : null ,
          startDate :null ,
          endDate :null ,
          startTodoDate :null ,
          endTodoDate :null ,
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
            } ,
            ] ,
          doneTaskStatus : [
            {
              name : 'best' ,
              title : 'خوش قول' ,
              type : 'lime'
            },
            {
              name : 'on-time',
              title : 'سروقت' ,
              type : 'purple'

            },
            {
              name : 'bad' ,
              title : 'بدقول ',
              type : 'gold'
            } ,
            {
              name : 'worst' ,
              title : 'افتضاح  ',
              type : 'volcano'
            } ,
          ]
        }
      },
      computed :  {
        todayDate() {
          return  this.$moment().utc().format('jYYYY-jMM-jDD')
        },
        allUserTodos() {
          return this.$store.getters['admin/todo/filterTodoList'](this.section , this.taskStatus , this.username ,
            this.startDate ? this.$moment(this.startDate).format("YYYY-MM-DD") : '' ,
            this.endDate ? this.$moment(this.endDate).format("YYYY-MM-DD") : '',
            this.startTodoDate ? this.$moment(this.startTodoDate).format("YYYY-MM-DD") : '' ,
            this.endTodoDate ? this.$moment(this.endTodoDate).format("YYYY-MM-DD") : '' ,)
        } ,
      },
      methods : {
        search() {
          this.$emit('setSearchData' , this.form)
        },
        countTasks(status , todoLists) {
          let counter = 0;
          todoLists.forEach(task => {
            if(task.status == status)
              counter ++
          })
          return counter
        } ,
        countDoneStatusTask(status , todoLists) {
          let counter = 0;
          todoLists.forEach(task => {
            console.log(task , status)
            if(task.todo_status == status) {
              counter ++
            }

          })
          return counter
        } ,
        countSection(status , section) {
          let counter = 0;
          this.allUserTodos.forEach(user => {
            if(user.role.section && user.role.section.id == section)
            {
              user.todo_lists.forEach(task => {
                if(task.status == status)
                  counter ++
              })
            }
          })
          return counter

        },
        countDoneStatusSection(status , section) {
          let counter = 0;
          this.allUserTodos.forEach(user => {
            if(user.role.section && user.role.section.id == section)
            {
              user.todo_lists.forEach(task => {
                if(task.todo_status == status)
                  counter ++
              })
            }
          })
          return counter

        }
      }

  }
</script>

<style>
  .badge-box {
    position: absolute;
    left: 40px;
    bottom: 12px;
  }
  .todo .ivu-badge-status-dot {
     width: 10px !important;
     height: 10px !important;
  }
  .todo .ivu-collapse > .ivu-collapse-item > .ivu-collapse-header {
     color : #000 !important;
  }
  .todo .ivu-badge-status-text {
     color : #000 !important;
  }
</style>
