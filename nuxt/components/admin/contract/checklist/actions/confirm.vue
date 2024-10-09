<template>
  <div>
    <Tooltip content="تایید نهایی" placement="top">
      <Button icon="md-checkmark" type="primary"
              :disabled="activeSection !== section || type !== 'confirm' ||
                        (!$store.getters['auth/canChangeTitleChecklistStatus'](sectionOrder) &&
                        !$store.getters['admin/checklistContract/canUser'](section))"
              @click="openConfirmationModel(section)"
      ></Button>
    </Tooltip>
    <Modal v-model="confirmationModal" title="ثبت اطلاعات چک لیست" width="950" footer-hide>
      <Form ref="formValidate" :model="formValidate" label-position="top" :rules="ruleValidate">
        <div class="row">
          <div class="col-6">
            <FormItem label="مدت زمان" prop="duration">
              <TimePicker format="HH:mm" placeholder="Select time" v-model="formValidate.duration"></TimePicker>
            </FormItem>
          </div>
          <div class="col-12">
            <FormItem label="توضیحات" prop="description">
              <wysiwyg v-model="formValidate.description" />
            </FormItem>
          </div>
          <div class="col-12">
            <FormItem>
              <Button type="primary" :loading="processLoading"
                      @click="createChecklistProcess( 'formValidate')">ثبت اطلاعات</Button>
            </FormItem>
          </div>
        </div>
      </Form>
    </Modal>
  </div>
</template>

<script>

export default {
  props : ['section' , 'type' , 'activeSection'  , 'sectionOrder'],
  name : 'confirm-section',
  data() {
    return{

      processLoading : false,
      confirmationModal : false ,
      text : '',
      formValidate : {
        duration : '' ,
        description :'' ,
        type : 'confirm'
      },
      ruleValidate: {
        description: [
          { required: true, type : 'string' ,  message: 'لطفا توضیحات را وارد کنید.', trigger: 'change' }
        ],
        duration: [
          { required: true,pattern:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/ ,  message: 'مدت زمان انجام کار را وارد کنید', trigger: 'change' }
        ],
      },
    }
  },
  methods : {
    createChecklistProcess( name) {
      this.formValidate.section = this.sectionId
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.processLoading = true
          this.$axios.post(`process/checklist-contract/${this.$route.params.id}`,
            this.formValidate)
            .then(response => {
              this.$Message.success(response.data.message);
              this.confirmationModal = false
              this.sectionOrder === 2 ? this.$emit('setType' , 'supportApprove') : this.$emit('setType' , 'managerConfirm')
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
    },
    openConfirmationModel(id) {
      this.confirmationModal = true
      this.sectionId = id
    },
  } ,

}
</script>
