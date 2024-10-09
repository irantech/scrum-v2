<template>
  <div>
    <adminIncludeCrumb name="تعطیلات رسمی" />
    <adminHolidayList :holiday_list="holidayList" />
  </div>

</template>

<script>
import {mapState} from "vuex";

export default {
  name : 'holidays' ,
  layout : 'admin',
  middleware : 'holidayListAccess' ,
  created() {
    this.$store.dispatch('admin/holiday/LoadAdminHoliday' )
  },
  computed :{
    ...mapState('admin/holiday' , ['holidayList']),
  }
}
</script>
