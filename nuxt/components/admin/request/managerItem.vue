<template>
  <div class="card_c">
    <div class="request_header mb-1">
      <img :src="`https://api.ladyscarf.ir/uploads/image/${request.user_requested.avatar}`">
      <div>
        <span class="label_c label_c_gold  mb-1" >{{ requestType }}</span>
        <h2 class="mb-0">{{ request.user_requested.name }}</h2>
        <h3 class="mb-0">{{  request.request_item.title  }}</h3>
        <span class="date-label label_c label_c_inf">{{ request.created_at }}</span>
      </div>
    </div>
    <div class="request_main">

      <div class="p-1 mb-1">
        <h2 class="mb-0">جزییات این درخواست</h2>
        <div class="row mt-1 request-detail">
          <div class="col-6">
            <span>تاریخ شروع:</span>
            {{request.request_item.starting_time}}
          </div>
          <div class="col-6">
            <span>تاریخ پایان:</span>
            {{request.request_item.ending_time}}
          </div>
          <div class="col-12">
            <span>تاریخ شروع پیشنهادی:</span>
            {{request_detail.starting_time}}
          </div>
          <div class="col-12">
            <span>تاریخ پایان پیشنهادی:</span>
            {{request_detail.ending_time}}
          </div>
          <div class="col-12 mt-1">کامنت این درخواست:{{request_detail.reason}}</div>
        </div>
      </div>
    </div>
    <div class="request_footer mt-1" v-if="request.has_confirmed != null">
      <h2>کامنت مدیر:{{request.manager_reason}}</h2>
      <span v-if="request.has_confirmed == '0'" class="deny">رد شده</span>
      <span v-else class="accept">تایید شده</span>
    </div>
    <div v-else>
      <span><Button @click="requestModal = true" type="info" ghost>تعیین وضعیت</Button></span>
    </div>
      <Modal v-model="requestModal" title=" تعیین وضعیت درخواست " width="500" footer-hide>
<div class="request_main bg-info text-light p-2 mb-2 border-radius">
  اطلاعات مربوط به این درخواست
  <p class="mt-1">ساعت شروع : {{request_detail.starting_time}}</p>
  <p class="mt-1">ساعت پایان : {{request_detail.ending_time}}</p>
  <p class="mt-1">دلیل این درخواست : {{request_detail.reason}}</p>
</div>
<Form ref="formValidate" :model="formValidate" :rules="ruleValidate" label-position="top"
      @submit.native.prevent="setStatus('formValidate')">
  <div class="row">
    <div class="col-6">
      <FormItem label="دلیل این درخواست" prop="has_confirmed">
        <Select v-model="formValidate.has_confirmed" style="width:200px">
          <Option value="0">رد کردن</Option>
          <Option value="1">تایید کردن</Option>
        </Select>
      </FormItem>
    </div>
    <div class="col-12">
      <FormItem label="دلیل این درخواست" prop="manager_reason">
        <Input v-model="formValidate.manager_reason" type="textarea" :autosize="{minRows: 2,maxRows: 5}" placeholder="توضیحات را وارد کنید..."></Input>
      </FormItem>
    </div>
    <div class="col-12">
      <FormItem>
        <Button type="primary" :loading="requestLoading" html-type="submit">ویرایش اطلاعات</Button>
      </FormItem>
    </div>
  </div>

</Form>
</Modal>
  </div>


</template>
<script>
export default {
  name : 'manager_request_item' ,
  props :['request'] ,
  data() {
    return{
      requestModal :false,
      requestLoading : false ,
      formValidate: {
        manager_reason: '',
        has_confirmed : ''
      },
      ruleValidate: {
        has_confirmed : [
          { required : true   ,message: 'فیلد تعیین وضعیت الزامی است.', trigger: 'change' }
        ],
        manager_reason : [
          { required : true , type : 'string' , message: 'فیلد دلیل این تغییر الزامی است.', trigger: 'change' }
        ],
      },
    }
  },
  computed : {
    requestType () {
      if(this.request.request_type == 'ToDoList') {
        return 'تغییر زمان بندی'
      }
    },
    request_detail() {
      return JSON.parse(this.request.reason)
    }
  } ,
  methods : {
    setStatus() {
      this.requestLoading = true
      this.$store.dispatch('admin/request/updateManagerRequestList'  , {
        'id' : this.request.id  ,
        'form' : this.formValidate
      }).then(response => {
        this.requestLoading = false
        this.$Message.warning(response.data.message);
        this.requestModal = false
      })
        .catch(error => {
          this.requestLoading = false
          this.$Message.error(error.message);
        })
    }
  }
}
</script>

