<template>
  <ListItem>
    <ListItemMeta :title="checklist.title + ' (' + checklist.language.title + ')'" :description="checklist.description"/>
    <template slot="action">
      <li class="text-success" v-if="!checklist.trashed && $store.getters['auth/can']('update-checklist')" >
        <a @click="openUpdateModal">
          ویرایش
        </a>
      </li >
      <li class="text-danger" v-if="!checklist.trashed && $store.getters['auth/can']('delete-checklist')">
        <a @click="deleteModel = true">
          حذف
        </a>
      </li>
      <li class="text-info" v-if="!checklist.trashed && $store.getters['auth/can']('show-titleChecklist')">
        <nuxt-link :to="`/admin/checklists/${checklist.id}`">
           موارد چک لیست
        </nuxt-link>
      </li>
      <li class="text-primary" v-if="checklist.trashed && $store.getters['auth/can']('restore-checklist')">
        <a @click="restoreModal = true">
          بازگرداندن
        </a>
      </li>
    </template>
    <Modal v-model="deleteModel" width="360">
      <p slot="header" style="color:#f60;text-align:center">
        <Icon type="ios-information-circle"></Icon>
        <span>حذف چک لیست انتخابی</span>
      </p>
      <div style="text-align:center">
        <p>آیا از حذف این چک لیست اطمینان دارید؟</p>
      </div>
      <div slot="footer">
        <Button type="error" size="large" long :loading="deleteLoading" @click="removeChecklist()">حذف</Button>
      </div>
    </Modal>
    <Modal v-model="restoreModal" width="360">
      <p slot="header" style="color:#17a2b8;text-align:center">
        <Icon type="ios-information-circle"></Icon>
        <span>بازگرداندن چک لیست انتخابی</span>
      </p>
      <div style="text-align:center">
        <p>آیا از بازگرداندن این چک لیست اطمینان دارید؟</p>
      </div>
      <div slot="footer">
        <Button type="info" size="large" long :loading="restoreLoading" @click="restoreChecklist()">بازگرداندن</Button>
      </div>
    </Modal>
    <Modal v-model="updateModal" :title="` ویرایش نقش ${checklist.title}` " width="500" footer-hide>
      <Form ref="formValidate" :model="formValidate" :rules="ruleValidate" label-position="top"
        @submit.native.prevent="updateChecklist('formValidate')">
        <div class="col-12">
          <FormItem label="عنوان" prop="title">
            <Input v-model="formValidate.title" type="text" placeholder="عنوان را وارد کنید..."></Input>
          </FormItem>
        </div>
        <div class="col-12">
          <FormItem label="زبان" prop="language">
            <Select v-model="formValidate.language" placeholder="انتخاب کنید">
              <Option v-for="(language , index) in languageList"  :key="index" :value="language.id">{{ language.title }}</Option>
            </Select>
          </FormItem>
        </div>
        <div class="col-12">
          <FormItem label="بخش ها" prop="section">
            <CheckboxGroup v-model="formValidate.section">
              <Checkbox v-for="(section , index)  in sectionList"  class="col-6"
                        :key="index" :label="section.id">
                <span>{{ section.title }}</span>
              </Checkbox>
            </CheckboxGroup>
          </FormItem>
        </div>
        <div class="col-12">
          <FormItem label="توضیحات" prop="description">
            <Input v-model="formValidate.description" type="textarea" :autosize="{minRows: 2,maxRows: 5}" placeholder="توضیحات را وارد کنید..."></Input>
          </FormItem>
        </div>
        <div class="col-12">
          <FormItem>
            <Button type="primary" :loading="updateLoading" html-type="submit">ویرایش اطلاعات</Button>
          </FormItem>
        </div>
      </Form>
    </Modal>
  </ListItem>
</template>

<script>
export default {
  name : 'checklist-item' ,
  props : ['checklist' , 'languageList' , 'sectionList'] ,
  data () {
    return {
      deleteModel : false ,
      deleteLoading : false ,
      updateModal : false ,
      updateLoading : false,
      restoreModal : false,
      restoreLoading : false,
      formValidate: {
        title: '',
        description : '',
        language :'' ,
        section : []
      },
      ruleValidate: {
        title: [
          { required: true, message: 'یک عنوان وارد کنید', trigger: 'change' }
        ],
        section : [
          {required : true ,type : 'array', message: 'حداقل یک بخش انتخاب کنید.', trigger: 'change' }
        ]
      },
    }
  },
  methods : {
    removeChecklist() {
      this.deleteLoading = true
      this.$store.dispatch('admin/checklist/removeChecklist', {id: this.checklist.id})
        .then(response => {
          this.deleteLoading = false
          this.$Message.warning(response.data.message);
          this.deleteModel = false
        })
        .catch(error => {
          this.deleteLoading = false
          this.$Message.error(error.message);
        })
    },
    restoreChecklist() {
      this.restoreLoading = true
      this.$store.dispatch('admin/checklist/restoreChecklist', {id: this.checklist.id})
        .then(response => {
          this.restoreLoading = false
          this.$Message.warning(response.data.message);
          this.restoreModal = false
        })
        .catch(error => {
          this.restoreLoading = false
          this.$Message.error(error.message);
        })
    },
    openUpdateModal() {
      this.updateModal = true
      this.formValidate.title = this.checklist.title
      this.formValidate.description = this.checklist.description
      this.formValidate.language = this.checklist.language.id
      this.formValidate.section  =  this.checklist.sections.map(x=>x.id)
    },
    updateChecklist(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.setSections()
          this.updateLoading = true
          this.$store.dispatch('admin/checklist/updateChecklist', {
            id: this.checklist.id,
            form: this.formValidate
          })
            .then(response => {
              this.updateModal = false
              this.updateLoading = false
              this.$Message.success(response.data.message);
            })
            .catch(error => {
              if(error.response.data.message)
                this.$Message.error(error.response.data.message);
              if(error.response.data.errors) {
                let errors = error.response.data.errors
                errors.forEach(error => {
                  this.$Message.error(error)
                })
              }
              this.updateLoading = false
            })
            .finally(()=> this.updateLoading  = false)
        } else {
          this.$Message.error('ابتدا خطاهای خود را بررسی کنید.');
        }
      })
    } ,
    setSections(){
      this.formValidate.sections = []
      this.sectionList.forEach(section => {
        if(this.formValidate.section.includes(section.id)){
          this.formValidate.sections.push({
            id : section.id
          })
        }
      })
      this.formValidate.sections = JSON.stringify(this.formValidate.sections )
    }
  } ,
}
</script>
