<template>
  <div>
    <Form ref="deliveryTimeForm" :model="deliveryTime" :rules="deliveryTimeValidate" label-position="top"
          @submit.native.prevent="setDeliveryTime('deliveryTimeForm')">
      <div class="row">
        <div class="col-12">
          <FormItem prop="date" >
            <date-picker :min="today_date"  auto-submit
                         display-format="jYYYY-jMM-jDD"
                         format="YYYY-MM-DD"
                         v-model="deliveryTime.date" clearable />
          </FormItem>
        </div>
        <div class="col-12">
          <Alert type="warning">در صورتی که میخواهید لیست کارهای خودتون رو ببیند بازه مشخص کنید. و دکمه بررسی تسک را کلیک کنید.</Alert>
          <FormItem>
            <InputNumber v-model="deliveryTime.duration" controls-outside />
          </FormItem>
        </div>
        <div class="col-12">
          <FormItem>
            <Button type="primary" :loading="deliveryTimeLoading" html-type="submit">ثبت تاریخ</Button>
            <Button type="warning" html-type="button" @click="checkTaskList('deliveryTimeForm')">بررسی تسک های شما در بازه تاریخی </Button>
          </FormItem>
        </div>
      </div>

    </Form>
    <Card class="mt-2" v-if="show_period_task">
      <Col span="24" class="px-2 my-1 d-flex justify-content-center" v-if="period_loading">
        <Spin size="large"> </Spin>
      </Col>
      <admin-task-period v-else-if="period_task.length > 0 " :task_list="period_task" />
      <Alert type="warning" v-if="!period_loading && period_task.length == 0 ">هیچ موردی یافت نشد.</Alert>
    </Card>
  </div>

</template>
<script>
export default {
  name : 'task-delivery' ,
  props : ['task'] ,
  data() {
    return{
      period_task : [] ,
      period_loading : false ,
      show_period_task  : false ,
      deliveryTimeLoading : false ,
      deliveryTime : {
        'date': '' ,
        'duration'  : 1
      },
      deliveryTimeValidate :{
        date: [
          { required: true , type : 'string', message: 'لطفاتایم مورد نظر وارد شود.', trigger: 'blur' }
        ],
      },
    }
  },
  computed : {
    today_date() {
      return  this.$moment().utc().format('jYYYY-jMM-jDD')
    },
  },
  methods :{
    setDeliveryTime (name){
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.deliveryTimeLoading = true
          this.deliveryTime.task_id = this.task.id
          this.$store.dispatch('admin/task/updateTaskDeliveryTime' , {
            form : this.deliveryTime ,
            task_id : this.task.id
          })
            .then(response => {
                this.$Message.success(response.data.message)
                this.$emit('closeDeliveryModel')
                this.deliveryTimeLoading = false
                this.handleSubmitReset(name)
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
    checkTaskList(name) {
      this.show_period_task = true
      this.period_loading = true
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.$axios.post(`task/list/period/${this.task.id}`, this.deliveryTime)
            .then(response => {
              this.period_task = response.data.data

              this.period_loading = false
            })
            .catch(error => reject(error))
        }else {
          this.$Message.error('وارد کردن تاریخ الزامی است.');
        }
      })
    } ,
    handleSubmitReset (name) {
      this.$refs[name].resetFields();
    },
  } ,
  created(){
    this.deliveryTime.date = this.task.delivery_time_base
  }
}
</script>

