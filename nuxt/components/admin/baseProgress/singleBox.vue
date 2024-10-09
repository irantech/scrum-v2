<template>
  <ListItem>
    <ListItemMeta :title="progress.title"
                  :description="progress.description"/>
    <template slot="action">
      <li class="text-success" v-if="!progress.trashed && $store.getters['auth/can']('update-subProgress')">
        <a @click="openUpdateModal()">ویرایش</a>
      </li>
      <li class="text-info" v-if="progress.trashed && $store.getters['auth/can']('update-subProgress')">
        <a @click="restoreBaseProgress">بازگردانی</a>
      </li>
      <li class="text-danger" v-if="!progress.trashed && $store.getters['auth/can']('delete-subProgress')">
        <a @click="openRemoveModal">حذف</a>
      </li>
    </template>
    <Modal v-model="deleteModal" width="360">
      <p slot="header" style="color:#f60;text-align:center">
        <Icon type="ios-information-circle"></Icon>
        <span>حذف زیرمجموعه انتخابی</span>
      </p>
      <div style="text-align:center">
        <p>آیا از حذف این زیرمجموعه اطمینان دارید؟</p>
      </div>
      <div slot="footer">
        <Button type="error" size="large" long :loading="deleteLoading" @click="removeSubBaseProgress()">حذف</Button>
      </div>
    </Modal>
    <Modal v-model="updateModal" title="مرحله اصلی جدید" width="800" footer-hide>
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
            <FormItem label="عنوان" prop="title">
              <Input v-model="formValidate.title" type="text" placeholder="عنوان را وارد کنید..."></Input>
            </FormItem>
          </div>
          <div class="col-12">
            <FormItem label="توضیحات" prop="description">
              <Input v-model="formValidate.description" type="textarea" :autosize="{minRows: 2,maxRows: 5}"
                     placeholder="توضیحات اختصاصی  را وارد کنید..."></Input>
            </FormItem>
          </div>
          <div class="col-12">
            <FormItem>
              <Button type="primary" :loading="updateLoading" @click="updateProgress('formValidate')">ثبت اطلاعات</Button>
            </FormItem>
          </div>
        </div>
      </Form>
    </Modal>
  </ListItem>
</template>

<script>
  export default  {
    name : 'baseProgress-singleBox' ,
    props : ['progress', 'sectionList'],
    data() {
      return{
        updateModal : false ,
        updateLoading : false  ,
        deleteModal : false,
        deleteLoading  : false,
        formValidate : {
          title  : '' ,
          description : '' ,
          section_id  : ''
        },
        ruleValidate: {
          section_id: [
            { required: true, type: 'number',  message: 'لطفا یک بخش مربوطه انتخاب کنید.', trigger: 'change' }
          ],
          title: [
            { required: true, message: 'یک عنوان وارد کنید', trigger: 'blur' }
          ],
          description: [
            { required: true, type: 'string', message: 'این فیلد اجباری است.', trigger: 'blur' }
          ],
        },
      }
    },
    methods : {
      openRemoveModal () {
        this.deleteModal = true
      },
      restoreBaseProgress() {
        this.$store.dispatch('admin/baseProgress/restoreSubBaseProgress' , { id : this.progress.id})
          .then(response=> {
            this.$Message.info(response.data.message);
          })
          .catch(error => {
            this.$Message(error.response.data);
          })
      },
      openUpdateModal() {
        this.updateModal = true
        this.formValidate.section_id =this.progress.section_id
        this.formValidate.title = this.progress.title
        this.formValidate.description = this.progress.description
      },
      removeSubBaseProgress() {
        this.deleteLoading = true
          this.$store.dispatch('admin/baseProgress/removeSubBaseProgress' , { id : this.progress.id})
            .then(response => {
              this.$Message.success(response.data.message)
              this.deleteLoading = false
              this.deleteModal = false
            })
          .catch(error => {
            this.deleteLoading  = false
            this.$Message.error(error.response.message)
          })
      },
      updateProgress(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.formValidate.base_progress_id = this.$route.params.id
            this.updateLoading = true
            this.$store.dispatch('admin/baseProgress/updateSubBaseProgress', {
              id: this.progress.id,
              form: this.formValidate
            })
              .then(response => {
                this.updateModal = false
                this.updateLoading = false
                this.$Message.success(response.data.message);
              })
              .catch(error => {
                this.$Message.error(error.response.message);
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
