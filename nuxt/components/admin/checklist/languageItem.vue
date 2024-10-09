<template>
  <ListItem>
    <ListItemMeta>
      <p slot="title">
        {{ language.title }}
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
        <Button type="error" size="large" long :loading="deleteLoading" @click="removeLanguage()">حذف</Button>
      </div>
    </Modal>
    <Modal v-model="updateModal" :title="`  ویرایش${language.title} ` " width="500" footer-hide>
      <Form ref="formValidate" :model="formValidate" :rules="ruleValidate" label-position="top"
        @submit.native.prevent="updateLanguage('formValidate')">
        <div class="col-12">
          <FormItem label="عنوان" prop="title">
            <Input v-model="formValidate.title" type="text" placeholder="عنوان را وارد کنید..."></Input>
          </FormItem>
        </div>
        <div class="col-12">
          <FormItem>
            <Button type="primary" :loading="updateLoading" html-type="submit">ویرایش اطلاعات</Button>
          </FormItem>
        </div>
      </Form>
    </Modal>
  </ListItem>
</template>

<script>
export default {
  name : 'language-item' ,
  props : ['language'] ,
  data () {
    return {
      deleteModel : false ,
      deleteLoading : false ,
      updateModal : false ,
      updateLoading : false,
      formValidate: {
        title: ''
      },
      ruleValidate: {
        title: [
          { required: true ,message: 'وارد کردن عنوان اجباری است.', trigger: 'change' }
        ],
      },
    }
  },
  methods : {
    removeLanguage() {
      this.deleteLoading = true
      this.$store.dispatch('admin/language/removeLanguage', {id: this.language.id})
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
      this.formValidate.title = this.language.title
    },
    updateLanguage(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.updateLoading = true
          this.$store.dispatch('admin/language/updateLanguage', {
            id: this.language.id,
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
