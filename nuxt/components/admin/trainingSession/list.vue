<template>
  <div class="col-11 m-auto">
    <table class="table table-hover table-bordered table-striped">
      <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">تاریخ</th>
        <th scope="col">ساعت</th>
        <th scope="col">برگزارکننده</th>
        <th scope="col">مشتری</th>
        <th scope="col">مدت زمان برگزاری</th>
        <th scope="col">آنلاین/حضوری</th>
        <th scope="col">محل برگزاری</th>
        <th scope="col">آدرس</th>
        <th scope="col">وضعیت</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(trainingSession , index) in trainingSessionList" :key="trainingSession.id">
        <th scope="row">{{index}}</th>
        <td>{{ trainingSession.session_date }}</td>
        <td>{{ trainingSession.session_time }}</td>
        <td>{{ trainingSession.user.name }}</td>
        <td><nuxt-link :to="`/admin/contract-checklist/${trainingSession.checklist_contract.id}`">{{ trainingSession.checklist_contract.contract.customer.name }}</nuxt-link></td>
        <td>{{ trainingSession.duration }}</td>
        <td :colspan="trainingSession.location_status == 'online' ? 2 : 0">{{ trainingSession.location_status == 'online' ? 'آنلاین' : 'حضوری'}}</td>
        <td v-if="trainingSession.location_status != 'online'" >{{ trainingSession.location_place != 'in' ? 'ایران تکنولوژی' : 'محل مشتری'}}</td>
        <td>{{ trainingSession.address }}</td>
        <td class="cursor-pointer">
          <a v-if="trainingSession.status == 'done'" @click="openModal(trainingSession.id)" class="text-danger">
            <Icon type="ios-settings" />
          </a>
          {{ setStatus(trainingSession.status) }}
        </td>
      </tr>
      </tbody>
    </table>



    <Modal v-model="contributorModal" width="950">
      <p slot="header" style="color:#f60;text-align:center">
        <Icon type="ios-information-circle"></Icon>
        <span>نمایندگان حاضر در این جلسه </span>
      </p>
      <div style="text-align:center">
        <table class="table table-hover table-bordered table-striped">
          <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">نام نماینده </th>
            <th scope="col">شماره موبایل</th>
            <th scope="col">لینک اجتماعی اول </th>
            <th scope="col">لیمک اجتماعی دوم</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="(contributor , index) in activeContributors" :key="contributor.id">
            <th scope="row">{{ ++index}}</th>
            <td>{{ contributor.name }}</td>
            <td>{{ contributor.mobile }}</td>
            <td>{{ contributor.social_link1 }}</td>
            <td>{{ contributor.social_link1  }}</td>
          </tr>
          </tbody>
        </table>
      </div>
      <div slot="footer">
        <Button type="error" size="large" long  @click="contributorModal =  !contributorModal">بستن</Button>
      </div>
    </Modal>

  </div>
</template>

<script>
  export  default  {
      name : 'training-session-temp' ,
      props : ['trainingSessionList'] ,
      data() {
        return {
          contributorModal : false,
          activeContributors : []
        }
      },
      methods:{
        setStatus(status) {
          switch (status) {
            case 'set_time' :
              return 'زمان تعیین شده' ;
            case 'cancel' :
              return  'کنسل شده' ;
            case 'done' :
              return  'انجام شده';
            case 'new_session' :
              return  'زمان دیگری ست شد.'

          }
        } ,
        openModal(id) {
          let trainingSession = this.trainingSessionList.find(x => x.id ==  id)
          this.activeContributors = JSON.parse(trainingSession.contributors)
          this.contributorModal = true
        }
      }
  }
</script>
