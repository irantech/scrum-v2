<template>
  <div class="col-12 col-md-9 m-auto">
    <Card>
      <p slot="title">
        لیست لیبل ها
      </p>
      <p slot="extra"  v-if="$store.getters['auth/can']('create-taskLabel')">
        <Button type="success" @click="openCreateModal">
          افزودن لیبل جدید
        </Button>
      </p>
      <List :loading="task_label_loading">
        <adminTaskLabelItem v-for="(label , index) in task_label_list" :key="index" :label="label"/>
      </List>
    </Card>
    <Modal v-model="createModal" title="لیبل جدید" width="800" footer-hide>
      <Form ref="formValidate" :model="formValidate" :rules="ruleValidate" label-position="top"
            @submit.native.prevent="handleSubmit('formValidate')">
        <div class="row">
          <div class="col-6">
            <FormItem label="عنوان" prop="title">
              <Input v-model="formValidate.title" type="text" placeholder="عنوان را وارد کنید..."></Input>
            </FormItem>
          </div>
          <div class="col-6">
            <FormItem label="رنگ این بخش" prop="color">
              <verte picker="square" menuPosition="bottom"  v-model="formValidate.color"></verte>
              {{formValidate.color}}
            </FormItem>
          </div>
        </div>
        <div class="col-12">
          <FormItem>
            <Button type="primary" :loading="createLoading" @click="handleSubmit('formValidate')">ثبت اطلاعات</Button>
          </FormItem>
        </div>
      </Form>
    </Modal>
  </div>
</template>

<script>
import verte from 'verte'
import 'verte/dist/verte.css';
export default {
  name : 'task-label-list',
  props : ['task_label_list' , 'task_label_loading'] ,
  components :{
    verte
  },
  data() {
    return {
      createModal : false ,
      createLoading : false,
      formValidate : {
        title : '' ,
        color : ''
      },
      ruleValidate :{
        title : [
          { required: true,  message: 'وارد کردن نام الزامی است.', trigger: 'change' }
        ],
        color : [
          { required: true,  message: 'وارد کردن رنگ الزامی است.', trigger: 'change' }
        ],
      }
    }
  },
  methods : {
    openCreateModal() {
      this.createModal = true
    },
    handleSubmit(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.createLoading = true
          let color = this.formValidate.color.slice(4)
          color = color.replace(")","");
          color = color.replace("(","");
          let first  = color.split(',')
          if(!first[3]){
            first[3] = '0.5'
          }
          this.formValidate.color = `hsla(${first[0]},${first[1]},${first[2]},${first[3]})`
          this.$store.dispatch('admin/task/createNewTaskLabel' , { form : this.formValidate })
            .then(response => {
                this.$Message.success(response.data.message);
                this.createModal = false
                this.createLoading = false
                this.handleReset(name)
              }
            )
            .catch(error => {
              console.log(error)
              this.createLoading = false
              this.$Message.error(error.response);
            })
        } else {
          this.$Message.error('ابتدا خطاهای خود را بررسی کنید.');
        }
      })
    },
    handleReset (name) {
      this.$refs[name].resetFields();
    }
  }
}
</script>
