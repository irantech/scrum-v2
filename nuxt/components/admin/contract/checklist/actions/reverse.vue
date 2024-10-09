<template>
  <div>
    <Form v-if="!section_type" ref="formReverse" class="border my-2 p-2" :model="formReverse" label-position="top"
          :rules="ruleReverse" @submit.native.prevent="createChecklistProcess( 'formReverse')">
        <div class="row">
          <div class="col-8">
            <FormItem class="mb-0" prop="section">
              <Select v-if="type === 'confirm'" placeholder="بازگشت به بخش" v-model="formReverse.section" :disabled="sectionOrder === 2 || section_type">
                <Option v-for="item in sectionList" :value="item.id" v-if="item.order < sectionOrder && item.order != 1 && item.order != 2" :key="item.id">{{ item.title }}</Option>
                <Option :value="0" v-if="activeSection == 4  || activeSection == 5" >برگشت به هر دو</Option>
              </Select>
              <span v-else-if="sectionOrder === 2" class="font-weight-bold">
                "طراح"
              </span>
               <Select placeholder="بازگشت به بخش" v-else-if="type === 'managerConfirm' && sectionOrder === 1" v-model="formReverse.section" :disabled="sectionOrder === 2 || section_type">
                <Option v-for="item in sectionList" :value="item.id" v-if="item.order <= sectionOrder" :key="item.id">{{ item.title }}</Option>
              </Select>
              <Select placeholder="بازگشت به بخش"  v-else v-model="formReverse.section" :disabled="sectionOrder === 2 || section_type">
                <Option v-for="item in sectionList" :value="item.id" v-if="item.order <= sectionOrder  && item.order !== 2" :key="item.id">{{ item.title }}</Option>
              </Select>

            </FormItem>
          </div>
          <div class="col-4">
            <FormItem class="mb-0">
              <Button type="primary" :loading="processLoading" :disabled="section_type"
                      @click="createChecklistProcess( 'formReverse')">ثبت اطلاعات</Button>
            </FormItem>
          </div>
        </div>

      </Form>
    <Form  v-if="$store.state.admin.checklistContract.activeReverse === 0" ref="formInline"
           :model="formInline" :rules="ruleInline" class="border py-2 col-12"
          :class="{'opacity-form' : !section_type ||  reverse_loading}">
      <div class="row align-items-end">
<!--        <div class="col-12" v-if="activeSection == '2' || activeSection == '3'">-->
<!--          <FormItem prop="section" class="mb-0">-->
<!--            <RadioGroup v-model="formInline.section">-->
<!--              <Radio v-for="(section , index) in section_list" :key="index" :label="section.id">{{section.title}}</Radio>-->
<!--            </RadioGroup>-->
<!--          </FormItem>-->
<!--        </div>-->
        <div class="col-2">
          <FormItem prop="status" class="mb-0">
            <Select placeholder="وضعیت برکشت"  v-model="formInline.status">
              <Option v-for="item in status_list" :value="item.id" :key="item.id">{{ item.title }}</Option>
            </Select>
          </FormItem>
        </div>
        <div class="col-9">
          <ScrumInput ref="scrumInput" :setRef="0" :multiple="multiple"
                      @bodyData="setBodyData"
                      @setFileCount="fileCount"
                      @update="updateChecklistReverse" @submitForm="createChecklistReverse('formInline')"></ScrumInput>
        </div>
        <div class="col-1">
          <Button type="primary" :loading="reverse_loading" @click="createChecklistReverse('formInline')">ثبت</Button>
        </div>
      </div>
    </Form>




<!--    <toolsZone :class="{'opacity-form' : !section_type}"-->
<!--               class="border my-2 p-2" :multiple="true"-->
<!--               :checklist_process="checklist_process"-->
<!--               @uploadedFiles="updatedData" />-->
  </div>
</template>

<script>
import ScrumInput from "../../../../tools/scrumInput";
export default {
  components: {ScrumInput},
  props : ['section' , 'type' , 'activeSection'  , 'sectionList' , 'sectionOrder'  , 'index'],
  name : 'reverse-section',
  data() {
    return{
      multiple : true ,
      reverse_loading : false,
      active_id  : '' ,
      upload_active : false,
      files : '',
      section_type : false,
      checklist_process : '',
      processLoading : false,
      reverse: '',
      reversalModal : false  ,
      upload_files : []  ,
      formReverse : {
        section : '',
        type : 'reverse' ,
        status_type  : ''
      },
      ruleReverse : {
        section: [
          { required: true , type : 'integer', message: 'یک بخش انتخاب کنید', trigger: 'blur' }
        ],
      },
      checklist_reverse_id : '',

      status_list : [
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
           id : '3',
           title  : 'فنی'
        },
        {
          id : '2',
          title  : 'گرافیک'
        }
      ],
      file_count : 0 ,
      counter : 0 ,
      formInline : {
        status  : '' ,
        body : ''
      } ,
      ruleInline : {
        status: [
          { required: true , type : 'string', message: 'انتخاب وضعیت مهم است.', trigger: 'blur' }
        ],
        body: [
          { required: true , type : 'string', message: 'وارد کردن متن اجباری است.', trigger: 'blur' }
        ],
      },

    }
  },
  methods : {
    fileCount(value) {
      this.file_count = value
    },
    setBodyData(value) {
      this.formInline.body = value
    },
    createChecklistReverse(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.reverse_loading = true
          this.$axios.post( `subTask/create` , {
            body    : this.formInline.body ,
            status  : this.formInline.status,
            checklist_process : this.checklist_process.id
          }).then(res=>{
            if(this.file_count === 0 ){
              this.$store.commit('admin/checklistContract/ADD_CHECKLIST_PROCESS_REVERSE_DATA' , { newReverse : res.data.data , processId : this.checklist_process.id})
              this.handleReset(name);
            }
            else {
              this.checklist_reverse_id = res.data.data.id
              this.$refs.scrumInput.setValue({
                checklist_reverse_id  : this.checklist_reverse_id ,
                active : true
              });
            }
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
          this.$store.commit('admin/checklistContract/ADD_CHECKLIST_PROCESS_REVERSE_DATA' , { newReverse : res.data.data , processId : this.checklist_process.id})
          this.handleReset('formInline')
        }
      })
    },
    createChecklistProcess(name) {
      console.log(this.formReverse)
      if(this.sectionOrder === 2)
        this.formReverse.section = this.section
      this.formReverse.activeSection = this.activeSection

      this.$refs[name].validate((valid) => {
        if (valid) {
          this.processLoading = true
          this.$axios.post(`process/checklist-contract/${this.$route.params.id}`,
            this.formReverse)
            .then(response => {
              this.$emit('setType' ,'confirm')
              this.$emit('setSection' , response.data.data)
              this.$Message.success(response.data.message);
              this.reversalModal = false
              this.checklist_process = response.data.process
              this.$store.commit('admin/checklistContract/ADD_CHECKLIST_PROCESS_REVERSE' , response.data.process)
              this.$emit('openCollapse')
              this.section_type = !this.section_type

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
          }).finally(()=> this.processLoading = false)
        }
        else {
          this.$Message.error('ابتدا خطاهای خود را بررسی کنید.');
        }

      })
    },
    handleReset (name) {
      this.$refs[name].resetFields();
      this.counter = 0;
      this.reverse_loading = false
      this.$refs.scrumInput.resetData()
    },
    openReversalModel(id) {
      this.reversalModal = true
      this.sectionId = id
    },
    updatedData(value){
      this.$store.commit('admin/checklistContract/ADD_CHECKLIST_PROCESS_REVERSE_DATA' , { newReverse : value , processId : this.checklist_process.id})
    }

  },

}
</script>
<style>
  .opacity-form{
    opacity: .2 ;
    pointer-events: none;
  }
</style>
