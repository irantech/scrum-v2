<template>
  <div>
    <Card class="mb-2">
      <Form  v-model="form" class="row" ref="searchForm"
             @submit.native.prevent="search('searchForm')">
        <div class="col-6 mb-3">
          <Form>
            <FormItem class="mb-2">
              <Select search v-model="form.section"  clearable placeholder="انتخاب بخش مورد نظر">
                <Option v-for="item in sectionList" :value="item.id" :key="item.id">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Form>
        </div>
        <div class="col-6 mb-3">
          <Form>
            <FormItem class="mb-2">
              <Select search v-model="form.has_confirmed"  clearable placeholder="وضعیت انجام کارها">
                <Option v-for="item in requestStatus" :value="item.name" :key="item.name">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Form>
        </div>
        <div class="col-6  mb-3">
          <client-only>
            <date-picker popover="bottom-right" :max="todayDate"  auto-submit
                         format="jYYYY-jMM-jDD"
                         v-model="form.start_time"
                         placeholder="کارها از تاریخ " clearable />
          </client-only>
        </div>
        <div class="col-6 mb-3">
          <client-only>
            <date-picker popover="bottom-right" format="jYYYY-jMM-jDD"  auto-submit
                         v-model="form.end_time" placeholder="تا تاریخ"
                         :min="form.start_time"  clearable/>
          </client-only>
        </div>

        <div class="col-2">
          <Button type="success"  html-type="submit">جستجو</Button>
        </div>
      </Form>
    </Card>
    <Col span="24" class="px-2 my-1 d-flex justify-center" v-if="loading">
      <Spin size="large"> </Spin>
    </Col>
    <div v-else class="row">
      <div v-for="(request , index) in notConfirmedRequests" class="col-6 mt-4">
        <adminRequestManagerItem    :key="index" :request="request" />
      </div>
      <Divider orientation="left" v-if="confirmedRequests.length > 0">درخواست های پاسخ داده شده</Divider>
      <div v-for="(request , index) in confirmedRequests" class="col-6 mt-4">
        <adminRequestManagerItem    :key="index" :request="request" />
      </div>
    </div>
  </div>

</template>

<script>
  export  default  {
    name : 'manger_request_list',
    props : ['request_list' , 'loading' , 'form' , 'sectionList'] ,
    data() {
      return{
        requestStatus : [
          {
            name : '-1' ,
            title : 'بررسی نشده',
          } ,
          {
            name : '0' ,
            title : 'رد شده'
          },
          {
            name : '1',
            title : 'تایید شده'

          },

        ] ,
      }
    },
    computed : {
      todayDate() {
        return  this.$moment().utc().format('jYYYY-jMM-jDD')
      },
      notConfirmedRequests() {
        return  this.request_list.filter(function(x) { return x.has_confirmed == null; })
      },
      confirmedRequests(){
        return  this.request_list.filter(function(x) { return x.has_confirmed != null; })
      },
    },
    methods : {
      search() {
        this.$emit('setSearchData' , this.form)
      }
    }
  }
</script>

