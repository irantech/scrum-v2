<template>
  <ListItem>
    <ListItemMeta :title="label.title" :style="`background: ${label.color}`"/>
    <template slot="action" >
      <li class="text-success mr-3">
        <a @click="openUpdateModal" v-if="!label.trashed">
          ویرایش
        </a>
      </li>
      <li class="text-info" v-if="label.trashed && $store.getters['auth/can']('restore-taskLabel')">
        <a @click="restoreModal = true">
          بازگردانی
        </a>
      </li>
      <li class="text-danger" v-if="!label.trashed && $store.getters['auth/can']('delete-taskLabel')">
        <a @click="deleteModel = true">
          حذف
        </a>
      </li>
    </template>
    <Modal v-model="updateModal" :title="` ویرایش لیبل ${label.title}` " width="500" footer-hide>
      <Form ref="formValidate" :model="formValidate" :rules="ruleValidate" label-position="top">
        <div class="col-12">
          <FormItem label="عنوان" prop="title">
            <Input v-model="formValidate.title" type="text" placeholder="عنوان را وارد کنید..."></Input>
          </FormItem>
        </div>
        <div class="col-12">
          <FormItem label="رنگ این بخش" prop="color">
            <verte picker="square" menuPosition="bottom"  v-model="formValidate.color"></verte>
            {{formValidate.color}}
          </FormItem>
        </div>
        <div class="col-12">
          <FormItem>
            <Button type="primary" :loading="updateLoading" @click="updateLabel('formValidate')">ویرایش اطلاعات</Button>
          </FormItem>
        </div>
      </Form>
    </Modal>
    <Modal v-model="deleteModel" width="360">
      <p slot="header" style="color:#f60;text-align:center">
        <Icon type="ios-information-circle"></Icon>
        <span>حذف لیبل {{label.title}}</span>
      </p>
      <div style="text-align:center">
        <p>آیا از حذف این لیبل اطمینان دارید؟</p>
      </div>
      <div slot="footer">
        <Button type="error" size="large" long :loading="deleteLoading" @click="removeLabel()">حذف</Button>
      </div>
    </Modal>
    <Modal v-model="restoreModal" width="360">
      <p slot="header" style="color:#17a2b8;text-align:center">
        <Icon type="ios-information-circle"></Icon>
        <span>بازگرداندن لیبل {{ label.title }}</span>
      </p>
      <div style="text-align:center">
        <p>آیا از بازگرداندن این لیبل اطمینان دارید؟</p>
      </div>
      <div slot="footer">
        <Button type="info" size="large" long :loading="restoreLoading" @click="restoreLabel()">بازگرداندن</Button>
      </div>
    </Modal>
  </ListItem>
</template>

<script>
import verte from 'verte'
import 'verte/dist/verte.css';
export default {
  name : 'label-item' ,
  props : ['label'] ,
  components :{
    verte
  },
  data () {
    return {
      deleteModel : false ,
      deleteLoading  : false ,
      restoreModal : false ,
      restoreLoading : false,
      colors: ['#311B92', '#512DA8', '#673AB7', '#9575CD', '#D1C4E9'],
      updateModal : false ,
      updateLoading : false,
      formValidate: {
        title: '',
        color: ''
      },
      ruleValidate: {
        title: [
          { required: true, message: 'یک عنوان وارد کنید', trigger: 'change' }
        ] ,
        color: [
          { required: true, message: 'یک رنگ وارد کنید', trigger: 'change' }
        ]
      },
    }
  },
  methods : {
    removeLabel(){
      this.deleteLoading = true
      this.$store.dispatch('admin/task/removeTaskLabel', {id: this.label.id})
        .then(response => {
          this.deleteLoading = false
          this.$Message.warning(response.data.message);
          this.deleteModel = false
        })
        .catch(error => {
          this.deleteLoading = false
          this.$Message.error(error.response.data);
        })
    },
    restoreLabel(){
      this.restoreLoading = true
      this.$store.dispatch('admin/task/restoreTaskLabel', {id: this.label.id})
        .then(response => {
          this.restoreLoading = false
          this.$Message.warning(response.data.message);
          this.restoreModal = false
        })
        .catch(error => {
          this.restoreLoading = false
          this.$Message.error(error.message);
        })
    } ,
    openUpdateModal() {
      this.updateModal = true
      this.formValidate.title = this.label.title
      this.formValidate.color = this.label.color
    },
    updateLabel(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.updateLoading = true
          let color = this.formValidate.color.slice(4)
          console.log(color)
          color = color.replace(")","");
          color = color.replace("(","");

          let first  = color.split(',')
          if(!first[3]){
            first[3] = '0.5'
          }
          this.formValidate.color = `hsla(${first[0]},${first[1]},${first[2]},${first[3]})`
          this.$store.dispatch('admin/task/UpdateTaskLabel', {
            id: this.label.id,
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
