<template>
  <div class="col-12 col-md-10 m-auto">
    <Card>
      <p slot="title">لیست قراردادها</p>
      <div class="row mb-2">
        <div class="col-12 col-md-4">
          <Form>
            <FormItem class="mb-2">
              <Input search placeholder="جستجو مشتریان" v-model="customerName" @on-change="setUrl()"  />
            </FormItem>
          </Form>
        </div>
        <div class="col-12 col-md-4">
            <Form>
              <FormItem class="mb-2">
                <Input search placeholder="جستجو کد قرارداد" v-model="contractCode"  @on-change="setUrl()"/>
              </FormItem>
            </Form>
          </div>
        <div class="col-12 col-md-4">
          <Form>
            <FormItem class="mb-2">
              <Input search placeholder="جستجو عنوان قرارداد" v-model="contractTitle" @on-change="setUrl" />
            </FormItem>
          </Form>
        </div>

        <div class="col-12 col-md-4 mb-2">
          <client-only>
            <date-picker popover="bottom-right" :max="todayDate"  auto-submit
                         format="jYYYY-jMM-jDD"
                         v-model="startYear" @change="setUrl"
                         placeholder="تاریخ شروع" clearable />
          </client-only>
        </div>
        <div class="col-12 col-md-4">
          <client-only>
            <date-picker popover="bottom-right" format="jYYYY-jMM-jDD"  auto-submit
                         v-model="endYear" @change="setUrl" placeholder="تاریخ پایان"
                         :min="startYear"  clearable/>
          </client-only>
        </div>
      </div>
      <Col span="24" class="px-2 my-1 d-flex justify-content-center" v-if="contractLoading">
        <Spin size="large"> </Spin>
      </Col>
      <div class="row" v-else>
        <div  class="my-1 col-12 col-md-6" v-for="(contract , index) in contractList" :key="index" v-if="contract.customer">
          <adminContractSingle :contract="contract" />
        </div>
      </div>

    </Card>
  </div>
</template>
<script>

  export default {
    name : 'contractList' ,
    props : ['contractLoading'],
    data() {
      return {
        customerName : '' ,
        contractCode : '',
        startYear : null ,
        contractTitle :null ,
        endYear :'' ,
      }
    },
    created() {
      this.customerName = this.$route.query.customerName
      this.contractTitle = this.$route.query.contractTilte
      this.startYear = this.$route.query.startYear
      this.contractCode = this.$route.query.contractCode
      this.endYear = this.$route.query.endYear
    },
    methods :{
      setUrl() {
        this.$router.replace({path: "/admin/contract",
          query: {
            customerName: this.customerName ,
            contractTitle  : this.contractTitle ,
            contarct_code :  this.contractCode ,
            startYear : this.startYear
          }}).catch(()=>{})
      }
    } ,
    computed : {
      contractList() {
        console.log(this.customerName)
        return this.$store.getters['admin/contract/filterContracts'](this.customerName , this.contractTitle , this.contractCode ,
          this.startYear ? this.$moment(this.startYear).format("YYYY-MM-DD") : '' ,
          this.endYear ? this.$moment(this.endYear).format("YYYY-MM-DD") : '' )
      },
      todayDate() {
        return  this.$moment().utc().format('jYYYY-jMM-jDD')
      }
    },
  }
</script>
