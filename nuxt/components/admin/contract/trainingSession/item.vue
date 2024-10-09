<template>
  <div>
    <ListItem class="border-bottom">
      <ListItemMeta :title="trainingSessionTitle"
                    :description="trainingSessionDesc" />

      <p v-if="trainingSession.status == 'done'">
        این جلسه
        <span>{{trainingSession.duration}}</span>
        طول کشیده
      </p>
      <p v-else-if="trainingSession.status == 'cancel'">
          این جلسه به دلیل
          <span>"{{trainingSession.cancel_reason}}"</span>
          کنسل شد.
      </p>
      <p v-else-if="trainingSession.status == 'new_session'">
        این جلسه به روز دیگری موکول شد.
      </p>
      <template  #action>
        <li v-if="trainingSession.status == 'set_time'">
          <ButtonGroup v-if="$store.getters['auth/can']('show-training-session')">

            <Button type="success" @click="updateModal = !updateModal">انجام شد</Button>
            <Button type="primary" @click="cancelModal = !cancelModal">لغو شد</Button>
            <Button type="warning" @click="deleteModel = true" >زمان دیگری</Button>
          </ButtonGroup>
        </li>
      </template>
    </ListItem>

    <Modal v-model="updateModal" title="ثبت اطلاعات جلسه برگزار شده" width="950" footer-hide>
      <Form ref="formValidate" :model="formValidate" label-position="top" :rules="ruleValidate">
        <div class="row">
          <div class="col-6">
            <FormItem label="مدت زمان آموزش" prop="duration">
              <TimePicker format="HH:mm" placeholder="Select time" v-model="formValidate.duration"></TimePicker>
            </FormItem>
          </div>

          <div class="col-12 border my-1 mb-3 position-relative" v-for="(contributor , index) in formValidate.contributors" :key="index">
            <div class="row">
              <div class="col-6">
                <FormItem label="نام نماینده"
                          :key="index"
                          :prop="'contributors.' + index + '.name'"
                          :rules="{required: true, message: 'وارد کردن نام نماینده اجباری است.'}">
                  <Input v-model="contributor.name" placeholder="نام نماینده"></Input>
                </FormItem>
              </div>
              <div class="col-6">
                <FormItem label="شماره موبایل نماینده"
                          :prop="'contributors.' + index + '.mobile'"
                          :rules="{required: true, message: 'وارد کردن شماره موبایل نماینده اجباری است.', trigger: 'change'}">
                  <Input v-model="contributor.mobile" placeholder="شماره موبایل نماینده"></Input>
                </FormItem>
              </div>
              <div class="col-12">
                <FormItem label="لینک شبکه اجتماعی اول" prop="social_link1">
                  <Input v-model="contributor.social_link1" placeholder="لینک شبکه اجتماعی اول"></Input>
                </FormItem>
              </div>
              <div class="col-12">
                <FormItem label="لینک شبکه اجتماعی دوم" prop="social_link2">
                  <Input v-model="contributor.social_link2" placeholder="لینک شبکه اجتماعی دوم"></Input>
                </FormItem>
              </div>
            </div>

            <Button type="error" @click="deleteContributor(index)" class="position-absolute" style="top: -15px ; left: 10px">حذف این نماینده</Button>
          </div>
          <Button type="success" @click="addNewContributor" class="mr-2">افزودن نماینده</Button>

          <div class="col-12">
            <FormItem class="float-left">
              <Button type="primary" :loading="processLoading"
                      @click="setTrainingSessionTime( 'formValidate')">ثبت اطلاعات</Button>
            </FormItem>
          </div>
        </div>
      </Form>
    </Modal>

    <Modal v-model="cancelModal" title="لغو زمان جلسه آموزش" width="950" footer-hide>
      <Form ref="cancelValidate" :model="cancelValidate" label-position="top" :rules="ruleCancel">
        <div class="row">
          <div class="col-12">
            <FormItem label="دلیل کنسلی" prop="cancel_reason">
              <Input  v-model="cancelValidate.cancel_reason" placeholder="دلیل کنسلی"></Input>
            </FormItem>
          </div>
          <div class="col-12">
            <FormItem>
              <Button type="primary" :loading="cancelLoading"
                      @click="cancel('cancelValidate')">ثبت اطلاعات</Button>
            </FormItem>
          </div>
        </div>
      </Form>
    </Modal>

    <Modal v-model="deleteModel" width="360">
      <p slot="header" style="color:#f60;text-align:center">
        <Icon type="ios-information-circle"></Icon>
        <span>ثبت جلسه جدید</span>
      </p>
      <div style="text-align:center">
        <p>آیا از ثبت جلسه دیگری اطمینان دارید؟</p>
      </div>
      <div slot="footer">
        <Button type="error" size="large" long :loading="deleteLoading" @click="addNewSession()">بله</Button>
      </div>
    </Modal>

  </div>
</template>

<script>
export  default  {
  name : 'training-session-item' ,
  props : ['trainingSession' , 'checklistContract'] ,
  data() {
    return {
      processLoading : false,
      deleteLoading : false ,
      cancelLoading : false,
      updateModal : false ,
      cancelModal : false ,
      deleteModel : false,
      formValidate : {
        duration : '' ,
        contributors : [{
          name : '' ,
          mobile: '' ,
          social_link1 :'' ,
          social_link2 : ''
        }],
      },
      ruleValidate: {
        duration: [
          { required: true,pattern:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/ ,  message: 'مدت زمان انجام کار را وارد کنید', trigger: 'change' }
        ],
      },
      cancelValidate : {
        cancel_reason : '' ,
      },
      ruleCancel: {
        cancel_reason: [
          { required: true, type : 'string' ,  message: 'دلیل کنسلی را وارد کنید.' , trigger: 'change' }
        ],
      },
    }
  },
  computed : {
    trainingSessionTitle() {
      return `برگزار کننده : ${this.trainingSession.user.name}`
    },
    trainingSessionDesc() {
        return `در روز ${this.trainingSession.session_date} راس ساعت ${this.trainingSession.session_time} به صورت  ${this.trainingSession.location_status == 'online'  ? 'آنلاین'   : 'حضوری'}  ${this.trainingSession.address ? 'به آدرس ' +  this.trainingSession.address   : ''} برگزار میشود.` ;
    }
  },
  methods : {
    handleReset (name) {
      this.$refs[name].resetFields();
    },
    setTrainingSessionTime(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.processLoading = true
          this.formValidate.status  = 'done'
          this.$axios.put(`trainingSession/${this.trainingSession.id}`, this.formValidate )
            .then(response => {
              this.trainingSession.duration = response.data.data.duration
              this.trainingSession.status = 'done'
              this.$Message.success(response.data.message);
              this.updateModal = false
              this.handleReset(name)
              this.$store.commit('auth/CHANGE_TRAINING_SESSION_COUNT' ,this.trainingSession.status )
            }).catch(error=>  {
            if(error.response.status === 403)
            {
              this.$Message.error(error.response.data.message)
            }
            else if(error.response.data.errors) {
              let errors = error.response.data.errors;
              errors.forEach(error => {
                this.$Message.error(error)
              })
            }
            else
              this.$Message.error(error.response);
          }).finally(()=> this.processLoading = false)
        }
        else {
          this.$Message.error('ابتدا خطاهای خود را بررسی کنید.');
        }

      })
    },
    cancel(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.cancelLoading          = true
          this.cancelValidate.status  = 'cancel'
          this.cancelValidate.checklist_contract_id  = this.checklistContract.id
          this.$axios.put(`trainingSession/${this.trainingSession.id}`, this.cancelValidate )
            .then(response => {
              this.trainingSession.status        = 'cancel'
              this.trainingSession.cancel_reason = this.cancelValidate.cancel_reason
              this.$Message.success(response.data.message);
              this.$store.commit('auth/CHANGE_TRAINING_SESSION_COUNT' ,this.trainingSession.status )
              this.cancelModal = false
              this.handleReset(name)
            }).catch(error=>  {
              if(error.response.status === 403)
              {
                this.$Message.error(error.response.data.message)
              }
              else if(error.response.data.errors) {
                let errors = error.response.data.errors;
                errors.forEach(error => {
                  this.$Message.error(error)
                })
              }
              else
                this.$Message.error(error.response);
            }).finally(()=> this.cancelLoading = false)
        }
        else {
          this.$Message.error('ابتدا خطاهای خود را بررسی کنید.');
        }

      })
    },
    addNewSession() {
      this.deleteLoading = true
      let form   = {}
      form.status = 'new_session'
      this.$axios.put(`trainingSession/${this.trainingSession.id}`, form )
        .then(response => {
          this.trainingSession.status        = 'new_session'
          this.$Message.success(response.data.message);
          this.$store.commit('auth/CHANGE_TRAINING_SESSION_COUNT' ,this.trainingSession.status )
          this.deleteModel = false
          this.handleReset(name)
        }).catch(error=>  {
          if(error.response.status === 403)
          {
            this.$Message.error(error.response.data.message)
          }
          else if(error.response.data.errors) {
            let errors = error.response.data.errors;
            errors.forEach(error => {
              this.$Message.error(error)
            })
          }
          else
            this.$Message.error(error.response);
        }).finally(()=> {
          this.deleteLoading = false
          this.$emit('openNewModal')
         })
    },
    addNewContributor() {
      let contributor = {
          name : '' ,
          mobile: '' ,
          social_link1 :'' ,
          social_link2 : ''
        }
      this.formValidate.contributors.push(contributor)
      },
    deleteContributor(index){
      this.formValidate.contributors.splice(index , 1)
      if(this.formValidate.contributors.length == 0 ){
        this.addNewContributor()
      }
    }
  },

}
</script>
