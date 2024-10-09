<template>
  <div>
    <div class="col-12 col-md-9 m-auto">
      <Card>
        <p slot="title">
          لیست سطح دسترسی ها
        </p>
<!--        <p slot="extra" >-->
<!--          <Button type="success" @click="openCreateModal">-->
<!--            افزودن سطح دسترسی جدید-->
<!--          </Button>-->
<!--        </p>-->
        <List>
          <adminUserPermissionItem v-for="(permission , index) in permissionList" :key="index" :permission="permission"/>
        </List>
      </Card>
    </div>
<!--    <Modal v-model="createModal" title="نقش جدید" width="500" footer-hide>-->
<!--      <Form ref="formValidate" :model="formValidate" :rules="ruleValidate" label-position="top">-->
<!--        <div class="col-12">-->
<!--          <FormItem label="عنوان" prop="title">-->
<!--            <Input v-model="formValidate.title" type="text" placeholder="عنوان را وارد کنید..."></Input>-->
<!--          </FormItem>-->
<!--        </div>-->
<!--        <div class="col-12">-->
<!--          <FormItem>-->
<!--            <Button type="primary" :loading="createLoading" @click="handleSubmit('formValidate')">ثبت اطلاعات</Button>-->
<!--          </FormItem>-->
<!--        </div>-->
<!--      </Form>-->
<!--    </Modal>-->
  </div>
</template>

<script>
export default {
  name : 'user-permission-list',
  props : ['permissionList'] ,
  // data() {
  //   return {
  //     createModal : false,
  //     createLoading : false ,
  //     formValidate : {
  //       title : ''
  //     },
  //     ruleValidate : {
  //       title: [
  //         { required: true, message: 'فیلد عنوان الزامی است.', trigger: 'change' }
  //       ],
  //     }
  //   }
  // },
  // methods : {
  //   openCreateModal() {
  //     this.createModal = true
  //   },
  //   handleSubmit(name) {
  //     this.$refs[name].validate((valid) => {
  //       if (valid) {
  //         this.createLoading = true
  //         this.$store.dispatch('admin/user/permission/createNewUserPermission' , { form : this.formValidate })
  //           .then(response => {
  //               this.$Message.success(response.data.message)
  //               this.createModal = false
  //               this.createLoading = false
  //               this.handleReset(name)
  //             }
  //           )
  //           .catch(error => {
  //             console.log(error)
  //             this.$Message.error(error.message);
  //             this.createLoading = false
  //           })
  //       } else {
  //         this.$Message.error('ابتدا خطاهای خود را بررسی کنید.');
  //       }
  //     })
  //   },
  //   handleReset (name) {
  //     this.$refs[name].resetFields();
  //   }
  // }
}
</script>
