<template>
  <div class="col-11 m-auto">
    <table class="table table-bordered" style="color: #000 !important;">
      <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">توسط چه کسی</th>
        <th scope="col">چه کاری</th>
        <th scope="col">در چه تاریخی </th>
        <th scope="col"> چه تغییری </th>
<!--        <th scope="col">  مشتری </th>-->
<!--        <th scope="col">  تاریخ </th>-->
      </tr>
      </thead>
      <tbody>
      <tr v-for="(log , index) in logList">
        <th scope="row">{{ ++index }}</th>
        <th scope="row">{{ log.causer.name }}</th>
        <th scope="row">{{ log.todoList.title }}</th>
        <th scope="row">{{ log.todoList.created_time }}</th>
        <th scope="row">{{ log.properties }}</th>
<!--        <td>-->
<!--          <p> {{ sms.sms_text }} </p>-->
<!--        </td>-->
<!--        <td>{{  sms.phone_number }}</td>-->
<!--        <td>{{ sms.status == 1 ? 'موفق' : 'ناموفق'}}</td>-->
<!--        <td >{{sms.customer ? sms.customer.name : ''}}</td>-->
<!--        <td >{{sms.date}}</td>-->
      </tr>
      </tbody>
    </table>
  </div>

</template>

<script>
export  default  {
  name : 'logs' ,
  props : ['logList']
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
