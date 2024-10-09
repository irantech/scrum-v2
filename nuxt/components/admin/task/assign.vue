<template>
  <Form ref="assignForm" :model="assignForm" :rules="assignValidate" label-position="top"
        @submit.native.prevent="assign('assignForm')">
    <div class="row">
      <div class="col-12">
        <FormItem prop="user" class="mb-0">
          <Select placeholder="انتخاب کارمند"  v-model="assignForm.user" filterable>
            <Option v-for="item in $store.state.admin.user.userList" :value="item.id" :key="item.id">{{ item.name }}</Option>
          </Select>
        </FormItem>
      </div>
      <div class="col-12">
        <FormItem>
          <Button type="primary" :loading="assignLoading" html-type="submit">اختصاص دادن تسک های انتخاب شده به این فرد</Button>
        </FormItem>
      </div>
    </div>

  </Form>
</template>
<script>
export default {
  name : 'task-assign' ,
  props : ['task'] ,
  data() {
    return{
      assignLoading : false ,
      assignForm : {
        'user': '' ,
        'subTaskList' : []
      },
      assignValidate :{
        user: [
          { required: true , type : 'integer', message: 'فرد مورد نظر انتخاب شود.', trigger: 'blur' }
        ],
      },
    }
  },
  methods :{
    assign(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.assignLoading = true
          this.assignForm.subTaskList = this.$store.state.admin.task.selectedSubTask
          this.$store.dispatch('admin/task/assignSubTaskToUser' , {
            form : this.assignForm,
            task_id : this.task.id
          })
            .then(response => {
                this.$Message.success(response.data.message)
                this.$store.commit('admin/task/SET_SELECTED_SUB_TASK_LIST' , [])
                this.$emit('closeAssignModel')
                this.$emit('emptySelectBox')
                this.assignLoading = false
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
              this.createLoading = false
            })
        } else {
          this.$Message.error('ابتدا خطاهای خود را بررسی کنید.');
        }
      })
    },
    handleSubmitReset (name) {
      this.$refs[name].resetFields();
    },
  }
}
</script>

