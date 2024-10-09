<template>
  <ListItem>
    <ListItemMeta :title="section.title" :description="section.description"/>
    <template slot="action">
      <li class="text-success" v-if="$store.getters['auth/can']('admin-edit-sections')">
        <a @click="openUpdateModal">
          ویرایش
        </a>
      </li>
    </template>
    <Modal v-model="updateModal" :title="` ویرایش نقش ${section.title}` " width="500" footer-hide>
      <Form ref="formValidate" :model="formValidate" :rules="ruleValidate" label-position="top">
        <div class="col-12">
          <FormItem label="عنوان" prop="title">
            <Input v-model="formValidate.title" type="text" placeholder="عنوان را وارد کنید..."></Input>
          </FormItem>
        </div>
        <div class="col-12">
          <FormItem label="توضیحات">
            <Input v-model="formValidate.description" type="text" placeholder="عنوان را وارد کنید..."></Input>
          </FormItem>
        </div>
        <div class="col-12">
          <FormItem label="رنگ این بخش">
            <verte picker="square" menuPosition="bottom"  v-model="formValidate.color"></verte>
            {{formValidate.color}}
          </FormItem>
        </div>
        <div class="col-12">
          <FormItem>
            <Button type="primary" :loading="updateLoading" @click="updateSection('formValidate')">ویرایش اطلاعات</Button>
          </FormItem>
        </div>
      </Form>
    </Modal>
  </ListItem>
</template>

<script>
import verte from 'verte'
import 'verte/dist/verte.css';
export default {
  name : 'section-item' ,
  props : ['section' , 'sectionList'] ,
  components :{
    verte
  },
  data () {
    return {
      colors: ['#311B92', '#512DA8', '#673AB7', '#9575CD', '#D1C4E9'],
      updateModal : false ,
      updateLoading : false,
      formValidate: {
        title: '',
        description : '' ,
        color: ''
      },
      ruleValidate: {
        title: [
          { required: true, message: 'یک عنوان وارد کنید', trigger: 'change' }
        ]
      },
    }
  },
  methods : {
    openUpdateModal() {
      this.updateModal = true
      this.formValidate.title = this.section.title
      this.formValidate.description = this.section.description
      this.formValidate.color = this.section.color
    },
    updateSection(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.updateLoading = true
          let color = this.formValidate.color.slice(4)
          color = color.replace(")","");
          color = color.replace("(","");
          let first  = color.split(',')
          console.log(first)
          this.formValidate.color = `hsla(${first[0]},${first[1]},${first[2]},${first[3]})`
          this.$store.dispatch('admin/section/UpdateAdminSections', {
            id: this.section.id,
            form: this.formValidate
          })
            .then(response => {
              this.updateModal = false
              this.updateLoading = false
              this.$Message.success(response.message);
            })
            .catch(error => {
              this.$Message.error(error.response.data.message);
              this.updateLoading = false
            })
            .finally(()=> this.updateLoading  = false)
        } else {
          this.$Message.error('ابتدا خطاهای خود را بررسی کنید.');
        }
      })
    } ,
  }
}
</script>
