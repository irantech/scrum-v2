<template>
  <div>
    <Button type="info" @click="showForm">افزودن مورد جدید</Button>
    <Form v-if="$store.state.admin.checklistContract.activeReverse === `process-${process_id}`"
          ref="formInline" :model="formInline" :rules="ruleInline" class="border parent-padding-box col-12">
      <div class="row align-items-end parent-cols">
<!--        <div class="padding-box col-lg-12">-->
<!--          <FormItem prop="section" class="mb-0" v-if="activeSection =='3' || activeSection == '2'">-->
<!--            <RadioGroup v-model="formInline.section">-->
<!--              <Radio v-for="(section , index) in section_list" :key="index" :label="section.id">{{section.title}}</Radio>-->
<!--            </RadioGroup>-->
<!--          </FormItem>-->
<!--        </div>-->
        <div class="padding-box col-lg-2">
          <FormItem prop="status" class="mb-0">
            <Select placeholder="وضعیت برکشت"  v-model="formInline.status">
              <Option v-for="item in status_list" :value="item.id" :key="item.id">{{ item.title }}</Option>
            </Select>
          </FormItem>
        </div>
        <div class="padding-box col-lg-9">
          <ScrumInput ref="scrumInput" :setRef="`process-${process_id}`" :multiple="multiple"
                      @bodyData="setBodyData"
                      @setFileCount="fileCount" @submitForm="createChecklistReverse('formInline')"
                      @update="updateChecklistReverse"></ScrumInput>
        </div>
        <div class="padding-box col-lg-1 parent-btn2">
          <Button type="primary" :loading="reverse_loading"
                  @click="createChecklistReverse('formInline')">ثبت</Button>
        </div>
      </div>
    </Form>
  </div>
</template>

<script>
  import ScrumInput from "../../../../tools/scrumInput";
  export default {
    name : 'reverse-reply' ,
    components : {ScrumInput},
    props : ['process_id' , 'activeSection'] ,
    data() {
      return{
        multiple : true ,
        status_list : [
          {
            id    : 'offer' ,
            title : 'پیشنهاد'
          } ,
          {
            id    : 'error' ,
            title : 'ایراد'
          } ,
          {
            id : 'periodical',
            title: 'ادواری'
          }
        ],
        formInline : {
          status  : '' ,
          body : ''
        } ,
        section_list : [
          {
            id : '3',
            title  : 'برنامه نویس'
          },
          {
            id : '2',
            title  : 'گرافیک'
          }
        ],
        ruleInline : {
          status: [
            { required: true , type : 'string', message: 'انتخاب وضعیت مهم است.', trigger: 'blur' }
          ],
          body: [
            { required: true , type : 'string', message: 'وارد کردن متن اجباری است.', trigger: 'blur' }
          ],
        },
        reverse_loading : false,
        file_count : 0,
        counter : 0 ,
      }
    },
    methods :{
      showForm() {
        this.$store.commit('admin/checklistContract/SET_ACTIVE_REVERSE' , `process-${this.process_id}`)
      },
      fileCount(value) {
        this.file_count = value
      },
      setBodyData(value) {
        this.formInline.body = value
      },
      createChecklistReverse(name) {
        console.log(name)
        this.$refs[name].validate((valid) => {
          if (valid) {
        this.reverse_loading = true
        this.$axios.post( `subTask/create` , {
          body    : this.formInline.body ,
          status  : this.formInline.status,
          checklist_process : this.process_id
        }).then(res=>{
          if(this.file_count === 0 ){
            this.$store.commit('admin/checklistContract/ADD_CHECKLIST_PROCESS_REVERSE_DATA' , {
              newReverse : res.data.data , processId : this.process_id
            })
            this.handleReset(name);
          }
          else {
            this.checklist_reverse_id = res.data.data.id
            this.$refs.scrumInput.setValue({
              checklist_reverse_id  : this.process_id ,
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
            this.$store.commit('admin/checklistContract/ADD_CHECKLIST_PROCESS_REVERSE_DATA' , {
              newReverse : res.data.data ,
              processId : this.process_id})
            this.handleReset('formInline')
          }
        })
      },

      handleReset (name) {
        this.reverse_loading = false
        this.counter = 0
        this.$refs[name].resetFields();
        this.$refs.scrumInput.resetData()
        this.$store.commit('admin/checklistContract/SET_ACTIVE_REVERSE' , 'notAny')
      },
    }
  }
</script>
<style>
@media (max-width: 576px) {
  .padding-box{
    padding: 0;
  }
  .parent-cols{
    padding: .5rem !important;
    border-radius: 8px;
    gap: 10px;
  }
  .parent-btn2{
    display: flex;
    align-items: center;
    justify-content: left;
  }
  .ivu-radio-wrapper{
    margin-bottom: 0;
  }
  .parent-padding-box{
    margin-top: 10px;
    border-radius: 8px;
  }
  .reply-option{
    flex-direction: column;
  }
  .parent-box-name-data>span{
    font-size: 10px;
  }
}
</style>
