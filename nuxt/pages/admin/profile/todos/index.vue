<template>
  <div>
    <adminIncludeCrumb name="لیست کارهای شما"/>
    <adminProfileToDoList :todoList="todoList"    @setSearchData="setSearchData"
                          :form="form" :status_count="status_count" :loading="loading"/>
  </div>
</template>

<script>
import {mapState} from "vuex";

export default {
  name : 'todo' ,
  layout : 'admin',
  data() {
    return{
      loading : false ,
      form : {
        status : '' ,
        type : '' ,
        start_time : '' ,
        start_delivery_time : '' ,
        end_delivery_time : '' ,
        has_delivery : '' ,
        end_time : '' ,
        title : '' ,
        done_task_status : '' ,
      },
    }
  },
  created() {
    this.setFormData()
    this.getTodoList()
    this.$store.dispatch('admin/task/LoadTaskLabelList' )
    this.$store.dispatch('admin/user/getUserList' )
    this.$store.dispatch('admin/section/LoadAdminSections' )
  },
  computed :{
    todoList() {
      return this.$store.state.admin.todo.toDos;
    },
    status_count() {
      return this.$store.state.admin.todo.status_count
    },
    read() {
      return this.$store.getters['auth/readToDos']
    },
    unread(){
      return this.$store.getters['auth/unreadTodos']
    }
  } ,
  methods : {
    setFormData() {
      this.form.start_time = this.$moment().subtract(15, 'days').format('jYYYY-jMM-jDD')
      this.form.end_time = this.$moment().utc().format('jYYYY-jMM-jDD')
    },
    getTodoList() {
      this.loading = true
      this.$store.dispatch('admin/todo/getToDoList' , this.form).then(response=>{
        let task_list = this.todoList.filter(todo => todo.type == 'Task')
        this.$store.commit('admin/task/SET_TASK_LIST', task_list.map(x => x.task))
        this.loading = false
      })
    } ,
    setSearchData(formData) {
      this.form = formData
      this.getTodoList()
    }
  }
}
</script>
