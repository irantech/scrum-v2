<template>
  <div>
    <div class="col-12 col-md-9 m-auto">
      <Card>
        <p slot="title">
          چک لیست ها
        </p>
        <p slot="extra" v-if="$store.getters['auth/can']('create-checklist')">
          <Button type="success" @click="openCreateModal">
            افزودن چک لیست جدید
          </Button>
        </p>
        <List :loading="checklistLoading">
          <adminChecklistItem v-for="(checklist , index) in checklists" :key="index"
                              :checklist="checklist" :sectionList="sectionList" :languageList="languageList"/>
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
            <Button type="primary" :loading="createLoading" html-type="submit">ثبت اطلاعات</Button>
          </FormItem>
        </div>
      </Form>
    </Modal>
  </div>
</template>

<script>
export default {
  name : 'checklist-list',
  props : ['checklists' ,  'checklistLoading' , 'languageList' , 'sectionList'] ,
  data() {
    return {
      createModal : false,
      createLoading : false ,
      formValidate : {
        title : '' ,
        description : '',
        language : 1 ,
        section : []
      },
      ruleValidate : {
        title: [
          { required: true, message: 'فیلد عنوان الزامی است.', trigger: 'change' }
        ],
        section : [
          { required : true ,type : 'array', message: 'حداقل یک بخش انتخاب کنید.', trigger: 'change' }
        ]
      }
    }
  },
  methods : {
    openCreateModal() {
      this.createModal = true
    },
    handleSubmit(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.setSections()
          this.createLoading = true
          this.$store.dispatch('admin/checklist/createNewChecklist' , { form : this.formValidate })
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
                let errors = error.response.data.errors;
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
    },
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
  }
}
</script>
