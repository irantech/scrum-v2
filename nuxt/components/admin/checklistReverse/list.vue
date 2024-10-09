<template>
  <div class="col-12 col-md-10 m-auto">
    <Card class="p-2">
      <div class="row mb-2">
        <Form  v-model="form" class="row" ref="searchForm"
               @submit.native.prevent="search('searchForm')">
          <div class="col-12 col-md-4">
            <FormItem class="mb-2">
              <Select search v-model="form.type"  clearable placeholder="انتخاب نوع">
                <Option v-for="item in type_list" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </div>
          <div class="col-12 col-md-4">
            <FormItem class="mb-2">
              <Input  type="text" search v-model="form.contract_title" placeholder="نام مشتری"/>
            </FormItem>
          </div>
          <div class="col-12 col-md-4">
            <FormItem class="mb-2">
              <Select search v-model="form.section"  clearable placeholder="انتخاب بخش مورد نظر">
                <Option v-for="item in sectionList" :value="item.id" :key="item.id">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </div>
          <div class="col-12 col-md-4">
            <FormItem class="mb-2">
              <Select search v-model="form.user"  clearable placeholder="انتخاب فرد مورد نظر">
                <Option v-for="item in userList" :value="item.id" :key="item.id">{{ item.userName }}</Option>
              </Select>
            </FormItem>
          </div>
          <div class="col-12 col-md-4 mb-2">
            <client-only>
              <date-picker popover="bottom-right" :max="today_date"  auto-submit
                           format="jYYYY-jMM-jDD"
                           v-model="form.start_date"
                           placeholder="تاریخ شروع" clearable />
            </client-only>
          </div>
          <div class="col-12 col-md-4">
            <client-only>
              <date-picker popover="bottom-right" format="jYYYY-jMM-jDD"  auto-submit
                           v-model="form.end_date" placeholder="تاریخ پایان"
                           :min="form.start_date"  clearable/>
            </client-only>
          </div>

          <div class="col-2">
            <Button type="success" html-type="submit">جستجو</Button>
          </div>
        </Form>
      </div>
    </Card>
      <Col span="24" class="px-2 my-1 d-flex justify-content-center" v-if="data_get_loading">
        <Spin size="large"> </Spin>
      </Col>
      <div class="row" v-else>
        <div  class="my-1 col-12 checklist-reverse-panel" >
          <Divider  v-if="checklist_reverse.length > 0" orientation="center" class="my-2">چک لیست</Divider>
          <Collapse >
            <Panel v-for="(checklist , index) in checklist_reverse"  :key="index">
              <div class="col-3 pt-2">
                <nuxt-link v-if=" checklist.contract.customer" :to="`/admin/contract-checklist/${checklist.id}`">
                  {{ checklist.contract.customer.name }}
                </nuxt-link>
                <span>({{checklist.checklist.title}})</span>
              </div>
              <div class="col-9 badge-box d-flex flex-wrap">
                <div class="py-1 px-2 mx-2">
                  <Tooltip content="تعداد کل برگشت ها" placement="top">
                    <Badge status="cyan" :text="`${checklist.reverse_data.length}`"  />
                     برگشت
                  </Tooltip>
                </div>
                <div class="py-1 px-2 mx-2">
                  <Tooltip content="کل زمان تاخیر" placement="top">
                    <Badge status="volcano" :text="`${calculateExtendTime(checklist.todolist)}`"  />
                    تاخیر
                  </Tooltip>
                </div>
                <div class="py-1 px-2 mx-2"
                     v-for="type in reverse_type_list"  :key="type.name" >
                  <Tooltip :content="type.title" placement="top">
                    <Badge :status="type.type" :text="`${countType(type.name , checklist.reverse_data)}`"  />
                    {{type.title}}
                  </Tooltip>
                </div>
              </div>

              <template slot="content">
                  <adminChecklistReverseSection  v-for="(section , index) in sectionList" :section="section" :key="section.id"
                                                 :checklist_reverse="checklist.reverse_data" :todolist="checklist.todolist" :users="checklist.users"></adminChecklistReverseSection>
              </template>
            </Panel>
          </Collapse>
          <Divider v-if="tasks.length > 0" orientation="center" class="my-2">تسک</Divider>
          <Collapse>
            <Panel v-for="(task , index) in tasks"  :key="`${index}k`">
              <div class="col-3 pt-2">
                <span>({{task.title}})</span>
              </div>
              <div class="col-9 badge-box d-flex flex-wrap">
                <div class="py-1 px-2 mx-2">
                  <Tooltip content="کل زمان تاخیر" placement="top">
                    <Badge status="volcano" :text="`${calculateExtendTime(task.todolist)}`"  />
                    تاخیر
                  </Tooltip>
                </div>
                <div class="py-1 px-2 mx-2" v-if="task.delivery_delay != 0">
                  <Tooltip content="تاخیر تا زمان تحویل" placement="top">
                    <Badge status="volcano" :text="`${task.delivery_delay} روز `"  />
                    تاخیر
                  </Tooltip>
                </div>
                <div class="py-1 px-2 mx-2"
                     v-for="type in reverse_type_list"  :key="type.name" >
                  <Tooltip :content="type.title" placement="top">
                    <Badge :status="type.type" :text="`${countType(type.name , task.reverse_data)}`"  />
                    {{type.title}}
                  </Tooltip>
                </div>
              </div>
              <template slot="content">
                <adminChecklistReverseSection  v-for="(section , index) in sectionList" :section="section" :key="section.id"
                                               :checklist_reverse="task.reverse_data" :todolist="task.todolist"></adminChecklistReverseSection>
              </template>
            </Panel>
          </Collapse>
        </div>
      </div>


  </div>
</template>
<script>

export default {
  name : 'checklist-reverse' ,
  props : ['data_get_loading' , 'checklist_reverse' , 'form' ,'sectionList' , 'userList' , 'tasks'],
  data() {
    return {
      type_list : [
        {
          value : 'task' ,
          title : 'تسک'
        } ,
        {
          value : 'checklist' ,
          title : 'چک لیست'
        }
     ] ,
      reverse_type_list : [
        {
          name : 'accept_periodic_count' ,
          title : 'ادواری تایید شده',
          type : 'success'
        } ,
        {
          name : 'reject_periodic_count' ,
          title : 'ادواری تایید نشده',
          type : 'error'
        },
        {
          name : 'accept_error_count' ,
          title : 'خطا تایید شده' ,
          type : 'success'
        },
        {
          name : 'reject_error_count' ,
          title : 'خطا تایید نشده' ,
          type : 'error'
        },
        {
          name : 'accept_offer_count',
          title : 'پیشنهاد تایید شده' ,
          type : 'success'
        },
        {
          name : 'reject_offer_count',
          title : 'پیشنهاد تایید نشده' ,
          type : 'error'
        },
      ]
    }
  },
  created() {
  },
  methods :{
    search() {
      this.$emit('setSearchData' , this.form)
    },
    countType(type , reverse_data) {
      let counter = 0;
      reverse_data.forEach(reverse => {
        if(type == 'accept_error_count' && reverse.error_reverse_data )
        {
          counter += reverse.error_reverse_data.accept_count
        }else if(type == 'reject_error_count' && reverse.error_reverse_data) {
          counter += reverse.error_reverse_data.reject_count
        }
        else if(type == 'accept_offer_count' && reverse.offer_reverse_data ) {
          counter += reverse.offer_reverse_data.accept_count
        }
        else if(type == 'reject_offer_count' && reverse.offer_reverse_data ) {
          counter += reverse.offer_reverse_data.reject_count
        }
        else if(type == 'accept_periodic_count' && reverse.periodic_reverse_data) {
          counter += reverse.periodic_reverse_data.accept_count
        }
        else if(type == 'reject_periodic_count' && reverse.periodic_reverse_data) {
          counter += reverse.periodic_reverse_data.reject_count
        }
      })
      return counter

    },
    calculateExtendTime(todolist) {
      console.log('dd'  , todolist)
       let time = 0
       let day = 0
      todolist.forEach(todo => {
        if(todo.progress_extend_time) {
          let split_time = todo.progress_extend_time.split(" ");

          day = day + parseInt(split_time[0])
          // time = time + split_time[1]
        }
          })
        return day + ' روز '
        }

  } ,
  computed : {
    today_date() {
      return  this.$moment().utc().format('jYYYY-jMM-jDD')
    }
  },
}
</script>
<style scoped>

.badge-box {
  position: absolute;
  left: 40px;
  bottom: 12px;
}
.ivu-collapse > .ivu-collapse-item > .ivu-collapse-header{
  height: 100px !important;

}
</style>
