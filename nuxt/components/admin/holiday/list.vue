<template>
  <div>
    <div class="col-12 col-md-9 m-auto">
      <Card>
        <p slot="title">
         تعطیلات رسمی
        </p>
        <p slot="extra" >
          <Button type="success" @click="openCreateModal">
            افزودن به لیست تعطیلات
          </Button>
        </p>
        <List>
          <adminHolidayItem v-for="(holiday , index) in holiday_list" :key="index" :holiday="holiday"/>
        </List>
      </Card>
    </div>
    <Modal v-model="createModal" title="تسک جدید" width="500" footer-hide>
      <Form ref="formValidate" :model="formValidate" label-position="top" @submit.native.prevent="handleSubmit('formValidate')">
        <div class="row">
          <div class="col-12">
            <FormItem label="عنوان " >
              <Input v-model="formValidate.title" type="text" placeholder="عنوان را وارد کنید..."></Input>
            </FormItem>
          </div>
          <div class="col-12">
            <FormItem label="تاریخ" prop="section_id">
              <date-picker auto-submit  format="YYYY-MM-DD" display-format="jYYYY-jMM-jDD" clearable v-model="formValidate.date" placeholder=" تاریخ"/>
            </FormItem>
          </div>
          <div class="col-12">
            <FormItem>
              <Button type="primary" :loading="createLoading" html-type="submit">ثبت اطلاعات</Button>
            </FormItem>
          </div>
        </div>

      </Form>
    </Modal>
  </div>
</template>

<script>
export default {
  name : 'holiday-list',
  props : ['holiday_list'] ,
  data() {
    return {
      createModal : false,
      createLoading : false ,
      formValidate : {
        title : '' ,
        date : '',
      }
    }
  },
  methods : {
    openCreateModal() {
      this.createModal = true
    },
    handleSubmit(name) {
          this.createLoading = true
          this.$store.dispatch('admin/holiday/createNewHoliday' , { form : this.formValidate })
            .then(response => {

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
    },
    handleReset (name) {
      this.$refs[name].resetFields();
    }
  }
}
</script>
