<template>
  <ListItem>
    <ListItemMeta :title="permission.title" :description="permission.label"/>
<!--    <template slot="action">-->
<!--      <li class="text-success" v-if="!permission.trashed">-->
<!--        <i-switch v-model="status" @on-change="changePermissionStatus" :disabled="statusLoading" size="large">-->
<!--          <span slot="open">پیشفرض</span>-->
<!--          <span slot="close">خاموش</span>-->
<!--        </i-switch>-->
<!--      </li>-->
<!--      <li class="text-success" v-if="!permission.trashed">-->
<!--        <a @click="openUpdateModal">-->
<!--          ویرایش-->
<!--        </a>-->
<!--      </li>-->
<!--      <li class="text-info" v-if="permission.trashed">-->
<!--        <a @click="restoreModal = true">-->
<!--          بازگردانی-->
<!--        </a>-->
<!--      </li>-->
<!--      <li class="text-danger" v-else>-->
<!--        <a @click="deleteModel = true">-->
<!--          حذف-->
<!--        </a>-->
<!--      </li>-->
<!--    </template>-->
<!--    <Modal v-model="deleteModel" width="360">-->
<!--      <p slot="header" style="color:#f60;text-align:center">-->
<!--        <Icon type="ios-information-circle"></Icon>-->
<!--        <span>حذف سطح دسترسی انتخابی</span>-->
<!--      </p>-->
<!--      <div style="text-align:center">-->
<!--        <p>آیا از حذف این سطح دسترسی اطمینان دارید؟</p>-->
<!--      </div>-->
<!--      <div slot="footer">-->
<!--        <Button type="error" size="large" long :loading="deleteLoading" @click="removePermission()">حذف</Button>-->
<!--      </div>-->
<!--    </Modal>-->
<!--    <Modal v-model="restoreModal" width="360">-->
<!--      <p slot="header" style="color:#17a2b8;text-align:center">-->
<!--        <Icon type="ios-information-circle"></Icon>-->
<!--        <span>بازگرداندن نقش انتخابی</span>-->
<!--      </p>-->
<!--      <div style="text-align:center">-->
<!--        <p>آیا از بازگرداندن این نقش اطمینان دارید؟</p>-->
<!--      </div>-->
<!--      <div slot="footer">-->
<!--        <Button type="info" size="large" long :loading="restoreLoading" @click="restorePermission()">بازگرداندن</Button>-->
<!--      </div>-->
<!--    </Modal>-->
<!--    <Modal v-model="updateModal" :title="` ویرایش نقش ${permission.title}` " width="500" footer-hide>-->
<!--      <Form ref="formValidate" :model="formValidate" :rules="ruleValidate" label-position="top">-->
<!--        <div class="col-12">-->
<!--          <FormItem label="عنوان" prop="title">-->
<!--            <Input v-model="formValidate.title" type="text" placeholder="عنوان را وارد کنید..."></Input>-->
<!--          </FormItem>-->
<!--        </div>-->
<!--        <div class="col-12">-->
<!--          <FormItem>-->
<!--            <Button type="primary" :loading="updateLoading" @click="updatePermission('formValidate')">ویرایش اطلاعات</Button>-->
<!--          </FormItem>-->
<!--        </div>-->
<!--      </Form>-->
<!--    </Modal>-->
  </ListItem>
</template>

<script>
export default {
  name : 'user-permissio-list-item' ,
  props : ['permission'] ,
  // data () {
  //   return {
  //     deleteModel : false ,
  //     deleteLoading : false ,
  //     updateModal : false ,
  //     updateLoading : false,
  //     restoreModal : false,
  //     restoreLoading : false,
  //     statusLoading : false,
  //     status  : this.permission.pre_defined,
  //     formValidate: {
  //       title: '',
  //     },
  //     ruleValidate: {
  //       title: [
  //         { required: true, message: 'یک عنوان وارد کنید', trigger: 'change' }
  //       ]
  //     },
  //   }
  // },
  // methods : {
  //   removePermission() {
  //     this.deleteLoading = true
  //     this.$store.dispatch('admin/user/permission/removeUserPermission', {id: this.permission.id})
  //       .then(response => {
  //         this.deleteLoading = false
  //         this.$Message.warning(response.data.message);
  //         this.deleteModel = false
  //       })
  //       .catch(error => {
  //         this.deleteLoading = false
  //         this.$Message.error(error.message);
  //       })
  //   },
  //   restorePermission() {
  //     this.restoreLoading = true
  //     this.$store.dispatch('admin/user/permission/restoreUserPermission', {id: this.permission.id})
  //       .then(response => {
  //         this.restoreLoading = false
  //         this.$Message.warning(response.data.message);
  //         this.restoreModal = false
  //       })
  //       .catch(error => {
  //         this.restoreLoading = false
  //         this.$Message.error(error.message);
  //       })
  //   },
  //   openUpdateModal() {
  //     this.updateModal = true
  //     this.formValidate.title = this.permission.title
  //   },
  //   updatePermission(name) {
  //     this.$refs[name].validate((valid) => {
  //       if (valid) {
  //         this.updateLoading = true
  //         this.$store.dispatch('admin/user/permission/updateUserPermission', {
  //           id: this.permission.id,
  //           form: this.formValidate
  //         })
  //           .then(response => {
  //             this.updateModal = false
  //             this.updateLoading = false
  //             this.$Message.success(response.data.message);
  //           })
  //           .catch(error => {
  //             this.$Message.error(error.response.data.message);
  //             this.updateLoading = false
  //           })
  //           .finally(()=> this.updateLoading  = false)
  //       } else {
  //         this.$Message.error('ابتدا خطاهای خود را بررسی کنید.');
  //       }
  //     })
  //   },
  //   changePermissionStatus() {
  //     this.statusLoading = true
  //     this.$store.dispatch('admin/user/permission/updateUserPermissionStatus' , {
  //       id : this.permission.id,
  //       form : {
  //         status : this.status
  //       }
  //     })
  //     .then(response => {
  //       this.statusLoading = false
  //       this.$Message.success(response.data.message)
  //     }).catch(erro => {
  //       this.$Message.error(erro.message)
  //       this.statusLoading =false
  //       console.log(erro)
  //     })
  //
  //   }
  // }
}
</script>
