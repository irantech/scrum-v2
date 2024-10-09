<template>
  <div>
    <div class="col-12 col-md-10 m-auto">
      <Card>
        <p slot="title">
          موارد چک لیست {{  checklist ? checklist.title  : ''}}
        </p>
        <p slot="extra" v-if="$store.getters['auth/can']('create-titleChecklist')">
          <Button type="success" @click="openCreateModal">
            افزودن عنوان جدید
          </Button>
        </p>
        <List :loading="loading">
          <adminChecklistTitleItem v-for="(titleChecklist , index) in checklist.title_checklists" :key="index"
                                   :titleChecklist="titleChecklist" :checklistId="checklist.id"
                                   :sectionList="sectionList" />
        </List>
      </Card>
    </div>
    <Modal v-model="createModal" title="چک لیست جدید" width="500" footer-hide>
      <Form ref="formValidate" :model="formValidate" :rules="ruleValidate" label-position="top"
            @submit.native.prevent="handleSubmit('formValidate')">
        <div class="col-12">
          <FormItem label="عنوان" prop="title">
            <Input v-model="formValidate.title" type="text" placeholder="عنوان را وارد کنید..."></Input>
          </FormItem>
        </div>
        <div class="col-12">
          <FormItem label="بخش های مربوطه" prop="section">
            <Select v-model="formValidate.section" placeholder="انتخاب کنید" multiple>
              <Option v-for="(section , index) in sectionList"  :key="index" :value="section.id">{{ section.title }}</Option>
            </Select>
          </FormItem>
        </div>
        <div class="col-12">
          <FormItem label="توضیحات" prop="description">
            <Input v-model="formValidate.description" type="textarea" :autosize="{minRows: 2,maxRows: 5}" placeholder="توضیحات را وارد کنید..."></Input>
          </FormItem>
        </div>
        <div class="col-12">
          <FormItem>
            <Button type="primary" :loading="createLoading" html-type="submit">ثبت اطلاعات</Button>
          </FormItem>
        </div>
      </Form>
    </Modal>
  </div>
</template>

<script>
export default {
  name : 'titleChecklist-list',
  props : ['checklist'  , 'loading'  ,'userList'] ,
  data() {
    return {
      createModal : false,
      createLoading : false ,
      formValidate : {
        title : '' ,
        description : '',
        section : ''
      },
      ruleValidate : {
        title: [
          { required: true, message: 'فیلد عنوان الزامی است.', trigger: 'change' }
        ],
        section: [
          { required: true,type: 'array', message: 'لطفا بخش مربوطه را انتخاب کنید', trigger: 'change' }
        ],
      } ,
    }
  },
  computed : {
    sectionList () {
      return this.$store.getters['admin/section/getSpecialSections'](this.checklist.sections)
    }
  },
  methods : {
    openCreateModal() {
      this.createModal = true
    },
    handleSubmit(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.createLoading = true
          this.formValidate.checklist_id = this.checklist.id
          this.$store.dispatch('admin/checklist/createNewTitleChecklist' , { form : this.formValidate })
            .then(response => {
                this.$Message.success(response.data.message)
                this.createModal = false
                this.createLoading = false
                this.handleReset(name)
              }
            )
            .catch(error => {
              if(error.response.data.message)
                this.$Message.error(error.response.data.message);
              if(error.response.data.errors) {
                let errors = error.response.data.errors
                errors.forEach(error => {
                  this.$Message.error(error)
                })
              }
              this.createLoading = false
            })
        } else {
          this.$Message.error('ابتدا خطاهای خود را بررسی کنید.');
        }
      })
    },
    handleReset (name) {
      this.$refs[name].resetFields();
    } ,
  }
}
</script>
