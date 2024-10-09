<template>
  <div>
    <div v-if="signedBy">
      <img  :src="signedBy.signature" :alt="signedBy.name" width="90" height="90">
      <br>
      <p>{{signedBy.name}}</p>
      <p>{{signedBy.sign_time}}</p>
    </div>
    <Tooltip v-else  content="امضا پشتیبان" placement="top">
      <Button style="border-style: dashed;width: 80px;height: 80px;"
              :disabled="!$store.getters['auth/can']('support-approve-design')"
              @click="openSignModel(section.id)"
      >ثبت امضا</Button>
    </Tooltip>

    <Modal v-model="signModal" title="ثبت امضای مدیر" width="500" footer-hide>
      <div class="d-flex justify-content-center border flex-column align-items-center">
        <img :src="user.signature" alt="sign" width="200">
        <p class="m-2 text-center" >با ثبت امضای شما برای مشتری پیام انجام این مرحله ارسال میشود. شما فقط یکبار میتوانید در این مرحله امضا بزنید. در ثبت امضای خود دقت کنید.</p>
        <Checkbox v-if="$store.getters['auth/can']('disable_checklist_sms')" v-model="formValidate.has_sms">با ارسال اس ام اس</Checkbox>
        <Button class="mb-1" :loading="signLoading" type="primary" @click="signChecklist()">ثبت امضا</Button>
      </div>
    </Modal>
  </div>
</template>

<script>
import {mapState} from "vuex";

export default {
  props : [ 'section' , 'activeSection' , 'sectionOrder' , 'signedSections'],
  name : 'sign-support-section',
  data() {
    return{
      signLoading : false,
      signModal : false ,
      formValidate : {
        duration : '' ,
        description :'' ,
        type : 'supportSign' ,
        has_sms : true
      },
      ruleValidate: {
        description: [
          { required: true, type : 'string' ,  message: 'لطفا توضیحات را وارد کنید.', trigger: 'change' }
        ],
        duration: [
          { required: true,pattern:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/ ,  message: 'مدت زمان انجام کار را وارد کنید', trigger: 'change' }
        ],
      },
    }
  },
  methods : {
    signChecklist() {
      this.formValidate.section = this.sectionId
      this.formValidate.description  = 'test'
      this.signLoading = true
      this.$axios.post(`process/checklist-contract/${this.$route.params.id}`,
        this.formValidate)
        .then(response => {
          this.$store.commit('admin/checklistContract/SET_SECTION_SIGNED' , {user : this.user , section : this.section , status : 'support'})
          this.$Message.success(response.data.message);
          // this.$emit('setType' , 'managerConfirm')
          this.signModal = false
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
      }).finally(()=> this.signLoading = false)
    },
    openSignModel(id) {
      this.signModal = true
      this.sectionId = id
    },
  } ,
  computed : {
    ...mapState('auth' , ['user']) ,
    signedBy() {
      return  this.$store.getters['admin/checklistContract/getSignedBySection'](this.section.id , 6)
    }
  }

}
</script>
