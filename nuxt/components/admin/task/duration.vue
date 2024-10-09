<template>
  <Form ref="formValidate" :model="formValidate" :rules="ruleValidate" label-position="top"
        @submit.native.prevent="handleSubmit('formValidate')">
    <div class="row">
      <div class="col-12">
        <FormItem label="بخش" prop="section_id">
          <Select v-model="formValidate.section_id" placeholder="انتخاب کنید">
            <Option v-for="(section , index) in $store.state.admin.section.sectionList"  :key="index" :value="section.id">{{ section.title }}</Option>
          </Select>
        </FormItem>
      </div>
      <div class="col-6">
        <FormItem label="چند روز کاری" prop="task_day_duration">
          <InputNumber v-model="formValidate.task_day_duration"  placeholder="چند ساعت کاری را وارد کنید..." controls-outside />
        </FormItem>
      </div>
      <div class="col-6">
        <FormItem label="چند ساعت کاری" prop="task_time_duration">
          <date-picker v-model="formValidate.task_time_duration" type="time" />
        </FormItem>
      </div>
      <div class="col-6">
        <FormItem label="تلرانس روزی" prop="interval_day_duration">
          <InputNumber v-model="formValidate.interval_day_duration"  placeholder="چند ساعت کاری را وارد کنید..." controls-outside />
        </FormItem>
      </div>
      <div class="col-6">
        <FormItem label="تلرانس ساعتی" prop="interval_time_duration">
          <date-picker v-model="formValidate.interval_time_duration" type="time" />
        </FormItem>
      </div>
      <div class="col-12">
        <FormItem>
          <Button type="primary" :loading="createLoading" html-type="submit">ثبت اطلاعات</Button>
        </FormItem>
      </div>
    </div>

  </Form>
</template>
<script>
export default {
  name : 'task-duration' ,
  props : ['task'] ,
  data() {
    return{
      createLoading : false  ,
      formValidate : {
        checklist : '' ,
        section : '',
        status : 1 ,
        description : '',
        task_day_duration : 0 ,
        task_time_duration : '00:00' ,
        interval_day_duration : 0 ,
        interval_time_duration : '00:00' ,
      },
      ruleValidate : {
        checklist_id: [
          { required: true, type : 'integer' , message: 'فیلد چک لیست الزامی است.', trigger: 'change' }
        ],
        section_id : [
          { required : true , type : 'integer' , message: 'فیلد بخش الزامی است.', trigger: 'change' }
        ],
        task_status : [
          { required : true ,  message: 'فیل نوع تسک الزامی است.', trigger: 'change' }
        ],
        task_day_duration : [
          { type : 'number' ,   message: 'لطفا یک عدد وارد کنید', trigger: 'change' }
        ],
        task_time_duration : [
          { pattern:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/ ,  message: 'فرمت ساعت درست وارد نشده', trigger: 'change' }
        ],
        interval_day_duration : [
          { type : 'number' ,   message: 'لطفا یک عدد وارد کنید', trigger: 'change' }
        ],
        interval_time_duration : [
          { pattern:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/ ,  message: 'فرمت ساعت درست وارد نشده', trigger: 'change' }
        ]
      }
    }
  },
  methods :{
    handleSubmit(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.createLoading = true
          this.formValidate.task = this.task.id
          this.formValidate.task_status = 1
          this.$store.dispatch('admin/taskTime/createNewTask' , { form : this.formValidate })
            .then(response => {
                this.$Message.success(response.data.message)
                this.$emit('closeSetTimeModel')
                this.createLoading = false
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

