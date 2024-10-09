<template>
  <div>
    <client-only>
      <adminIncludeCrumb name="داشبورد" />
      <adminDashboardContractReport :contractMonthInYear="contractMonthInYear"
                                    :contractCountInYear="contractCountInYear"/>
    </client-only>
  </div>

</template>

<script>
import {mapState} from "vuex";

export default {
  name : 'admin' ,
  layout : 'admin',
  async fetch({store}) {
    await store.dispatch('admin/contract/countContractInMonth' , {
      form : {
        startDate : null ,
        endDate : null
      }
    })
  },
  // created() {
  //   this.$store.dispatch('admin/contract/countContractInMonth' , {
  //     form : {
  //       startDate : null ,
  //       endDate : null
  //     }
  //   })
  // },
  computed :{
    ...mapState('admin/contract' , ['contractMonthInYear']),
    ...mapState('admin/contract' , ['contractCountInYear'])
  }
}
</script>
