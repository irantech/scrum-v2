<template>
  <div class="col-11 m-auto">
    <table class="table table-bordered" style="color: #000 !important;">
      <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">عنوان پیام</th>
        <th scope="col" style="width: 20%">متن پیام</th>
        <th scope="col">شماره موبایل </th>
        <th scope="col"> وضعیت </th>
        <th scope="col">  مشتری </th>
        <th scope="col">  تاریخ </th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(sms , index) in smsLogList" :class="sms.status == 1  ? 'bg-sms-success' : 'bg-warning'">
        <th scope="row">{{ ++index }}</th>
        <th scope="row">{{ sms.title }}</th>
        <td>
          <p> {{ sms.sms_text }} </p>
        </td>
        <td>{{ sms.phone_number }}</td>
        <td>{{ sms.status == 1 ? 'موفق' : 'ناموفق'}}</td>
        <td >{{sms.customer ? sms.customer.name : ''}}</td>
        <td >{{sms.date}}</td>
      </tr>
      </tbody>
    </table>
    <Page v-if="totalItems" :total="totalItems" class="text-center mt-3"
          :page-size="perPage"
          @on-change="getNewDate" @on-page-size-change="getPerPage" show-sizer  />
  </div>

</template>

<script>
  export  default  {
    name : 'sms-logs' ,
    props : ['smsLogList' , 'perPage' , 'totalItems'] ,
    methods : {
      getNewDate(page){
        this.$emit('setPage' , page)
      },
      getPerPage(perPage){
        this.$emit('setPerPage' , perPage)
      }
    }
  }
</script>

<style>
  .bg-sms-success {
    background: #0f0;
  }
  .bg-sms-error {
    background: #f32c2c;
  }
</style>
