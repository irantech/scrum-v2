<template>
  <div>
    <adminIncludeCrumb name="اس ام اس و ..."/>
    <div>
      <table class="table table-bordered table-striped">
        <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">نام</th>
          <th scope="col">شماره تلفن</th>
          <th scope="col">وضعیت</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(item,index) in this.items" :key="index">
          <th scope="row">{{ index }}</th>
          <td></td>
          <td>Otto</td>
          <td>@mdo</td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
<script>
export default {
  name : 'number-list-sms' ,
  layout : 'admin',

  data() {
    return{
      items:"",
    }
  },
  created() {
    this.$axios.post(`https://api.ladyscarf.ir/api/curl/all-data` , {
      per_page: "5"
    })
      .then(response => {
        this.items = response;
        console.log(this.items.data.info.data)
      })
  },
  computed:{
  }
}
</script>

