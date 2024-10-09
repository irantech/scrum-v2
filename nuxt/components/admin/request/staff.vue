<template>
  <div class="row">

    <div v-for="(request , index) in staff_request" class="col-6 mt-4">
      <adminRequestStaffItem :request="request"></adminRequestStaffItem>
    </div>
  </div>
</template>

<script>
  export  default  {
      props : ['staff_request']
  }
</script>

