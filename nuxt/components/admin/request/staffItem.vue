<template>
  <div class="card_c">
    <div class="request_main">
      <h2>تغییر زمان بندی</h2>
      <h3>{{ request.request_item.title }}</h3>
    </div>
    <div class="request_main">
      <p class="mt-1">تاریخ ایجاد پیشنهادی: {{ start_date }}</p>
      <p class="mt-1">تاریخ پایان پیشنهادی: {{ end_date }}</p>
      <p class="mt-1"> {{ staff_reason }} </p>
    </div>
    <div class="request_footer" v-if="request.has_confirmed == null">
      <h2>هنوز مدیر درخواست شما را ندیده است.</h2>
    </div>
    <div class="request_footer" v-else>
      <h2>کامنت مدیر: {{ request.manager_reason }}</h2>
      <span class="deny" v-if="request.has_confirmed == '0'">رد شده</span>
      <span class="accept" v-else-if="request.has_confirmed == '1'">تایید شده</span>
    </div>

  </div>
</template>

<script>
  export  default  {
    props: ['request'] ,
    computed : {
      staff_reason() {
        let request_reason = JSON.parse(this.request.reason)
        return request_reason.reason
      },
      start_date() {
        let request_reason = JSON.parse(this.request.reason)
        return request_reason.starting_time
      },
      end_date() {
        let request_reason = JSON.parse(this.request.reason)
        return request_reason.ending_time
      },
    }

  }
</script>
