<template>
    <div>
      <div class="col-12 col-md-9 m-auto">
        <Card>
          <p slot="title">
            لیست کاربران
          </p>
          <p slot="extra"  v-if="$store.getters['auth/can']('create-user')">
            <Button type="success" @click="openCreateModal">
              افزودن کاربر جدید
            </Button>
          </p>
          <List :loading="userLoading">
            <adminUserItem v-for="(user , index) in userList" :key="index" :user="user" :roleList="roleList"/>
          </List>
        </Card>
      </div>
      <Modal v-model="createModal" title="کاربر جدید" width="800" footer-hide>
        <Form ref="formValidate" :model="formValidate" :rules="ruleValidate" label-position="top"
          @submit.native.prevent="handleSubmit('formValidate')">
          <div class="row">
            <div class="col-6">
              <FormItem label="نام" prop="name">
                <Input v-model="formValidate.name" type="text" placeholder="نام را وارد کنید..."></Input>
              </FormItem>
            </div>
            <div class="col-6">
              <FormItem label="نام کاربری" prop="userName">
                <Input v-model="formValidate.userName" type="text" placeholder="نام کاربری را وارد کنید..."></Input>
              </FormItem>
            </div>

            <div class="col-6">
              <FormItem label="نقش" prop="role">
                <Select v-model="formValidate.role" placeholder="انتخاب کنید">
                  <Option v-for="(role , index) in roleList"  :key="index" :value="role.id" v-if="!role.trashed">{{ role.title }}</Option>
                </Select>
              </FormItem>
            </div>
            <div class="col-6">
              <FormItem label="ایمیل" prop="email">
                <Input v-model="formValidate.email" type="email" placeholder="ایمیل را وارد کنید..."></Input>
              </FormItem>
            </div>
            <div class="col-6">
              <FormItem label="پسورد" prop="password">
                <Input v-model="formValidate.password" type="password" :password="true" placeholder="رمز عبور را وارد کنید..."></Input>
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

  export default  {
    name : 'admin-user-list',
    props : ['userList' , 'roleList' , 'userLoading'] ,
    data() {
      return {
        createModal : false ,
        createLoading : false,
        formValidate : {
          name : '' ,
          userName : '' ,
          role : '' ,
          email : '' ,
          password : ''
        },
        ruleValidate :{
          name : [
            { required: true,  message: 'وارد کردن نام الزامی است.', trigger: 'change' }
          ],
          userName : [] ,
          password : [
            { required: true,  message: 'وارد کردن رمزعبور الزامی است.', trigger: 'change' },
            { type: 'string', min: 6, message: 'حداقل 6 کاراکتر وارد کنید', trigger: 'blur' }
          ] ,
          role  : []  ,
          email : [
            { required: true,  message: 'وارد کردن ایمیل الزامی است.', trigger: 'change' },
            { type: 'email', message: 'فرمت ایمیل نادرست است.', trigger: 'blur' }
          ]
        }
      }
    },
    methods : {
      openCreateModal() {
          this.createModal = true
      } ,
      handleSubmit(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.createLoading = true
            this.$store.dispatch('admin/user/createNewUser' , { form : this.formValidate })
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
