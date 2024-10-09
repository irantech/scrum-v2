<template>
  <Card style="width:100%">

    <template #title>
      جلسه های آموزش
    </template>
    <template v-if="$store.getters['auth/can']('show-training-session')" #extra>
      <Button @click.prevent="createModal = !createModal" type="primary" :disabled="!( (order == 5 && type != 'confirm' )  || order == 6 ) || checkPreviousSessionStatus" >
        <Icon type="ios-loop-strong"></Icon>
        ثبت جلسه آموزش
      </Button>
    </template>

    <adminContractTrainingSessionList :checklistContract="checklistContract" :trainingSessionList="trainingSessionList" @openNewSessionModal="createModal = true" />
    <Modal v-model="createModal" title="ثبت جلسه آموزش" width="950" footer-hide>
      <Form ref="formValidate" :model="formValidate" label-position="top" :rules="ruleValidate">
        <div class="row">
          <div class="col-6">
            <FormItem label="روز آموزش" prop="session_date">
              <client-only>
                <date-picker auto-submit format="jYYYY-jMM-jDD"
                             clearable v-model="formValidate.session_date"
                             placeholder="1400-5-1"/>
              </client-only>
            </FormItem>
          </div>
          <div class="col-6">
            <FormItem label="ساعت آموزش" prop="session_time">
              <TimePicker format="HH:mm" placeholder="Select time" v-model="formValidate.session_time"></TimePicker>
            </FormItem>
          </div>
          <div class="col-6">
            <FormItem label="همکار محترم" prop="user_id">
              <Select v-model="formValidate.user_id" placeholder="همکار محترم">
                <Option v-for="user in userList" :key="user.id" :value="user.id" v-if="user.role.section && user.role.section.id == 4">{{ user.userName }}</Option>
              </Select>
            </FormItem>
          </div>
          <div class="col-6">
            <FormItem label="حضوری/آنلاین" prop="location_status">
              <Select v-model="formValidate.location_status" placeholder="حضوری/آنلاین">
                <Option value="0">آنلاین </Option>
                <Option value="1">حضوری</Option>
              </Select>
            </FormItem>
          </div>
          <div class="col-6">
            <FormItem label="مکان برگزاری" prop="location_place">
              <Select v-model="formValidate.location_place" placeholder="مکان برگزاری"
                      :disabled="formValidate.location_status == 0 "
                      :rules="{required: formValidate.location_status == 1 ? true : false , message: 'وارد کردن آدرس اجباری است.'}">
                <Option value="0">ایران تکنولوژی </Option>
                <Option value="1">مکان مشتری</Option>
              </Select>
            </FormItem>
          </div>
          <div class="col-12" >
            <FormItem label="آدرس"
                      prop="address"
                      :rules="{required: formValidate.location_status == 1 ? true : false , message: 'وارد کردن آدرس اجباری است.'}">
              <Input :disabled="formValidate.location_status == 0 " v-model="formValidate.address" placeholder="آدرس"></Input>
            </FormItem>
          </div>
          <div class="col-12">
            <FormItem>
              <Button type="primary" :loading="processLoading"
                      @click="setTrainingSession( 'formValidate')">ثبت اطلاعات</Button>
            </FormItem>
          </div>
        </div>
      </Form>
    </Modal>
  </Card>
</template>
<script>
import {mapState} from "vuex";
export default {
  name : 'training-session-form',
  props : ['checklistContract' , 'order' , 'type'],
  data () {
    return {
      trainingSessionList : [] ,
      createModal : false ,
      formValidate : {
        user_id : '' ,
        session_date : '' ,
        session_time : '' ,
        location_status : '',
        location_place : '',
        address : ' ',
        checklist_contract_id: ''
      },
      ruleValidate: {
        user_id: [
          { required: true, type : 'integer', message: 'لطفا یک همکار انتخاب کنید.', trigger: 'change' }
        ],
        session_date: [
          { required: true, message: 'روز برگزاری جلسه را تعیین کنید.' , trigger: 'change' }
        ],
        location_status: [
          { required: true, message: 'ساعت برگزاری جلسه را تعیین کنید.', trigger: 'change' }
        ],
        session_time: [
          { required: true,pattern:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/ ,  message: 'ساعت برگزاری جلسه را تعیین کنید.', trigger: 'change' }
        ],
      },
      processLoading : false
    }
  },
  methods :{

    setTrainingSession(name) {
      this.$refs[name].validate((valid) => {

        if (valid) {
          this.formValidate.checklist_contract_id = this.checklistContract.id
          this.processLoading = true
          this.$axios.post(`trainingSession`, this.formValidate )
            .then(response => {
              this.trainingSessionList.push(response.data.data)
              this.$Message.success(response.data.message);
              this.createModal = false
              this.handleReset(name)
              this.$store.commit('auth/CHANGE_TRAINING_SESSION_COUNT' , response.data.data.status )
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
    } ,

    handleReset (name) {
      this.$refs[name].resetFields();
    },
    getTrainingSession() {
      this.$axios.get(`trainingSession/${this.checklistContract.id}`)
        .then(response => {
          this.trainingSessionList =  response.data.data
        })
        .catch(error => {
          console.log(error)
        })
    }
  } ,
  watch :{
    'formValidate.location_place' : {
        handler(val) {
          if ( val == 0 ) {
            this.formValidate.address = "تهران - خیابان مطهری - بعد از مفتح - پلاک 180 - واحد 1"
          }else if(val == 1)  {
            this.formValidate.address = this.checklistContract.contract.customer_address
          } else {
            this.formValidate.address = ''
          }
        },
        deep: true
    },
    'formValidate.location_status' : {
      handler(val) {
        if( val == 0 ){
          this.formValidate.location_place = ''
          this.formValidate.address = ''
        }
      },
      deep: true
    },
  },
  mounted() {
    this.$store.dispatch('admin/user/getUserList')
    this.getTrainingSession()
  } ,
  computed : {
    ...mapState('admin/user' , ['userList'])   ,
    checkPreviousSessionStatus () {
      let result =    this.trainingSessionList.find(x => x.status == 'set_time' )
      return result ? true : false
    }
  }
}
</script>
