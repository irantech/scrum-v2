<template>
  <Row>
    <Col span="24" :md="{span : 12}" :lg="{span : 8}" class="login m-auto mt-3 px-3">
      <Card>
        <p slot="title" class="d-flex align-items-center">
          <i class="fal fa-sign-in-alt ml-1"></i>
           ورود
        </p>
        <Form ref="formInline" :model="formInline" :rules="ruleInline"
              @submit.native.prevent="do_login('formInline')">
          <FormItem prop="user">
            <Input type="text" v-model="formInline.email" placeholder="Username">
              <Icon type="ios-person-outline" slot="prepend"></Icon>
            </Input>
          </FormItem>
          <FormItem prop="password">
            <Input type="password" v-model="formInline.password" placeholder="Password">
              <Icon type="ios-lock-outline" slot="prepend"></Icon>
            </Input>
          </FormItem>
          <FormItem>
            <Button type="primary" html-type="submit" :loading="loginLoading">ورود</Button>
          </FormItem>
        </Form>
      </Card>
    </Col>
  </Row>

</template>

<script>
export default {
  name : 'login' ,
  data () {
    return {
      formInline: {
        email: '',
        password: '' ,
        firstLoggin: false,
      },
      ruleInline: {
        email: [
          { required: true, message: 'لطفا ایمیل را وارد کنید.', trigger: 'blur' }
        ],
        password: [
          { required: true, message: 'لطفا پسورد وارد کنید. .', trigger: 'blur' },
          { type: 'string', min: 6, message: 'پسورد نمیتواند کمتر از 6 کاراکتر باشد', trigger: 'blur' }
        ]
      },
      loginLoading : false
    }
  },
  methods: {

    do_login(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.loginLoading = true
          this.$axios.post('login', {
              email: this.formInline.email,
              password: this.formInline.password,
            })
            .then(response => {
              this.$cookies.set('token', response.data.success.token , {
                path: '/',
                maxAge: 60 * 60 * 24 * 7
              })
	            this.loginLoading = false
              this.$store.dispatch('auth/getLoggedInUser')
              this.$Message.success('خوش آمدید');
              this.$router.push('/admin')
              this.firstLoggin = true;
            })
            .catch(error => {
	            this.loginLoading = false
              this.$cookies.remove('token')
              this.$cookies.remove('role')
              this.$Message.error(error.response.data.error);
              console.log(error);
            });
        } else {
          this.$Message.error('Fail!');
        }
      })
    },
    checkRole(token) {
      this.$axios.get('current-role', {headers: {Authorization: `Bearer ${token}`}})
        .then(response => {
          this.$cookies.set('role', response.data , {
            path: '/',
            maxAge: 60 * 60 * 24 * 7
          })
          //localStorage.setItem('role', response.data);
          if (this.firstLoggin) {
            this.firstLoggin = false;
          }
        })
        .catch(error => {
          console.log(error);
          this.$cookies.remove('role')
         // localStorage.removeItem('role');
        });
    },
  }
}
</script>
