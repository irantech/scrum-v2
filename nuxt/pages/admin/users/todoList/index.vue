<template>
  <div>

    <adminIncludeCrumb name="لیست کارهای کاربران"/>
    <adminUserTodoList @setSearchData="setSearchData"
                        :form="form"  :loading="loading"/>
  </div>
</template>

<script>
  import {mapState} from "vuex";
  export  default  {
      name : 'all-user-todolist' ,
      layout : 'admin',
      data() {
          return {
            loading : false ,
            form : {
              startTodoDate : '' ,
              endTodoDate : ''
            }
          }
      },
      created() {
        this.setFormData()
        this.getTodoList()
      },
      computed:{
        allUserTodos() {
          return this.$store.state.admin.todo.allUserTodos;
        },
      },
      methods :{
        getTodoList() {
          this.loading = true
          this.$store.dispatch('admin/todo/getUserToDoList' , this.form).then( res =>{
            this.loading = false
          })
        },
        setFormData() {
          this.form.startTodoDate = this.$moment().subtract(1, 'months').format('jYYYY-jMM-jDD')
          this.form.endTodoDate = this.$moment().utc().format('jYYYY-jMM-jDD')
        },
        setSearchData(formData) {
          this.form = formData
          this.getTodoList()
        }
      }
  }
</script>
