<template>
  <Form ref="formInline" :model="formInline" :rules="ruleInline" label-position="top"
        @submit.native.prevent="handleSubmit('formInline')">
    <div class="row">
      <div class="col-12">
        <FormItem class="col-12">
          <Select placeholder="انتخاب کارمند"  v-model="formInline.user" filterable>
            <Option v-for="item in $store.state.admin.user.userList" :value="item.id" :key="item.id">{{ item.name }}</Option>
          </Select>
        </FormItem>
      </div>
      <div class="col-12" v-if="user.userName == task.user_owner.userName">
        <FormItem class="col-12">
          <Select placeholder="انتخاب وضعیت"  v-model="formInline.status" filterable>
            <Option v-for="item in status_list_maker" :value="item.value" :key="item.value">{{ item.title }}</Option>
          </Select>
        </FormItem>
      </div>
      <div class="col-12" v-else>
        <FormItem class="col-12">
          <Select placeholder="انتخاب وضعیت"  v-model="formInline.status" filterable>
            <Option v-for="item in status_list_donner" :value="item.value" :key="item.value">{{ item.title }}</Option>
          </Select>
        </FormItem>
      </div>
      <div class="col-12">
        <FormItem  class="col-12">
          <Input v-model="formInline.description" placeholder="توضیحات"></Input>
        </FormItem>
      </div>
      <div class="col-12">
        <FormItem>
          <Button type="primary" :loading="sectionLoading" html-type="submit">ثبت اطلاعات</Button>
        </FormItem>
      </div>
    </div>

  </Form>
</template>
<script>
import {mapState} from "vuex";

export default {
  name : 'task-section-change' ,
  props : ['task' , 'todo'] ,
  data() {
    return{
      status_list_maker :[
        {
          value : 'hold' ,
          title : 'نیاز به بررسی'
        },
        {
          value : 'running' ,
          title : 'بره توی کار'
        }

      ],
      status_list_donner :[
        {
          value : 'done' ,
          title : 'انجام شد'
        }
      ],
      sectionLoading : false ,
      formInline : {
        user  : '',
        description  : '' ,
        status  : ''
      } ,
      ruleInline : {
        label: [
          { required: true , type : 'array', message: 'انتخاب وضعیت مهم است.', trigger: 'blur' }
        ],
      },
    }
  },
  computed : {
    ...mapState('auth' , ['user'])
  },
  methods :{
    handleSubmit(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.sectionLoading = true
          this.$store.dispatch('admin/task/changeTaskSection' , {
            form : this.formInline ,
            task_id : this.task.id
          })
            .then(response => {

                let todo = ''
            
                if(response.data.data.last_todo_list) {
                  todo = response.data.data.last_todo_list
                  // if(this.formInline.status == 'complete' ){
                  //   todo = todo[todo.length-1]
                  // }else{
                  //   todo = todo[todo.length-2]
                  // }

                }
              console.log(todo)
              if(this.$route.name != 'admin-task'){
                this.$store.commit('admin/todo/CHANGE_TODO_STATUS' , {
                  'task_id' : this.task.id ,
                  'todo'   : todo
                })
              }

                this.$emit('closeSectionModel')
                this.$emit('closeDetailModel')
                this.$Message.success(response.data.message)
                this.sectionLoading = false
                this.handleSubmitReset(name)
              }
            )
            .catch(error => {
              if(error.response.data.message)
                this.$Message.error(error.response.data.message);
              if(error.response.data.errors) {
                let errors = error.response.data.errors;
                errors.forEach(error => {
                  this.$Message.error(error)
                })
              }
              this.sectionLoading = false
            })
        } else {
          this.$Message.error('ابتدا خطاهای خود را بررسی کنید.');
        }
      })
    },
    handleSubmitReset (name) {
      this.$refs[name].resetFields();
    },
  } ,
}
</script>
