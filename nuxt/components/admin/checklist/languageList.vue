<template>
  <div>
    <div class="col-12 col-md-9 m-auto">
      <Card>
        <p slot="title">
          زبان ها
        </p>
        <p slot="extra">
          <Button type="success" @click="openCreateModal">
            افزودن زبان جدید
          </Button>
        </p>
        <List :loading="languageLoading">
          <adminChecklistLanguageItem v-for="(language , index) in languageList" :key="index"
                              :language="language"/>
        </List>
      </Card>
    </div>
    <Modal v-model="createModal" title="چک لیست جدید" width="500" footer-hide>
      <Form ref="formValidate" :model="formValidate" :rules="ruleValidate" label-position="top"
        @submit.native.prevent="handleSubmit('formValidate')">
        <div class="col-12">
          <FormItem label="عنوان" prop="title">
            <Input v-model="formValidate.title" type="text" placeholder="عنوان را وارد کنید..."></Input>
          </FormItem>
        </div>
        <div class="col-12">
          <FormItem>
            <Button type="primary" :loading="createLoading" html-type="submit">ثبت اطلاعات</Button>
          </FormItem>
        </div>
      </Form>
    </Modal>
  </div>
</template>

<script>
export default {
  name : 'language-list',
  props : ['languageList' , 'languageLoading'] ,
  data() {
    return {
      createModal : false,
      createLoading : false ,
      formValidate : {
        title : '' ,
      },
      ruleValidate : {
        title: [
          { required: true, message: 'فیلد عنوان الزامی است.', trigger: 'change' }
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
          this.$store.dispatch('admin/language/createNewLanguage' , { form : this.formValidate })
            .then(response => {
                this.$Message.success(response.data.message)
                this.createModal = false
                this.createLoading = false
                this.handleReset(name)
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
    handleReset (name) {
      this.$refs[name].resetFields();
    }
  }
}
</script>
