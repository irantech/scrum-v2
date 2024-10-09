<template>
  <Form ref="formInline" :model="formInline" :rules="ruleInline" label-position="top"
        @submit.native.prevent="handleSubmit('formInline')">
    <div class="row">
      <div class="col-12">
        <FormItem label="لیست لیبل ها" class="col-12">
          <Select v-model="formInline.label" multiple label="لیبل">
            <Option v-for="item in this.$store.state.admin.task.taskLabelList" :value="item.id" :key="item.id">{{ item.title }}</Option>
          </Select>
        </FormItem>
      </div>
      <div class="col-12">
        <FormItem>
          <Button type="primary" :loading="labelLoading" html-type="submit">ثبت اطلاعات</Button>
        </FormItem>
      </div>
    </div>

  </Form>
</template>
<script>
 export default {
  name : 'task-label-change' ,
  props : ['task' ] ,
  data() {
    return{
      labelLoading : false ,
      formInline : {
        label  : ''
      } ,
      ruleInline : {
        label: [
          { required: true , type : 'array', message: 'انتخاب وضعیت مهم است.', trigger: 'blur' }
        ],
      },
    }
  },
  methods :{
    handleSubmit(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.labelLoading = true
          this.$store.dispatch('admin/task/changeTaskLabel' , {
            form : this.formInline ,
            task_id : this.task.id
          })
            .then(response => {
                this.$Message.success(response.data.message)

                this.$emit('closeLabelModal')
                this.labelLoading = false
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
              this.labelLoading = false
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
  created(){
    this.formInline.label = this.task.label_list.map(x => x.id)
  }
}
</script>
