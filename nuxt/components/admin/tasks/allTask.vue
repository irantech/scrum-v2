<script>
import {mapState} from "vuex";


export default {
  data() {
    return{
      searchLoading : false ,
    }
  },
  name: "allTask",
  props : ['task_all','searchForm','not_assign_tasks'] ,
  computed :{
    ...mapState('admin/user' , ['userList']) ,
  },
  methods : {
    search() {
      this.$emit('setSearchData' , this.searchForm)
    },
  },
}
</script>

<template>
  <div>

    <section class="scrum-task">
      <div class="container">
        <div class="col-12 m-auto">
          <Card class="row mb-2">
            <Form  v-model="searchForm" class="row" ref="searchForm"
                   @submit.native.prevent="search('searchForm')">

              <div class="col-12 col-md-4">
                <FormItem class="mb-2">
                  <Select search v-model="searchForm.referrer"  clearable placeholder="ایجادکننده تسک">
                    <Option v-for="item in userList" :value="item.name" :key="item.id"  >{{ item.name }}</Option>
                  </Select>
                </FormItem>
              </div>
              <div class="col-12 col-md-4">
                <FormItem prop="section" class="mb-2" >
                  <Select v-model="searchForm.receiver_delivery" placeholder="اجراکننده تسک">
                    <Option v-for="(section , index) in userList"  :key="index" :value="section.id">{{ section.name }}</Option>
                  </Select>
                </FormItem>
              </div>
              <div class="col-12 col-md-4">
                <FormItem class="mb-2">
                  <client-only>
                    <date-picker popover="bottom-right" auto-submit
                                 format="jYYYY-jMM-jDD"
                                 v-model="searchForm.start_date"
                                 placeholder="تاریخ ایجاد- از" clearable />
                  </client-only>
                </FormItem>
              </div>
              <div class="col-12 col-md-4">
                <FormItem class="mb-2">
                  <client-only>
                    <date-picker popover="bottom-right" format="jYYYY-jMM-jDD"  auto-submit
                                 v-model="searchForm.end_date" placeholder=" تاریخ ایجاد - تا "
                                 :min="searchForm.start_date"  clearable/>
                  </client-only>
                </FormItem>
              </div>
              <div class="col-4">
                <Button type="success" :loading="searchLoading" html-type="submit">جستجو</Button>
              </div>
            </Form>
          </Card>
        </div>
        <div class="parent-box-all-task">
          <h3 class="title-tasks">تسک های تایم داده شده</h3>
          <div class="box-all-task" v-for="(contract , tasks_index) in task_all" :key="tasks_index"  v-if="contract.tasks.length">
            <div  class="title-all-task">
              <div v-if="contract.customer" class="">{{contract.customer.name}}</div>
              <div class="">{{contract.max_days_left}} روز</div>
            </div>
            <div class="parent-scrum-task">
              <a v-for="(task , index) in contract.tasks" :key="index" href="javascript:" class="scrum-task-item">
                {{task.tasks}}
                <div class="parent-title-calendar">
                  <div class="parent-title-scrum-task-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="" class="bi bi-tags" viewBox="0 0 16 16">
                      <path d="M3 2v4.586l7 7L14.586 9l-7-7zM2 2a1 1 0 0 1 1-1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 2 6.586z"/>
                      <path d="M5.5 5a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1m0 1a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3M1 7.086a1 1 0 0 0 .293.707L8.75 15.25l-.043.043a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 0 7.586V3a1 1 0 0 1 1-1z"/>
                    </svg>
                    <p>
                      {{ task.title }}
                    </p>
                  </div>
                  <div class="parent-calendar-scrum-task-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="" class="bi bi-calendar2" viewBox="0 0 16 16">
                      <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1z"/>
                      <path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5z"/>
                    </svg>
                    <span>{{ task.created_at }}</span>
                  </div>
                </div>
                <div class="division-labor">
                  <div class="creation">
                    <Avatar   :src="`${$env.UPLOAD_URL}${task.user_owner.avatar}`"  />
                    <span>{{ task.user_owner.name }}</span>
                  </div>
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="" class="bi bi-arrow-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                  </svg>
                  <div class="recipient">
                    <Avatar v-for="(img , index) in task.user_list" :key="index"  :src="`${$env.UPLOAD_URL}${img.avatar}`"  />
                  </div>
                </div>
                <div class="remaining-day">
                  <strong>{{task.days_passed_since_making_task}}</strong>
                  <span>از زمان ایجاد  تسک گذشته</span>
                </div>
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="scrum-task mt-4">
      <div class="container">
        <div class="parent-scrum-task2">
          <h3 class="title-tasks mb-2">تسک های تایم داده نشده</h3>
          <div class="parent-contract-all">
            <article  v-for="(task , index) in not_assign_tasks" :key="index"  class="scrum-task-item">

              {{task.tasks}}

              <div class="parent-title-calendar">
                <div class="parent-title-scrum-task-item">
                  <svg class="svg-clock" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.3.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M48 106.7V56c0-13.3-10.7-24-24-24S0 42.7 0 56V168c0 13.3 10.7 24 24 24H136c13.3 0 24-10.7 24-24s-10.7-24-24-24H80.7c37-57.8 101.7-96 175.3-96c114.9 0 208 93.1 208 208s-93.1 208-208 208c-42.5 0-81.9-12.7-114.7-34.5c-11-7.3-25.9-4.3-33.3 6.7s-4.3 25.9 6.7 33.3C155.2 496.4 203.8 512 256 512c141.4 0 256-114.6 256-256S397.4 0 256 0C170.3 0 94.4 42.1 48 106.7zM256 128c-13.3 0-24 10.7-24 24V256c0 6.4 2.5 12.5 7 17l72 72c9.4 9.4 24.6 9.4 33.9 0s9.4-24.6 0-33.9l-65-65V152c0-13.3-10.7-24-24-24z"/></svg>
                  <p>
                    {{ task.title }}

                  </p>
                </div>
<!--                <div class="parent-contract-number">-->
<!--                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-feather" viewBox="0 0 16 16">-->
<!--                    <path d="M15.807.531c-.174-.177-.41-.289-.64-.363a3.8 3.8 0 0 0-.833-.15c-.62-.049-1.394 0-2.252.175C10.365.545 8.264 1.415 6.315 3.1S3.147 6.824 2.557 8.523c-.294.847-.44 1.634-.429 2.268.005.316.05.62.154.88q.025.061.056.122A68 68 0 0 0 .08 15.198a.53.53 0 0 0 .157.72.504.504 0 0 0 .705-.16 68 68 0 0 1 2.158-3.26c.285.141.616.195.958.182.513-.02 1.098-.188 1.723-.49 1.25-.605 2.744-1.787 4.303-3.642l1.518-1.55a.53.53 0 0 0 0-.739l-.729-.744 1.311.209a.5.5 0 0 0 .443-.15l.663-.684c.663-.68 1.292-1.325 1.763-1.892.314-.378.585-.752.754-1.107.163-.345.278-.773.112-1.188a.5.5 0 0 0-.112-.172M3.733 11.62C5.385 9.374 7.24 7.215 9.309 5.394l1.21 1.234-1.171 1.196-.027.03c-1.5 1.789-2.891 2.867-3.977 3.393-.544.263-.99.378-1.324.39a1.3 1.3 0 0 1-.287-.018Zm6.769-7.22c1.31-1.028 2.7-1.914 4.172-2.6a7 7 0 0 1-.4.523c-.442.533-1.028 1.134-1.681 1.804l-.51.524zm3.346-3.357C9.594 3.147 6.045 6.8 3.149 10.678c.007-.464.121-1.086.37-1.806.533-1.535 1.65-3.415 3.455-4.976 1.807-1.561 3.746-2.36 5.31-2.68a8 8 0 0 1 1.564-.173"/>-->
<!--                  </svg>-->
<!--                  <span>{{ task.contract.title }}</span>-->
<!--                </div>-->
                <div class="parent-calendar-scrum-task-item">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="" class="bi bi-calendar2" viewBox="0 0 16 16">
                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1z"/>
                    <path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5z"/>
                  </svg>
                  <span>{{ task.created_at }}</span>
                </div>
              </div>
              <div class="division-labor">
                <div class="creation">
                  <Avatar   :src="`${$env.UPLOAD_URL}${task.user_owner.avatar}`"  />
                  <span>{{ task.user_owner.name }}</span>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="" class="bi bi-arrow-left" viewBox="0 0 16 16">
                  <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                </svg>
                <div class="recipient">
                  <Avatar v-for="(img , index) in task.user_list" :key="index"  :src="`${$env.UPLOAD_URL}${img.avatar}`"  />
                </div>
              </div>
            </article>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
a:hover {
  text-decoration: none
}
.parent-scrum-task{
  display: flex;
  width: 100%;
  flex-direction: column;
  border: 1px solid #eee;
  border-radius: 0 0 12px 12px;
  overflow: hidden;
  box-shadow: 0 1px 2px 0 rgba(0,0,0,.03);
}
.parent-scrum-task a{
  display: flex;
  align-items: center;
  width: 100%;
  gap: 30px;
  padding: 5px 10px;
  background: #fff;
}
.parent-scrum-task a p{
  margin-bottom: 0;
  color: #333;
  font-size: 14px;
}
.division-labor{
  display: flex;
  align-items: center;
}
.parent-title-calendar{
  display: flex;
  align-items: center;
  gap: 20px;
}
.parent-title-scrum-task-item{
  display: flex;
  align-items: center;
  gap: 6px;
}
.parent-calendar-scrum-task-item{
  display: flex;
  align-items: center;
  gap: 5px;
}
.parent-calendar-scrum-task-item span{
  font-size: 12px;
  color: gray;
}
.division-labor{
  display: flex;
  align-items: center;
  gap: 30px;
}
.creation{
  display: flex;
  align-items: center;
  gap: 5px;
}
.creation img{
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: 2px solid #ccc;
}
.creation span{
  font-size: 12px;
  color: gray;
}
.bi-arrow-left{
  width: 20px;
  height: 20px;
  fill: gray;
}
.recipient{
  display: flex;
  align-items: center;
  gap: 3px;
}
.recipient img{
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: 2px solid #ccc;
}
.bi-calendar2{
  fill: grey;
  width: 14px;
}
.parent-box-all-task{
  display: flex;
  flex-direction: column;
  gap: 15px;
  margin-top: 30px;
  margin-bottom: 50px;
}
.title-all-task{
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.title-all-task div{
  background: #515a6e;
  border-radius: 12px 12px 0 0;
  font-size: 12px;
  color: #fff;
  padding: 7px 10px 7px;
}
.parent-scrum-task .scrum-task-item:nth-child(even){
  background: #f2f4ff;
}
.remaining-day{
  display: flex;
  align-items: center;
  gap: 6px;
  transition: all ease .3s;
}
.remaining-day strong{
  font-size: 14px;
  color: #333;
}
.remaining-day span{
  font-size: 14px;
  color: gray;
}
.title-tasks{
  color: #000;
}
.parent-contract-all article{
  display: flex;
  align-items: center;
  width: 100%;
  gap: 30px;
  padding: 5px 10px;
  border: 1px solid #eee;
  border-radius: 12px;
  background: #f9f9f9;
}
.parent-scrum-task2{
  display: flex;
  width: 100%;
  flex-direction: column;
}
.parent-contract-all{
  display: flex;
  flex-direction: column;
  gap: 5px;
}
.ivu-avatar{
  width: 30px;
  height: 30px;
  line-height: 30px;
}
.svg-clock{
  width: 16px;
  height: 16px;
  fill: #757575;
}

</style>
