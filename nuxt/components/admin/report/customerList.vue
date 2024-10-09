<template>
  <div class="col-12 col-md-11 m-auto">
    <Card class="row mb-2">
      <Form  v-model="form" class="row" ref="searchForm"
             @submit.native.prevent="search('searchForm')">
        <div class="col-12 col-md-4">
          <FormItem class="mb-2">
            <Select search v-model="form.checklist" clearable placeholder="جستجو چک لیست">
              <Option v-for="item in checklists" :value="item.id" :key="item.id">{{ item.title }}</Option>
            </Select>
          </FormItem>
        </div>
        <div class="col-12 col-md-4">
          <FormItem class="mb-2">
            <Input  type="text" search v-model="form.contract_title" placeholder="نام مشتری"/>
          </FormItem>
        </div>
<!--        <div class="col-12 col-md-4">-->
<!--          <FormItem class="mb-2">-->
<!--            <Input type="text" search v-model="form.contract_code" placeholder="شماره قرارداد"/>-->
<!--          </FormItem>-->
<!--        </div>-->
        <div class="col-12 col-md-4">
          <FormItem class="mb-2">
            <Select search v-model="form.status" clearable  placeholder="وضعیت چک لیست">
              <Option v-for="item in status_list" :value="item.id" :key="item.id">{{ item.title }}</Option>
            </Select>
          </FormItem>
        </div>
        <div class="col-12 col-md-4">
          <FormItem class="mb-2">
            <Select search v-model="form.section"  clearable placeholder="انتخاب بخش مورد نظر">
              <Option v-for="item in sectionList" :value="item.id" :key="item.id">{{ item.title }}</Option>
            </Select>
          </FormItem>
        </div>
        <div class="col-2">
          <Button type="success" :loading="searchLoading" html-type="submit">جستجو</Button>
        </div>
      </Form>
    </Card>


    <Card class="row mb-3" v-if="sectionList">
      <div class="d-flex">
        <div v-for="section in sectionList" :key="section.id">
           <span class="p-2 mx-2" :style="`background-color:${section.color}`">{{section.title}}</span>
        </div>
      </div>
    </Card>

    <div class="row">
      <Col span="24" class="px-2 my-1 d-flex justify-center" v-if="dataLoading">
        <Spin size="large"> </Spin>
      </Col>
      <div v-else v-for="(report , index) in customerReports" class="col-4 mb-3" :key="index">
        <p class="font-weight-bold p-2">
          {{ report.name }}
          <nuxt-link class="text-info" :to="`/admin/report-customers/customer/${report.id}`">
            <i class="fa fa-external-link-alt"></i>
          </nuxt-link>
        </p>
        <div>
          <report-customer-single  v-for="(customer , index) in report.contracts"
                                   :customer="customer" :key="index"/>
        </div>
      </div>
    </div>
    <Page v-if="totalItems" :total="totalItems" class="text-center mt-3"
          :page-size="perPage"
          @on-change="getNewDate" :disabled="dataLoading"  @on-page-size-change="getPerPage" show-sizer  />
  </div>
</template>

<script>
  import ReportCustomerSingle from "./customer";
  export default {
    name : 'report-customer-list' ,
    components: {ReportCustomerSingle},
    props : ['customerReports' , 'dataLoading' , 'totalItems' , 'checklists' , 'sectionList' , 'form' , 'perPage'],
    data() {
        return {
          searchLoading : false ,
          value1 : '0' ,
          searchText : '' ,
          status_list : [
            {
              id : 1  ,
              title : 'تکمیل شده'
            },
            {
              id : 2 ,
              title : 'ناقص'
            }
          ]
        }
    },
    methods : {
      getNewDate(page){
        this.$emit('setPage' , page)
      },
      getPerPage(perPage){
        this.$emit('setPerPage' , perPage)
      },
      search() {
        this.$emit('setSearchData' , this.form)
      }

    }
  }
</script>
