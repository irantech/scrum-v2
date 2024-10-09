<template>
  <div>
    <div class="col-12 col-md-9 m-auto">
      <Card>
        <p slot="title">
          مراحل کلی این قرارداد
        </p>
        <p slot="extra" v-if="$store.getters['auth/can']('create-baseProgress')">
          <Button type="success" @click="createModal = true" >
            افزودن مرحله اصلی
          </Button>
        </p>
        <List :loading="loadingBaseProgress">
          <adminBaseProgressBox v-for="(baseProgress , index) in baseProgressList" :key="index"
                                :baseProgress="baseProgress" :sectionList="sectionList"
                                :softwareList="softwareList" :userRoles="userRoles"/>
        </List>
      </Card>
    </div>
    <Modal v-model="createModal" title="مرحله اصلی جدید" width="800" footer-hide>
      <Form ref="formValidate" :model="formValidate" :rules="ruleValidate" label-position="top" @submit.native.prevent="handleSubmit('formValidate')">
        <div class="row">
          <div class="col-6">
            <FormItem label="بخش مربوطه" prop="section_id">
              <Select v-model="formValidate.section_id" placeholder="انتخاب کنید">
                <Option v-for="(section , index) in sectionList"  :key="index" :value="section.id">{{ section.title }}</Option>
              </Select>
            </FormItem>
          </div>
          <div class="col-6">
            <FormItem label="نرم افزار" prop="software_id">
              <Select v-model="formValidate.software_id" placeholder="انتخاب کنید">
                <Option v-for="(software , index) in softwareList"  :key="index" :value="software.id">{{ software.title }}</Option>
              </Select>
            </FormItem>
          </div>
          <div class="col-6">
            <FormItem label="دسترسی" prop="user_role">
              <Select v-model="formValidate.user_role" placeholder="انتخاب کنید">
                <Option v-for="(role , index) in userRoles"  :key="index" :value="role.value">{{ role.title }}</Option>
              </Select>
            </FormItem>
          </div>
          <div class="col-6">
            <FormItem label="درصد کلی" prop="percentage">
              <InputNumber
                :max="100"
                :min="0"
                :formatter="value => `${value}%`"
                :parser="value => value.replace('%', '')"
                v-model="formValidate.percentage" controls-outside></InputNumber>
            </FormItem>
          </div>
        </div>
        <div class="col-12">
          <FormItem label="عنوان" prop="title">
            <Input v-model="formValidate.title" type="text" placeholder="عنوان را وارد کنید..."></Input>
          </FormItem>
        </div>
        <div class="col-12">
          <FormItem label="توضیحات" prop="description">
            <Input v-model="formValidate.description" type="textarea" :autosize="{minRows: 2,maxRows: 5}" placeholder="توضیحات اختصاصی  را وارد کنید..."></Input>
          </FormItem>
        </div>
        <div class="col-12">
          <FormItem label="توضیحات اختصاصی" prop="private_description">
            <Input v-model="formValidate.private_description" type="textarea" :autosize="{minRows: 2,maxRows: 5}" placeholder="توضیحات اختصاصی  را وارد کنید..."></Input>
          </FormItem>
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
    name : 'baseProgressList' ,
    props : ['baseProgressList' , 'loadingBaseProgress' , 'softwareList' , 'sectionList'] ,
    data(){
      return{
        userRoles: [
          {value: 'admin', title: 'مدیر کل'},
          {value: 'office', title: 'اداری'},
          {value: 'support', title: 'پشتیبانی'},
          {value: 'accountant', title: 'حسابدار'},
          {value: 'programmer', title: 'برنامه نویس'},
          {value: 'graphic', title: 'گرافیست'},
        ],
        createModal : false,
        createLoading :  false ,
        formValidate: {
          section_id: '',
          user_role: '',
          software_id: '',
          title: '',
          percentage: 0,
          private_description: '',
          description: ''
        },
        ruleValidate: {
          section_id: [
            { required: true, type: 'number',  message: 'لطفا یک بخش مربوطه انتخاب کنید.', trigger: 'change' }
          ],
          user_role: [
            { required: true ,  type: 'string' ,message: 'دسترسی نمیتواند خالی باشد', trigger: 'change' },
          ],
          software_id: [
            { required: true,  type: 'number', message: 'نرم افزار نمیتواند حالی باشد', trigger: 'change' }
          ],
          title: [
            { required: true, message: 'یک عنوان وارد کنید', trigger: 'blur' }
          ],
          percentage: [
            { required: true, type: 'number', min: 1, message: 'درصد مورد نظر را وارد کنید.', trigger: 'change' },
          ],
          private_description: [
            { required: true, type: 'string', message: 'توضیحات را وارد کنید', trigger: 'blur' }
          ],
          description: [
            { required: true, type: 'string', message: 'این فیلد اجباری است.', trigger: 'blur' }
          ],
        },
      }
    } ,
    methods : {
      handleSubmit (name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.createLoading = true
            this.$store.dispatch('admin/baseProgress/createNewBaseProgress' , { form : this.formValidate })
                .then(response => {
                    this.$Message.success(response.data.message);
                    this.$store.dispatch('admin/baseProgress/getBaseProgressList')
                    this.createModal = false
                    this.createLoading = false
                    this.handleReset(name)
                  }
                )
                .catch(error => {
                  this.createLoading = false
                  this.$Message.error(error.response.data.message);
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
