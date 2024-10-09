<template>
  <ListItem>
    <ListItemMeta>
      <p slot="title">
        {{ sms.title }}
      </p>
    </ListItemMeta>


    <template slot="action">
      <li class="text-success">
        <a @click="openUpdateModal">
          ویرایش
        </a>
      </li>
      <li class="text-danger">
        <a @click="deleteModel = true">
          حذف
        </a>
      </li>
    </template>
    <Modal v-model="deleteModel" width="360">
      <p slot="header" style="color:#f60;text-align:center">
        <Icon type="ios-information-circle"></Icon>
        <span>حذف  زبان انتخابی</span>
      </p>
      <div style="text-align:center">
        <p>آیا از حذف زبان اطمینان دارید؟</p>
      </div>
      <div slot="footer">
        <Button type="error" size="large" long :loading="deleteLoading" @click="removeSmsTemplate()">حذف</Button>
      </div>
    </Modal>
    <Modal v-model="updateModal" :title="`  ویرایش${sms.title} ` " width="500" footer-hide>
      <Form ref="formValidate" :model="formValidate" :rules="ruleValidate" label-position="top"
            @submit.native.prevent="updateSmsTemplate('formValidate')">
        <div class="col-12">
          <FormItem label="عنوان" prop="title">
            <Input v-model="formValidate.title" type="text" placeholder="عنوان را وارد کنید..."></Input>
          </FormItem>
          <FormItem label="کلید اصلی" prop="key">
            <Input v-model="formValidate.key" type="text" placeholder="عنوان را وارد کنید..."></Input>
          </FormItem>
          <FormItem label="پارامترها" prop="params">
            <Input v-model="formValidate.params" type="text" placeholder="عنوان را وارد کنید..."></Input>
          </FormItem>
          <FormItem label="قالب" prop="template">
            <Input v-model="formValidate.template" type="text" placeholder="عنوان را وارد کنید..."></Input>
          </FormItem>
        </div>
        <div class="col-12">
          <FormItem>
            <Button type="primary" :loading="updateLoading" @click="updateSmsTemplate('formValidate')">ویرایش اطلاعات</Button>
          </FormItem>
        </div>
      </Form>
    </Modal>
  </ListItem>
</template>

<script>
export default {
  name : 'sms-template-item' ,
  props : ['sms'] ,
  data () {
    return {
      deleteModel : false ,
      deleteLoading : false ,
      updateModal : false ,
      updateLoading : false,
      formValidate: {
        title: '' ,
        key : '' ,
        params :'' ,
        template :''
      },
      ruleValidate: {
        title: [
          { required: true ,message: 'وارد کردن عنوان اجباری است.', trigger: 'change' }
        ],
        key: [
          { required: true ,message: 'وارد کردن عنوان اجباری است.', trigger: 'change' }
        ],
      },
    }
  },
  methods : {
    removeSmsTemplate() {
      this.deleteLoading = true
      this.$store.dispatch('admin/smsTemplate/removeSmsTemplate', {id: this.sms.id})
        .then(response => {
          this.deleteLoading = false
          this.$Message.warning(response.data.message);
          this.deleteModel = false
        })
        .catch(error => {
          this.deleteLoading = false
          this.$Message.error(error.response.data.message);
        })
    },
    openUpdateModal() {
      this.updateModal = true
      this.formValidate.title = this.sms.title
      this.formValidate.key = this.sms.key
      this.formValidate.params = this.sms.params
      this.formValidate.template = this.sms.template
    },
    updateSmsTemplate(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.updateLoading = true
          this.$store.dispatch('admin/smsTemplate/updateSmsTemplate', {
            id: this.sms.id,
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
  },
}
</script>
