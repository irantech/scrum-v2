<template>
    <ListItem >
      <ListItemMeta :title="baseProgress.title"
                    :description="baseProgress.description + '-' + '(' + baseProgress.percentage + '%)' + '-' + '[' + baseProgress.software.title + ']'"/>
      <template slot="action">
        <li class="text-primary" v-if="!baseProgress.trashed">
          <nuxt-link :to="`/admin/base-progress/${baseProgress.id}`"> زیرمجموعه ها</nuxt-link>
        </li>
        <li class="text-success" v-if="!baseProgress.trashed && $store.getters['auth/can']('update-baseProgress')">
          <a @click="openUpdateModal()">
            ویرایش
          </a>
        </li>
        <li class="text-danger" v-if="!baseProgress.trashed && $store.getters['auth/can']('delete-baseProgress')">
          <a @click="openRemoveModal()">حذف</a>
        </li>
        <li class="text-info" v-if="baseProgress.trashed && $store.getters['auth/can']('restore-baseProgress')" >
          <a @click="restoreBaseProgress()">بازگردانی</a>
        </li>
      </template>
      <Modal v-model="deleteModel" width="360">
        <p slot="header" style="color:#f60;text-align:center">
          <Icon type="ios-information-circle"></Icon>
          <span>حذف قرارداد انتخابی</span>
        </p>
        <div style="text-align:center">
          <p>آیا از حذف این مرحله اصلی اطمینان دارید؟</p>
        </div>
        <div slot="footer">
          <Button type="error" size="large" long :loading="deleteLoading" @click="removeBaseProgress()">حذف</Button>
        </div>
      </Modal>
      <Modal v-model="updateModal" :title="` ویرایش مرحله ${baseProgress.title}`" width="800" footer-hide>
        <Form ref="formValidate" :model="formValidate" :rules="ruleValidate" label-position="top">
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
              <Button type="primary" :loading="updateLoading" @click="updateBaseProgress('formValidate')">ویرایش اطلاعات</Button>
            </FormItem>
          </div>
        </Form>
      </Modal>
    </ListItem>

</template>

<script>
  export default  {
    name  : 'BaseProgress-box',
    props : ['baseProgress' , 'softwareList' , 'sectionList' , 'userRoles'] ,
    data() {
      return {
        deleteModel : false ,
        deleteLoading : false ,
        updateModal : false ,
        updateLoading : false,
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
    },
    methods : {
      restoreBaseProgress() {
        this.$store.dispatch('admin/baseProgress/restoreBaseProgress', {id: this.baseProgress.id})
          .then(response => {
            this.$Message.info(response.data.message)
          })
          .catch(error => {
            this.$Message.error(error.response.data)
          })
      },
      removeBaseProgress() {
        this.deleteLoading = true
        this.$store.dispatch('admin/baseProgress/removeBaseProgress', {id: this.baseProgress.id})
          .then(response => {
            this.deleteLoading = false
            this.$Message.warning(response.data.message);
            this.deleteModel = false
          })
          .catch(error => {
            this.$Message.error(error.response.data);
          })
      },
      openRemoveModal() {
        this.deleteModel = true
      },
      openUpdateModal() {
        this.updateModal = true
        this.formValidate.user_role = this.baseProgress.user_role
        this.formValidate.software_id = this.baseProgress.software_id
        this.formValidate.title = this.baseProgress.title
        this.formValidate.description = this.baseProgress.description
        this.formValidate.private_description = this.baseProgress.private_description
        this.formValidate.percentage = this.baseProgress.percentage
        this.formValidate.section_id = this.baseProgress.section_id
      } ,
      updateBaseProgress(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            let soft = this.softwareList.find(software => software.id == this.formValidate.software_id)
            this.formValidate.softwareTitle = soft.title
            this.updateLoading = true
            this.$store.dispatch('admin/baseProgress/updateBaseProgress', {
              id: this.baseProgress.id,
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
      }
    }
  }
</script>
