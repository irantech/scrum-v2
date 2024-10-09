<template>
  <div class="my-2 p-2 d-flex flex-column  border position-relative border-radius reply-box"
       :class="reverse.seen == '1'  ? 'bg-light' : ''">
    <div class="d-flex parent-box-mobile">
      <div class="d-flex align-items-center flex-column parent-item-box">
        <Avatar :src="`${$env.UPLOAD_URL}/${reverse.user.avatar}`" class="ml-1" />

      </div>
      <div class="d-flex flex-column w-100">
        <div class="pt-2">
          <div class="d-flex align-items-center parent-box-name-data">
            <p class="input-text-user">
              {{reverse.user.userName}}
            </p>
            <span class="contract-option mr-1 ltr">{{reverse.date}}</span>
          </div>

          <div v-if="editForm">
            <Form ref="formEdit" id="formCustom1" class="pb-3"
                  :model="formEdit" :rules="ruleEdit" @submit.native.prevent="editReply('formEdit')" >
              <div class="d-flex align-items-center parent-box-formCustom1">
                <div class="w-100-me">
<!--                  <FormItem prop="section" class="mb-0" v-if="reverse.parent == null && (activeSection =='3' || activeSection == '2')">-->
<!--                    <RadioGroup v-model="formEdit.section">-->
<!--                      <Radio v-for="(section , index) in section_list" :key="index" :label="section.id">{{section.title}}</Radio>-->
<!--                    </RadioGroup>-->
<!--                  </FormItem>-->
                  <FormItem prop="status" class="mb-0" v-if="reverse.parent == null">
                    <Select placeholder="وضعیت برکشت"  v-model="formEdit.status">
                      <Option v-for="item in type_list" :value="item.id" :key="item.id">{{ item.title }}</Option>
                    </Select>
                  </FormItem>
                  <FormItem prop="status" class="mb-0" v-else>
                    <RadioGroup v-model="formEdit.status">
                      <Radio v-for="(status , index) in status_list" :key="index"
                             :label="status.id">{{status.title}}</Radio>
                    </RadioGroup>
                  </FormItem>
                </div>
                <div class="col-lg-9 col-12">
                  <FormItem prop="body" class="mb-0">
                      <Input type="text" v-model="formEdit.body"></Input>
                      <p class="text-left">
                        <span class="btn-link cursor-pointer" @click="editReply('formEdit')">save</span>
                        or
                        <span @click="editForm = !editForm" class="btn-link cursor-pointer">cancel</span>
                      </p>
                  </FormItem>

                </div>
              </div>
            </Form>
          </div>

          <div v-if="!editForm" class="d-flex">
            <span v-if="reverse.status === 'offer' || reverse.status === 'accept'">
            <Icon style="font-size: 20px" type="md-checkmark-circle-outline" class="text-success ml-1" />
          </span>
            <span v-if="reverse.status === 'error' || reverse.status === 'reject'">
            <Icon style="font-size: 20px" type="ios-close-circle-outline" class="text-danger ml-1" />
            </span>
            <span v-if="reverse.status === 'periodical'">
              <Icon style="font-size: 20px" type="ios-analytics" class="text-danger ml-1" />
          </span>
            {{reverse.order}}-
            <p v-html="$md.render(reverse.body)" class="mb-2 dir-ltr input-text-user"></p>
          </div>
          <adminContractChecklistActionsFile v-for="(file , index) in JSON.parse(reverse.file_list)"
                                             :key="index" :file="file" :reverse="reverse" :process_id="process_id"/>
        </div>
        <Form ref="formCustom" id="formCustom" class="pb-3"
              v-if="$store.state.admin.checklistContract.activeReverse === reverse.id"
              :model="formCustom" :rules="ruleCustom" >
          <div class="d-flex align-items-end parent-box-ripley">
            <div class="parent-radio-btn">
              <FormItem prop="status" class="mb-0">
                <RadioGroup v-model="formCustom.status">
                  <Radio v-for="(status , index) in status_list" :key="index"
                         :label="status.id">{{status.title}}</Radio>
                </RadioGroup>
              </FormItem>
            </div>
            <div class="col-lg-9 col-12 parent-ripley-text-input">
              <scrum-input :ref="`scrumInput${reverse.id}`" :multiple="true" :setRef="reverse.id"
                           @setFileCount="fileCount" @submitForm="replyChecklistReverse('formCustom')"
                           @bodyData="setBodyData" @update="updateChecklistReverse"></scrum-input>
            </div>
            <div class="parent-btn-ripley">
              <Button type="primary" :loading="reverse_loading" @click="replyChecklistReverse('formCustom')">ثبت</Button>
            </div>
          </div>
        </Form>
        <reply-reverse  v-for="(reply , index) in reverse.replies" :key="index" :reverse="reply"
                        style="background : rgba(0,0,0,0.02)" :process_id="process_id" :level="level + 1"/>
      </div>
    </div>
    <div class="d-flex reply-option-parent">
    <div class="d-flex border-radius align-items-center reply-option" >
      <Tooltip content="پاسخ" v-if="level < 4" >
          <Icon class="text-light px-1" style="font-size: 20px; height: 20px" type="ios-undo" @click="openReplyForm" />
      </Tooltip>
      <Tooltip content="ویرایش" v-if="can_user_edit" >
        <Icon class="text-light px-1" style="font-size: 20px; height: 20px;" type="md-create" @click="openEditForm" />
      </Tooltip>
      </div>
      <div v-if="!reverse.parent" class="d-flex border-radius align-items-center reply-option" :class="reverse.seen == '1'  ? 'bg-success' : ''" @click="seenReverse()">
        <Tooltip   :content="reverse.seen == 0  ? 'اضافه شدن به دیده ها' : 'تنظیم به عنوان مشاهده نشده'">
          <Icon class="text-light px-1" style="font-size: 20px; height: 20px;" type="ios-eye"  />
      </Tooltip>
      </div>
    </div>
  </div>
</template>

<script>
  import ScrumInput from "../../../../tools/scrumInput";
  export default {
    name : 'reply-reverse' ,
    components: {ScrumInput},
    props : ['reverse' , 'user' , 'process_id' , 'level' , 'activeSection'] ,
    data(){
      return  {
        imageModal : false,
        selectedImage : '',
        status_list : [
          {
            id : 'accept' ,
            title : 'پذیرفتن'
          } ,
          {
            id : 'reject' ,
            title: 'رد'
          }
        ],
        type_list : [
          {
            id    : 'offer' ,
            title : 'پیشنهاد'
          } ,
          {
            id    : 'error' ,
            title : 'ایراد'
          },
          {
            id : 'periodical' , title: 'ادواری'
          }
        ],
        section_list : [
          {
            id : 3,
            title  : 'برنامه نویس'
          },
          {
            id : 2,
            title  : 'گرافیک'
          }
        ],
        reply : false,
        editForm : false,
        selected : '' ,
        checklist_reverse_id : '' ,
        file_count : 0,
        counter : 0 ,
        reverse_loading : false ,
        formCustom : {
          status : '' ,
          body : ''
        },
        ruleCustom : {
          status: [
            { required: true , type : 'string', message: 'وضعیت را انتخاب کنید.', trigger: 'blur' }
          ],
          body: [
            { required: true , type : 'string', message: 'متن مورد نظر دا وارد کنید.', trigger: 'blur' }
            // { validator: validateBody , trigger: 'blur' }
          ],
        },
        formEdit : {
          status : '' ,
          body : ''
        },
        ruleEdit : {
          status: [
            { required: true , type : 'string', message: 'وضعیت را انتخاب کنید.', trigger: 'blur' }
          ],
          body: [
            { required: true , type : 'string', message: 'متن مورد نظر دا وارد کنید.', trigger: 'blur' }
            // { validator: validateBody , trigger: 'blur' }
          ],
        }
      }
    },
    methods : {
      seenReverse() {
          this.$axios.put(`subTask/seen/${this.reverse.id}`).then(res =>{
            this.$store.commit('admin/checklistContract/SEEN_CHECKLIST_PROCESS_REVERSE_DATA' , {processId : this.process_id , reverseId : this.reverse.id })
            this.$Message.success(res.data.message)
          }).catch(error =>{
            this.$Message.error(error.response.data.message)
            this.reverse_loading = false
          })
      },
      editReply(name){
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.edit_loading = true
            this.$axios.put(`reply/subTask/${this.reverse.id}`, {
              status: this.formEdit.status,
              body: this.formEdit.body,
            }).then(res =>{
              this.$store.commit('admin/checklistContract/EDIT_CHECKLIST_PROCESS_REVERSE_DATA' , { newReverse : res.data.data , processId : this.process_id})
              this.edit_loading = false
              this.editForm = false
            }).catch(error =>{
              this.$Message.error(error.response.data.message)
              this.reverse_loading = false
            })
          }
          else {
            this.$Message.error('ابتدا خطاهای خود را بررسی کنید.');
          }
        })
      },
      openReplyForm() {
        this.$store.commit('admin/checklistContract/SET_ACTIVE_REVERSE' , this.reverse.id)
        setTimeout(() => {
          const element = document.getElementById('formCustom');
          // console.log(element.offsetTop )
          element.scrollIntoView({ behavior: 'smooth' , block: "end" });
          // window.scrollTo({ left: 0, top: element.offsetTop , behavior: "smooth" });
        }, 200);
        this.reply = true

      },
      openEditForm(){
        this.editForm = true
        this.formEdit.status = this.reverse.status
        this.formEdit.body = this.reverse.body
      },
      fileCount(value) {
        this.file_count = value
      },
      setBodyData(value){
        this.formCustom.body  = value
      },
      replyChecklistReverse(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.reverse_loading = true
            this.$axios.post(`reply/subTask`, {
              status: this.formCustom.status,
              body: this.formCustom.body,
              parent_id: this.reverse.id ,
              checklist_process: this.process_id ,
            }).then(res =>{
              if(this.file_count === 0 ){
                this.$store.commit('admin/checklistContract/ADD_CHECKLIST_PROCESS_REVERSE_DATA' , { newReverse : res.data.data , processId : this.process_id})
                this.handleReset(name);
              }
              else {
                this.checklist_reverse_id = res.data.data.id
                this.$refs[`scrumInput${this.reverse.id}`].setValue({
                  checklist_reverse_id  : this.checklist_reverse_id ,
                  active : true
                });
              }
            }).catch(error =>{
              this.$Message.error(error.response.data.message)
              this.reverse_loading = false
            })
          }
          else {
            this.$Message.error('ابتدا خطاهای خود را بررسی کنید.');
          }
        })
      },
      updateChecklistReverse(file_path) {
        this.$axios.put(`subTask/${this.checklist_reverse_id}` , {
          file : file_path
        }).then(res => {
          this.counter = this.counter + 1
          if(this.counter === this.file_count){
            this.$store.commit('admin/checklistContract/ADD_CHECKLIST_PROCESS_REVERSE_DATA' , { newReverse : res.data.data , processId : this.process_id})
            this.handleReset('formCustom')
          }
        })
      },
      handleReset (name) {
        this.reverse_loading = false
        this.counter = 0
        this.$refs[name].resetFields();
        this.$refs[`scrumInput${this.reverse.id}`].resetData()
        this.$store.commit('admin/checklistContract/SET_ACTIVE_REVERSE' , 'notAny')
      },
    } ,
    computed : {
      can_user_edit (){
        return this.$store.state.auth.user.id === this.reverse.user.id
      }
    }
  }
</script>
<style>
@media (max-width: 576px) {
  .input-text-user>p{
    font-size: 13px;
    line-height: 23px;
  }
  .parent-box-ripley{
    flex-direction: column;
  }
  .parent-ripley-text-input{
    padding: 0 !important;
  }
  .ivu-radio{
    margin-left: 5px;
  }
  #formCustom{
    padding-bottom: 0 !important;
  }
  .parent-radio-btn label{
    margin-bottom: 0;
    font-size: 12px;
  }
  .parent-radio-btn{
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .parent-btn-ripley{
    margin-top: 10px;
  }
  .parent-box-mobile{
    flex-direction: column;
  }
  .parent-item-box{
    align-items: flex-start !important;
  }
  .parent-box-name-data{
    position: absolute;
    top: 15px;
    right: 45px;
  }
  .input-text-user{
    font-size: 13px;
  }
  .reply-option{
    top: 10px;
  }
  .cursor-pointer{
    width: 100%;
  }
  #upload-1996 label{
    margin-bottom: 0;
  }
  #upload-1996{
    height: 100%;
  }
  .ivu-form-item-error-tip{
    font-size: 10px;
  }
  .upload label{
    margin-bottom: 0;
  }
  .file-uploads{
    height: 100%;
  }
  .ivu-card-bordered{
    border: none;
  }
  .ivu-card-body{
    padding: 0;
    margin-top: 10px;
  }
  .ivu-collapse{
    border: none;
  }
  .w-100-me{
    width: 100%;
  }
  .parent-box-formCustom1>div:last-child{
    padding: 0;
  }
  .parent-box-formCustom1{
    flex-direction: column;
  }
  .ivu-radio-wrapper{
    font-size: 12px;
  }
}
</style>
