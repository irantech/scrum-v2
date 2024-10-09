<template>

  <ListItem item-layout="vertical" class="user-list" :class="{'bg-gray' : user.trashed}">

    <ListItemMeta :title="`( ${user.role.title} ) ${user.name}`" :avatar="`${user.avatar ? $env.UPLOAD_URL+ user.avatar : ''}`"
                  :description="user.email" />
    <div class="ml-4" v-if="user.signature">
      <img :src="user.signature" style="width: 130px ; height: 130px">
    </div>

    <template slot="action">
        <li v-if="user.id != this.$store.state.auth.user.id && $store.getters['auth/can']('force-login')">
          <Button @click="loginUser" icon="md-done-all" type="success" shape="square"></Button>
        </li>
        <li class="text-success" v-if="!user.trashed && $store.getters['auth/can']('update-user')">
          <a @click="openUpdateModal" >
            ویرایش
          </a>
        </li>
        <li class="text-info" v-if="user.trashed && $store.getters['auth/can']('restore-user')">
          <a @click="restoreModal = true">
            بازگردانی
          </a>
        </li>
        <li class="text-danger" v-if="!user.trashed && $store.getters['auth/can']('delete-user')">
          <a @click="deleteModel = true">
            حذف
          </a>
        </li>
      </template>

    <Modal v-model="deleteModel" width="360">
      <p slot="header" style="color:#f60;text-align:center">
        <Icon type="ios-information-circle"></Icon>
        <span>حذف کاربر {{user.name}}</span>
      </p>
      <div style="text-align:center">
        <p>آیا از حذف این کاربر اطمینان دارید؟</p>
      </div>
      <div slot="footer">
        <Button type="error" size="large" long :loading="deleteLoading" @click="removeUser()">حذف</Button>
      </div>
    </Modal>
    <Modal v-model="restoreModal" width="360">
      <p slot="header" style="color:#17a2b8;text-align:center">
        <Icon type="ios-information-circle"></Icon>
        <span>بازگرداندن کاربر {{ user.name }}</span>
      </p>
      <div style="text-align:center">
        <p>آیا از بازگرداندن این کاربر اطمینان دارید؟</p>
      </div>
      <div slot="footer">
        <Button type="info" size="large" long :loading="restoreLoading" @click="restoreUser()">بازگرداندن</Button>
      </div>
    </Modal>
    <Modal v-model="updateModal" :title="` ویرایش کاربر ${user.name}` " width="800" footer-hide>
      <Form ref="formValidate" :model="formValidate" :rules="ruleValidate" label-position="top">
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
            <Button type="primary" :loading="updateLoading" @click="handleUpdate('formValidate')">ثبت اطلاعات</Button>
          </FormItem>
        </div>
      </Form>
    </Modal>
  </ListItem>
</template>

<script>
  export default {
    name : 'user-list-item' ,
    props : ['user' , 'roleList'] ,
    data(){
      return {
        deleteModel : false ,
        deleteLoading  : false ,
        restoreModal : false ,
        restoreLoading : false,
        updateModal  : false ,
        updateLoading  : false,
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
      removeUser() {
        this.deleteLoading = true
        this.$store.dispatch('admin/user/removeUser', {id: this.user.id})
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
      restoreUser(){
        this.restoreLoading = true
        this.$store.dispatch('admin/user/restoreUser', {id: this.user.id})
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
      openUpdateModal(){
        this.updateModal = true
        this.formValidate.name = this.user.name
        this.formValidate.userName = this.user.userName
        this.formValidate.email = this.user.email
        this.formValidate.role = this.user.role.id
        this.formValidate.password = ''
      },
      handleUpdate(name) {
          this.$refs[name].validate((valid) => {
            if (valid) {
              this.updateLoading = true
              this.$store.dispatch('admin/user/updateUser', {
                id: this.user.id,
                form: this.formValidate
              })
                .then(response => {
                  this.updateModal = false
                  this.updateLoading = false
                  this.$Message.success(response.data.message);
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
      },
      loginUser() {
            this.loginLoading = true
            this.$axios.get(`force-login/${this.user.id}`)
              .then(response => {
                this.$cookies.set('token', response.data.success.token , {
                  path: '/',
                  maxAge: 60 * 60 * 24 * 7
                })
                this.$Message.success('خوش آمدید');
                this.$store.dispatch('auth/getLoggedInUser')
                this.$router.push('/admin')
              })
              .catch(error => {
                this.$cookies.remove('token')
                this.$cookies.remove('role')
                this.$Message.error(error.response.data.error);
                this.$Message.success('خطا , عدم دسترسی');
              });
        }
      }
  }
</script>

<style>
.bg-gray{
  background: #eee;
}
</style>
