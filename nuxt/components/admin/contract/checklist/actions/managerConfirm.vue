<template>
  <div>
    <Tooltip content="تایید مدیر" placement="top">
      <Button icon="ios-checkmark-circle-outline" type="success"
              @click="openManagerConfirmationModel(section.id)"
              :disabled="activeSection !== section.id || type !== 'managerConfirm' ||
                              !$store.getters['auth/canManagerApprove'](sectionOrder)"></Button>
    </Tooltip>
    <Modal v-model="managerConfirmationModal" title="تایید مدیر" width="800" footer-hide>
      <Form ref="managerForm" :model="formManager" label-position="top" :rules="managerValidate">
        <div class="row">
          <div class="col-12">
            <FormItem label="توضیحات" prop="description">
              <Input v-model="formManager.description" type="textarea" :autosize="{minRows: 2,maxRows: 5}" placeholder="پیام خود را وارد کنید"></Input>
            </FormItem>
          </div>
          <div class="col-12">
            <FormItem>
              <Button type="primary" :loading="processLoading"
                      @click="createChecklistProcess('managerForm')">ثبت اطلاعات</Button>
            </FormItem>
          </div>
        </div>
      </Form>
    </Modal>
  </div>
</template>

<script>
export default {
  props : ['section' , 'type' , 'activeSection' , 'sectionOrder' ],
  name : 'manager-confirm-section',
  data() {
    return{
      processLoading : false,
      managerConfirmationModal: false,
      formManager : {
        description : ''
      },
      managerValidate : {
        description: [
          { required: true, message: 'کامنت خود را وارد کنید', trigger: 'change' },
        ],
      },
    }
  },
  methods : {
    createChecklistProcess(name) {
      this.formManager.section = this.sectionId
      this.formManager.type = this.type

      this.$refs[name].validate((valid) => {
        if (valid) {
          this.processLoading = true
          this.$axios.post(`process/checklist-contract/${this.$route.params.id}`,
            this.formManager)
            .then(response => {
              this.$Message.success(response.data.message);
              this.managerConfirmationModal = false
              if(response.data.data === null)
              {
                this.$emit('setSection' , this.section)
                this.$emit('setType' , '')
              }
              else if(response.data.data.id !== this.section){
                this.$emit('setType' , 'confirm')
                this.$emit('setSection' , response.data.data)
              }
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
    openManagerConfirmationModel(id){
      this.managerConfirmationModal = true
      this.sectionId = id
    },
  },


}
</script>
