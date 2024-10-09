<template>
  <ListItem>
    <ListItemMeta :title="holiday.title" :description="holiday.date"/>
    <template slot="action">
      <li class="text-success">
        <a @click="openUpdateModal">
          ویرایش
        </a>
      </li>
      <li class="text-danger">
        <a @click="deleteModel = true">
          حذف
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
        <Button type="error" size="large" long :loading="deleteLoading" @click="removeHoliday()">حذف</Button>
      </div>
    </Modal>
    <Modal v-model="updateModal" :title="` ویرایش تعطیلی با عنوان ${holiday.title}` " width="500" footer-hide>
      <Form ref="formValidate" :model="formValidate" label-position="top">
        <div class="col-12">
          <FormItem label="عنوان" prop="title">
            <Input v-model="formValidate.title" type="text" placeholder="عنوان را وارد کنید..."></Input>
          </FormItem>
        </div>
        <div class="col-12">
          <FormItem label="تاریخ">
            <date-picker auto-submit format="YYYY-MM-DD" display-format="jYYYY-jMM-jDD" clearable v-model="formValidate.date" placeholder="از تاریخ"/>
          </FormItem>
        </div>
        <div class="col-12">
          <FormItem>
            <Button type="primary" :loading="updateLoading" @click="updateHoliday('formValidate')">ویرایش اطلاعات</Button>
          </FormItem>
        </div>
      </Form>
    </Modal>
  </ListItem>
</template>

<script>

export default {
  name : 'holiday-item' ,
  props : ['holiday'] ,
  data () {
    return {
      deleteModel : false ,
      deleteLoading : false ,
      updateModal : false ,
      updateLoading : false,
      formValidate: {
        title: '',
        date: ''
      },
    }
  },
  methods : {
    removeHoliday() {
      this.deleteLoading = true
      this.$store.dispatch('admin/holiday/removeHoliday', {id: this.holiday.id})
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
    openUpdateModal() {
      this.updateModal = true
      this.formValidate.title = this.holiday.title
      this.formValidate.date = this.holiday.date
    },
    updateHoliday(name) {
          this.updateLoading = true
          this.$store.dispatch('admin/holiday/UpdateAdminHoliday', {
            id: this.holiday.id,
            form: this.formValidate
          })
            .then(response => {
              this.updateModal = false
              this.updateLoading = false
              this.$Message.success(response.message);
            })
            .catch(error => {
              this.$Message.error(error.response.data.message);
              this.updateLoading = false
            })
            .finally(()=> this.updateLoading  = false)
    } ,
  }
}
</script>
