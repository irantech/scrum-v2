<template>
  <section class="row">
    <div class="col-12 col-md-6">
      <Card class="mb-2">
        <Form ref="formInline" class="row p-2">
          <FormItem prop="startDate"  class="col-12 col-md-5 p-1 mb-0">
            <client-only>
              <date-picker auto-submit format="jYYYY-jMM-jDD" :max="todayDate"  popover="bottom-right" clearable v-model="form.startDate" placeholder="از تاریخ"/>
            </client-only>
          </FormItem>
          <FormItem prop="endDate"  class="col-12 col-md-5 p-1 mb-0">
            <client-only>
              <date-picker auto-submit format="jYYYY-jMM-jDD" popover="bottom-right" clearable v-model="form.endDate" :min="form.startDate" placeholder="تا تاریخ"/>
            </client-only>
          </FormItem>
          <FormItem class="col-2 p-1 mb-0">
            <Button type="primary" @click="filterByDate('formInline')" class="w-100">فیلتر</Button>
          </FormItem>
        </Form>
        <div class="chart">
          <adminChartLineChart :data="contractCountInYear" :labels="contractMonthInYear" :height="400" />
        </div>
      </Card>
    </div>
    <div class="col-12 col-md-6">
<!--      <Card title="لیست کارهای شما">-->
<!--        <List :loading="loading">-->
<!--          <Alert v-if="unread.length === 0 " type="warning" class="text-center">-->
<!--            لیست کارهای شما خالی است.-->
<!--          </Alert>-->
<!--          <ListItem v-for="(job , index) in unread" :key="index" class="border-bottom-0 py-1">-->
<!--          <div class="col-11 mx-auto p-2 unread-box" style="box-shadow: -1px 0px 4px -2px #aaa;">-->
<!--            <div class="mark-btn p-1 " style="position: absolute;top: 0;right: -36px; ">-->
<!--              <Tooltip content="دیدم" placement="bottom">-->
<!--                <Button  @click="markAsRead(job.id)"-->
<!--                         class="border d-flex justify-content-center align-items-center float-left read-btn">-->
<!--                </Button>-->
<!--              </Tooltip>-->
<!--            </div>-->
<!--            <p class="w-100 py-1 read-title">-->
<!--              <Icon type="md-checkmark-circle-outline text-danger"/>-->
<!--              {{ job.data.contract_title }} ({{ job.data.checklist_title }})-->
<!--              <nuxt-link @click.native="markAsRead(job.id)" :to="`/admin/contract-checklist/${job.data.checklist_contract}`">-->
<!--                <i class="fa fa-external-link-alt" ></i>-->
<!--              </nuxt-link>-->
<!--              <span class="float-left ml-1">{{ job.created_at }} </span>-->
<!--            </p>-->
<!--            <p class="mb-1 pr-2" style="font-size: 11px">-->
<!--              {{ job.data.description }}-->
<!--            </p>-->
<!--          </div>-->
<!--        </ListItem>-->
<!--        </List>-->
<!--      </Card>-->
    </div>
  </section>
</template>

<script>
export default {
  name : 'dashboard_contract-report' ,
  props : ['contractCountInYear' , 'contractMonthInYear'],
  data() {
    return {
      form: {
        startDate : null ,
        endDate : null
      },
      loading : false,
    };
  },
  methods : {
    filterByDate() {
      this.$store.dispatch('admin/contract/countContractInMonth' , {
        form : {
          startDate : this.form.startDate ,
          endDate : this.form.endDate
        }
      })
    },
    markAsRead(id) {
      this.$store.dispatch('auth/markAsRead' , {id : id})
    }
  } ,
  computed : {
    todayDate() {
      return  this.$moment().utc().format('jYYYY-jMM-jDD')
    },
    // unread(){
    //   return this.$store.getters['auth/unreadTodos']
    // }
  },
  // created() {
  //   this.loading = true
  //   this.$store.dispatch('auth/getToDoList').then(res => {
  //     this.loading = false
  //   })
  // }
};
</script>

<style scoped>
.title {
  font-family: "Quicksand", "Source Sans Pro", -apple-system, BlinkMacSystemFont,
  "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  display: block;
  font-weight: 400;
  font-size: 100px;
  color: #2E495E;
  letter-spacing: 1px;
  font-size: 6em;
}
.green {
  color: #00C48D;
}

.subtitle {
  font-weight: 300;
  font-size: 1em;
  color: #2E495E;
  word-spacing: 5px;
  padding-bottom: 15px;
}

.links {
  padding-top: 15px;
}
</style>
