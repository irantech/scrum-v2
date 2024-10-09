<template>
    <Card>
      <Alert type="warning" class="text-center" v-if="!user.avatar || !user.signature">قراردادن پروفایل و ثبت امضا الزامی است.</Alert>
      <div class="row">
        <div class="col-12 col-md-3 border-left justify-content-center d-flex">
          <div class="demo-avatar">
            <img v-if="user.avatar" :src="`${$env.UPLOAD_URL}/${user.avatar}`" class="rounded mb-2" alt="avatar" width="150" height="150">
            <img v-else src="~/assets/images/avatar.png" class="rounded mb-2" alt="avatar" width="150" height="150" >
            <div>
              <Upload
                name="upload" :on-success="uploadAvatar"
                :action="`${$env.BASE_URL}upload`">
                <Button icon="ios-cloud-upload-outline">انتخاب عکس</Button>
              </Upload>
              <Button type="success" @click="upload" :loading="avatarLoading">{{ avatarLoading ? 'در حال آپلود' : 'آپلود' }}</Button>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-9">
          <div class="row my-3">
            <div class="col-4 col-md-2">
              نام :
            </div>
            <div class="col-8 col-md-10" v-if="showName">
              {{user.name}}
              <a class="text-info" @click="openName">
                <i class="fa fa-edit" />
              </a>
            </div>
            <div class="col-8 col-md-10" v-else>
              <Form class="d-flex align-items-center"  @submit.native.prevent="updateName">
                <Input type="text" v-model="form.name" class="col-8" ref="input"/>
                <a class="text-danger pl-2" @click="showName = true"><i class="fas fa-window-close"></i></a>
                <a class="text-info" @click="updateName"><i class="fa fa-check-square"/></a>
              </Form>
            </div>
          </div>
          <div class="row my-3">
            <div class="col-4 col-md-2">
              نام کاربری :
            </div>
            <div class="col-8 col-md-10" v-if="showUserName">
              {{user.userName}}
              <a class="text-info" @click="openUserName">
                <i class="fa fa-edit" />
              </a>
            </div>
            <div class="col-8 col-md-10" v-else>
              <Form class="d-flex align-items-center" @submit.native.prevent="updateUserName">
                <Input type="text" class="col-8" v-model="form.userName" />
                <a class="text-danger pl-2" @click="showUserName = true"><i class="fas fa-window-close"></i></a>
                <a class="text-info" @click="updateUserName"><i class="fa fa-check-square"/></a>
              </Form>
            </div>
          </div>
          <div class="row my-3">
            <div class="col-4 col-md-2">
              ایمیل :
            </div>
            <div class="col-8 col-md-10" v-if="showEmail">
              {{user.email}}
              <a class="text-info" @click="openEmail">
                <i class="fa fa-edit" />
              </a>
            </div>
            <div class="col-8 col-md-10" v-else>
              <Form class="d-flex align-items-center" @submit.native.prevent="updateEmail">
                <Input type="text" class="col-8" v-model="form.email" />
                <a class="text-danger pl-2" @click="showEmail = true"><i class="fas fa-window-close"></i></a>
                <a class="text-info" @click="updateEmail"><i class="fa fa-check-square"/></a>
              </Form>
            </div>
          </div>
          <div class="row my-3">
            <div class="col-4 col-md-2">
              موبایل :
            </div>
            <div class="col-8 col-md-10" v-if="showMobile">
              {{user.mobile}}
              <a class="text-info" @click="openMobile">
                <i class="fa fa-edit" />
              </a>
            </div>
            <div class="col-8 col-md-10" v-else>
              <Form class="d-flex align-items-center" @submit.native.prevent="updateMobile">
                 <Input type="text" v-model="form.mobile" class="col-8" />
                <a class="text-danger pl-2" @click="showMobile = true"><i class="fas fa-window-close"></i></a>
                <a class="text-info" @click="updateMobile"><i class="fa fa-check-square"/></a>
              </Form>
            </div>
          </div>
          <div class="row my-3">
            <div class="col-4 col-md-2">
              پسورد :
            </div>
            <div class="col-8 col-md-10">
              ********
              <a class="text-info" @click="openModal = true">
                <i class="fa fa-edit" />
              </a>
            </div>
          </div>
          <div class="row my-3">
            <div class="col-12 col-md-6" v-if="!showUpload">
              <div class="text-center alert-info p-1 my-1 rounded">امضای خود را در این بخش وارد کنید.</div>
              <VueSignaturePad height="300px" ref="signaturePad" class="border"  />
              <div class="mt-2">
                <Button type="success" :loading="signatureLoading" @click="save">ذخیره</Button>
                <Button type="warning"  @click="undo">برگشت</Button>
                <Button type="error"  @click="clear">پاک کردن</Button>
                یا
                <Button type="info"  @click="showUpload = !showUpload">آپلود کنید</Button>
              </div>
            </div>
            <div class="col-12 col-md-6" v-else>
              <div class="text-center alert-info p-1 my-1 rounded">امضای خود را در این بخش آپلود کنید.</div>
              <div class="mt-2">
                <div class="border p-3 position-relative signiture-box d-flex justify-content-center align-items-center">
                  <Icon type="ios-close-circle"
                        class="position-absolute text-danger close-signiture" @click="showUpload = !showUpload"/>
                  <Upload
                    name="upload" :on-success="uploadSign"
                    :action="`${$env.BASE_URL}upload`">
                    <Button icon="ios-cloud-upload-outline">انتخاب عکس امضای شما</Button>
                  </Upload>
                </div>
                <div class="mt-2">
                  <Button type="success" @click="uploadSigniture" :loading="avatarLoading">{{ avatarLoading ? 'در حال آپلود' : 'آپلود' }}</Button>
                </div>

              </div>
            </div>
            <div class="col-12 col-md-6">
              <div class="text-center alert-success p-1 my-1 rounded">امضای ثبت شده شما</div>
              <div class="border signiture-box d-flex justify-content-center align-items-center">
                <img :src="user.signature" alt="signature" width="200" height="200">
              </div>
            </div>

          </div>
        </div>
      </div>

      <Modal v-model="openModal" title="تغییر پسورد" width="500" footer-hide>
        <Form ref="formValidate" label-position="top" @submit.native.prevent="updatePassword('formValidate')">
          <div class="col-12">
            <FormItem label="پسورد" prop="password">
              <Input v-model="form.password" type="password" placeholder="پسورد جدید را وارد کنید..."></Input>
            </FormItem>
          </div>
          <div class="col-12">
            <FormItem label="تکرار پسورد" prop="language">
              <Input v-model="form.passwordAgain" type="password" placeholder="تکرار پسورد را وارد کنید..."></Input>
            </FormItem>
          </div>
          <div class="col-12">
            <FormItem>
              <Button type="primary" :loading="changeLoading" @click="updatePassword('formValidate')">ویرایش اطلاعات</Button>
            </FormItem>
          </div>
        </Form>
      </Modal>


    </Card>
</template>

<script>
  import VueSignaturePad from "vue-signature-pad"
  export  default {
    name : 'profile-info',
    props : ['user'] ,
    components:{
      VueSignaturePad
    },
    data () {
      return {
        showUpload : false ,
        changeLoading: false,
        openModal : false,
        showMobile: true,
        showName : true,
        showUserName: true,
        showEmail : true ,
        form :{
          name : '' ,
          email : '',
          userName : '',
          mobile : '',
          password : '',
          passwordAgain : ''
        },
        signImage : '' ,
        uploadedSign : '',
        params : {
          dateField : '' ,
          newData : ''
        },
        signatureLoading : false,
        avatarLoading : false ,
        file :'',
        uploaded : '',
      }
    },
    methods: {
      undo() {
        this.$refs.signaturePad.undoSignature();
      },
      save() {
        this.signatureLoading = true
        const { isEmpty, data } = this.$refs.signaturePad.saveSignature();
        if(!isEmpty) {
          this.$store.dispatch('auth/uploadProfileSignature' , {
            signature : data
          }).then(response=>{
            this.$Message.success(response.data.message)
          }).catch(err=>{this.$Message.error(err.response.data.message)})
          .finally(()=>this.signatureLoading = false)

        }
        else {
          this.$Message.error('ابتدا امضای خود را طراحی کنید')
        }
      },
      clear(){
        this.$refs.signaturePad.clearSignature();
      },
      uploadAvatar(response) {
        this.uploaded = response.picName
        this.image = response.url
      },
      upload() {
        this.avatarLoading = true
        this.$store.dispatch('auth/uploadProfileAvatar' , {
          avatar : this.uploaded
        }).then(response => {
          this.avatarLoading = false
          this.$Message.success(response.data.message)
        }).catch(error => {
          if(error.response.status === 422 ) {
            let errors = error.response.data.error.message
            errors.forEach(error => {
              this.$Message.error(error)
            })
          }
          this.avatarLoading = false
        })
      } ,
      updatePassword() {
        if(this.form.password != ''){
          if(this.form.password === this.form.passwordAgain){
            this.changeLoading = true
            this.params.dateField = 'password'
            this.params.newData = this.form.password
            this.updateData()
          }
          else {
            this.$Message.error('پسورد و تکرار آن با هم همخوانی ندارد.')
          }
        }
        else {
          this.$Message.error('ابتدا پسورد و تکرار آن را وارد کنید')
        }

      },
      updateName() {
        if(this.form.name !== ''){
          this.params.dateField = 'name'
          this.params.newData = this.form.name
          this.updateData()
        }
      },
      updateEmail() {
        if(this.form.email !== ''){
          const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
          let isEmail =  re.test(this.form.email);
          if(isEmail) {
            this.params.dateField = 'email'
            this.params.newData = this.form.email
            this.updateData()
          }
          else {
            this.$Message.error('فرمت ایمیل نادرست است')
          }
        }
      },
      updateUserName() {
        if(this.form.userName !== ''){
          this.params.dateField = 'userName'
          this.params.newData = this.form.userName
          this.updateData()
        }
      },
      updateMobile() {
        if(this.form.mobile !== ''){
          const re = /^(0|0098|\+98)9(0[1-5]|[1 3]\d|2[0-2]|98)\d{7}$/;
          let isMobile =  re.test(this.form.mobile);
          if(isMobile) {
            this.params.dateField = 'mobile'
            this.params.newData = this.form.mobile
            this.updateData()
          }
          else {
            this.$Message.error('فرمت موبایل نادرست است')
          }
        }
      },
      updateData() {
        this.$store.dispatch('auth/updateUserData' , { params : this.params})
          .then(response=> {
            this.$Message.success(response.data.message)
            this.resetData()
          }).catch(error => {
            this.changeLoading = false
            let errors = error.response.data.errors
            errors.forEach(error => {
              this.$Message.error(error)
            })
        })
      } ,
      openUserName() {
        this.showUserName = false
        this.form.userName = this.user.userName
      },
      openName() {
        this.showName = false
        this.form.name = this.user.name
      },
      openMobile() {
        this.showMobile = false
        this.form.mobile = this.user.mobile
      },
      openEmail() {
        this.showEmail = false
        this.form.email = this.user.email
      },
      resetData() {
        this.showEmail = true
        this.showUserName = true
        this.showMobile = true
        this.showName = true
        this.changeLoading = false
        this.openModal = false
      } ,
      uploadSign(response){
        this.uploadedSign = response.picName
        this.signImage = response.url
      },
      uploadSigniture() {
        this.signatureLoading = true
          this.$store.dispatch('auth/uploadProfileSignature' , {
            signature : this.signImage
          }).then(response=>{
            this.$Message.success(response.data.message)
          }).catch(err=>{this.$Message.error(err.response.data.message)})
            .finally(()=>this.signatureLoading = false)
      }
    } ,

  }
</script>
