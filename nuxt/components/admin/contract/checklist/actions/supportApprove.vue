<template>
  <div>
    <Tooltip content="تایید توسط پشتیبان" placement="top">
      <Button icon="md-done-all" type="info" @click="approveModal = true"
              :disabled="has_stopped || type !== 'supportApprove'|| !$store.getters['auth/can']('support-approve-design')"></Button>
    </Tooltip>
    <Modal v-model="approveModal" title="تایید طرح توسط پشتیبان" width="800" footer-hide>
      <Form ref="approveForm" :model="approveForm" label-position="top" :rules="approveValidate">
        <div class="row">
          <div class="col-12">
            <FormItem label="توضیحات" prop="description">
              <Input v-model="approveForm.description" type="textarea" :autosize="{minRows: 2,maxRows: 5}" placeholder="پیام خود را وارد کنید"></Input>
            </FormItem>
          </div>
          <div class="col-6">
            <FormItem label="شماره تیکت" prop="description">
              <Input v-model="approveForm.ticket_number" type="text"
                     :autosize="{minRows: 2,maxRows: 5}" placeholder="شماره تیکت را وارد کنید"></Input>
            </FormItem>
          </div>
          <div class="col-12">
            <FormItem>
              <Button type="primary" :loading="processLoading"
                      @click="createChecklistProcess( 'approveForm')">ثبت اطلاعات</Button>
            </FormItem>
          </div>
        </div>
      </Form>
    </Modal>
  </div>
</template>

<script>
  export default {
    props : ['section' , 'type' , 'has_stopped'],
    name : 'support-approves-section',
    data() {
      return{
        processLoading : false,
        approveModal : false ,
        approveForm : {
          description : '' ,
          type : 'supportApprove' ,
          ticket_number : ''
        },
        approveValidate : {
          description: [
            { required: true, message: 'کامنت خود را وارد کنید', trigger: 'change' },
          ],
          ticket_number: [
            { required: true, message: 'شماره تیکت را وارد کنید', trigger: 'change' },
          ],
        }
      }
    },
    methods : {
      createChecklistProcess(name) {
        this.approveForm.activeSection = this.section
        this.approveForm.section = this.section
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.processLoading = true
            this.$axios.post(`process/checklist-contract/${this.$route.params.id}`,
              this.approveForm)
              .then(response => {
                this.$Message.success(response.data.message);
                this.approveModal = false
                this.$emit('setType' , 'managerConfirm')
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
            }).finally(()=> this.processLoading = false )
          }
          else {
            this.$Message.error('ابتدا خطاهای خود را بررسی کنید.');
          }

        })
      },
      handleReset (name) {
        this.$refs[name].resetFields();
      },
    }

  }
</script>
