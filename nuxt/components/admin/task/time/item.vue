<template>
  <ListItem>
    <ListItemMeta :title="task.checklist.title + '  ' + task.section.title + ' (' + checkStatus+ ')'" :description="task.description"/>
    <template slot="action">
      <li class="text-success">
        <a @click="openUpdateModal">
          ویرایش
        </a>
      </li >
<!--      <li class="text-danger">-->
<!--        <a @click="deleteModel = true">-->
<!--          حذف-->
<!--        </a>-->
<!--      </li>-->
    </template>
    <Modal v-model="deleteModel" width="360">
      <p slot="header" style="color:#f60;text-align:center">
        <Icon type="ios-information-circle"></Icon>
        <span>حذف چک لیست انتخابی</span>
      </p>
      <div style="text-align:center">
        <p>آیا از حذف این چک لیست اطمینان دارید؟</p>
      </div>
      <div slot="footer">
        <Button type="error" size="large" long :loading="deleteLoading" @click="removeTask()">حذف</Button>
      </div>
    </Modal>
    <Modal v-model="updateModal" title=" ویرایش تسک " width="500" footer-hide>
      <Form ref="formValidate" :model="formValidate" :rules="ruleValidate" label-position="top"
            @submit.native.prevent="updateTask('formValidate')">
        <div class="row">
          <div class="col-12">
            <FormItem label="چک لیست" prop="checklist_id">
              <Select v-model="formValidate.checklist_id" placeholder="انتخاب کنید">
                <Option v-for="(checklist , index) in checklists"  :key="index" :value="checklist.id">{{ checklist.title }}</Option>
              </Select>
            </FormItem>
          </div>
          <div class="col-12">
            <FormItem label="بخش" prop="section_id" disabled="disabled">
              <Select v-model="formValidate.section_id" placeholder="انتخاب کنید">
                <Option v-for="(section , index) in sectionList"  :key="index" :value="section.id">{{ section.title }}</Option>
              </Select>
            </FormItem>
          </div>
          <div class="col-12">
            <FormItem label="نوع" prop="status" disabled="disabled">
              <Select v-model="formValidate.task_status" placeholder="انتخاب کنید" >
                <Option value="0">برگشت</Option>
                <Option value="1">اختصاص</Option>
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
            <FormItem label="تلرانس روزی" prop="task_day_duration">
              <InputNumber v-model="formValidate.interval_day_duration"  placeholder="چند ساعت کاری را وارد کنید..." controls-outside />
            </FormItem>
          </div>
          <div class="col-6">
            <FormItem label="تلرانس ساعتی" prop="task_time_duration">
              <date-picker v-model="formValidate.interval_time_duration" type="time" />
            </FormItem>
          </div>
          <div class="col-12">
            <FormItem label="توضیحات" prop="description">
              <Input v-model="formValidate.description" type="textarea" :autosize="{minRows: 2,maxRows: 5}" placeholder="توضیحات را وارد کنید..."></Input>
            </FormItem>
          </div>
          <div class="col-12">
            <FormItem>
              <Button type="primary" :loading="updateLoading" html-type="submit">ویرایش اطلاعات</Button>
            </FormItem>
          </div>
        </div>

      </Form>
    </Modal>
  </ListItem>
</template>

<script>
export default {
  name : 'task-item' ,
  props : ['task' , 'checklists' , 'sectionList'] ,
  data () {
    return {
      deleteModel : false ,
      deleteLoading : false ,
      updateModal : false ,
      updateLoading : false,
      restoreModal : false,
      restoreLoading : false,
      formValidate: {
        checklist_id: '',
        description : '',
        task_status :'' ,
        section_id : '',
        task_day_duration : '',
        task_time_duration : '',
        interval_day_duration : '',
        interval_time_duration : '',
      },
      ruleValidate: {
        checklist_id: [
          { required: true, type : 'integer' , message: 'فیلد چک لیست الزامی است.', trigger: 'change' }
        ],
        section_id : [
          { required : true , type : 'integer' , message: 'فیلد بخش الزامی است.', trigger: 'change' }
        ],
        task_status : [
          { required : true , type : 'integer' , message: 'فیل نوع تسک الزامی است.', trigger: 'change' }
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
      },
    }
  },
  methods : {
    removeTask() {
      this.deleteLoading = true
      this.$store.dispatch('admin/taskTime/removeChecklist', {id: this.checklist.id})
        .then(response => {
          this.deleteLoading = false
          this.$Message.warning(response.data.message);
          this.deleteModel = false
        })
        .catch(error => {
          this.deleteLoading = false
          this.$Message.error(error.message);
        })
    },
    openUpdateModal() {
      this.updateModal = true
      this.formValidate.checklist_id = this.task.checklist.id
      this.formValidate.section_id = this.task.section.id
      this.formValidate.task_status = this.task.task_status
      this.formValidate.task_day_duration = this.task.task_day_duration
      this.formValidate.task_time_duration = this.task.task_time_duration
      this.formValidate.interval_day_duration = this.task.interval_day_duration
      this.formValidate.interval_time_duration = this.task.interval_time_duration
      this.formValidate.description  =  this.task.description
    },
    updateTask(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.updateLoading = true
          this.$store.dispatch('admin/taskTime/UpdateAdminTasks', {
            id: this.task.id,
            form: this.formValidate
          })
            .then(response => {
              this.updateModal = false
              this.updateLoading = false
              this.$Message.success(response.data.message);
            })
            .catch(error => {
              if(error.response.data.message)
                this.$Message.error(error.response.data.message);
              if(error.response.data.errors) {
                let errors = error.response.data.errors
                errors.forEach(error => {
                  this.$Message.error(error)
                })
              }
              this.updateLoading = false
            })
            .finally(()=> this.updateLoading  = false)
        } else {
          this.$Message.error('ابتدا خطاهای خود را بررسی کنید.');
        }
      })
    } ,
  } ,
  computed :{
    checkStatus()  {
      if(this.task.task_status == 1 ) {
        return 'اختصاص';
      }else {
        return 'برگشت';
      }
    }
  }
}
</script>
