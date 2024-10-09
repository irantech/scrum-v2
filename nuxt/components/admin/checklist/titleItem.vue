<template>
  <ListItem>
    <ListItemMeta>
      <p slot="title">
        {{ titleChecklist.title }}
        <span v-if="titleChecklist.section.length > 0 " class="extra-list-item">
          [
          <span v-for="(sect , index) in titleChecklist.section" :key="index" >
            ({{ sect.title }})
          </span>
          ]
        </span>
      </p>
      <p slot="description">{{titleChecklist.description}}</p>
    </ListItemMeta>


    <template slot="action">
      <li class="text-success" v-if="$store.getters['auth/can']('update-titleChecklist')">
        <a @click="openUpdateModal">
          ویرایش
        </a>
      </li>
      <li class="text-danger" v-if="$store.getters['auth/can']('delete-titleChecklist')">
        <a @click="deleteModel = true">
          حذف
        </a>
      </li>
    </template>
    <Modal v-model="deleteModel" width="360">
      <p slot="header" style="color:#f60;text-align:center">
        <Icon type="ios-information-circle"></Icon>
        <span>حذف  عنوان انتخابی</span>
      </p>
      <div style="text-align:center">
        <p>آیا از حذف عنوان اطمینان دارید؟</p>
      </div>
      <div slot="footer">
        <Button type="error" size="large" long :loading="deleteLoading" @click="removeTitleChecklist()">حذف</Button>
      </div>
    </Modal>
    <Modal v-model="restoreModal" width="360">
      <p slot="header" style="color:#17a2b8;text-align:center">
        <Icon type="ios-information-circle"></Icon>
        <span>بازگرداندن عنوان انتخابی</span>
      </p>
      <div style="text-align:center">
        <p>آیا از بازگرداندن این عنوان اطمینان دارید؟</p>
      </div>
      <div slot="footer">
        <Button type="info" size="large" long :loading="restoreLoading" @click="restoreTitleChecklist()">بازگرداندن</Button>
      </div>
    </Modal>
    <Modal v-model="updateModal" :title="` ویرایش${titleChecklist.title}` " width="500" footer-hide>
      <Form ref="formValidate" :model="formValidate" :rules="ruleValidate" label-position="top"
        @submit.native.prevent="updateTitleChecklist('formValidate')">
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
            <Button type="primary" :loading="updateLoading" html-type="submit">ویرایش اطلاعات</Button>
          </FormItem>
        </div>
      </Form>
    </Modal>
  </ListItem>
</template>

<script>
export default {
  name : 'titleChecklist-item' ,
  props : ['titleChecklist' , 'sectionList' , 'checklistId' ] ,
  data () {
    return {
      deleteModel : false ,
      deleteLoading : false ,
      updateModal : false ,
      updateLoading : false,
      restoreModal : false,
      restoreLoading : false,
      assignModel : false ,
      assignLoading : false,
      formValidate: {
        title: '',
        description : '' ,
        section : []
      },
      assignForm : {
        users : []
      },
      ruleValidate: {
        users: [
          { required: true, type : 'array' ,message: 'ابتدا کارمندان مورد نظر را انتخاب کنید.', trigger: 'change' }
        ],
      },
    }
  },
  methods : {
    removeTitleChecklist() {
      this.deleteLoading = true
      this.$store.dispatch('admin/checklist/removeTitleChecklist', {id: this.titleChecklist.id})
        .then(response => {
          this.deleteLoading = false
          this.$Message.warning(response.data.message);
          this.deleteModel = false
        })
        .catch(error => {
          this.deleteLoading = false
          this.$Message.error(error.response.data.message);
        })
    },
    restoreTitleChecklist() {
      this.restoreLoading = true
      this.$store.dispatch('admin/titleChecklist/restoreChecklist', {id: this.titleChecklist.id})
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
      this.formValidate.title = this.titleChecklist.title
      this.formValidate.description = this.titleChecklist.description
      this.formValidate.section = this.titleChecklist.section.map(check => check.id)
    },
    updateTitleChecklist(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.updateLoading = true
          this.formValidate.checklist_id = this.checklistId
          this.$store.dispatch('admin/checklist/updateTitleChecklist', {
            id: this.titleChecklist.id,
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
  },
}
</script>
