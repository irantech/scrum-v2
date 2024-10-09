<template>
  <div class="col-9 m-auto">
    <Card>
      <p slot="title">
        مرحله اصلی : {{ baseProgress.title }}
      </p>
      <p slot="extra" v-if="$store.getters['auth/can']('create-subProgress')">
        <Button type="success"  >
          <a @click="openCreteModal()">افزودن زیرمجموعه</a>
        </Button>
      </p>
      <Alert show-icon v-if="baseProgress.progress && baseProgress.progress.length  === 0">
        <p>هنوز برای این قرارداد مرحله اصلی ثبت نشده است.</p>
        <Button type="primary" v-if="$store.getters['auth/can']('create-subProgress')">
          <a @click="openCreteModal()">افزودن </a>
        </Button>

      </Alert>
      <List v-else>
        <adminBaseProgressSingleBox v-for="(progress , index) in baseProgress.progress"
                                    :key="index" :progress="progress" :sectionList="sectionList"/>
      </List>
    </Card>
    <Modal v-model="createModal" title="فزودن زیرمجموعه جدید" width="800" footer-hide>
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
            <Button type="primary" :loading="createLoading" @click="handleSubmit('formValidate')">ثبت اطلاعات</Button>
          </FormItem>
        </div>
        </div>
      </Form>
    </Modal>
  </div>
</template>

<script>
  export default {
    name : 'baseProgress-single' ,
    props : ['baseProgress' , 'sectionList'],
    data() {
      return {
        createModal : false,
        createLoading : false,
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
        handleSubmit(name) {
          this.$refs[name].validate((valid) => {
            if (valid) {
              this.formValidate.base_progress_id = this.baseProgress.id
              this.createLoading = true
              this.$store.dispatch('admin/baseProgress/addNewSubBaseProgress' , { form : this.formValidate })
                .then(response => {
                    this.$Message.success(response.data.message);
                    this.createModal = false
                    this.createLoading = false
                    this.handleReset(name)
                  }
                )
                .catch(error => {
                  console.log(error)
                  this.$Message.error(error.response);
                })
            } else {
              this.$Message.error('ابتدا خطاهای خود را بررسی کنید.');
            }
          })
        },
        handleReset (name) {
          this.$refs[name].resetFields();
        },
        openCreteModal() {
          this.createModal = true
        }
      }
    }
  }
</script>
