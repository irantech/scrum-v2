<template>
  <div>
    <adminIncludeCrumb name="گزارش قراردادها"/>
    <report-customer-list    :customerReports="customerReports"
                             :dataLoading="dataLoading"
                             @setPage="getNewData" :totalItems="customerReportsCount"
                             :sectionList="sectionList"
                             :perPage="perPage"
                             @setPerPage="getNewPerPageDta" :checklists="checklists"
                             @setSearchData="setSearchData" :form="form"/>
  </div>
</template>

<script>
  import {mapState} from "vuex";
  import ReportCustomerList from "../../../components/admin/report/customerList";

  export default  {
    name : 'report-customers' ,
    components: {ReportCustomerList},
    layout : 'admin' ,
    data(){
      return {
        currentPage : 1 ,
        dataLoading : false,
        perPage : 40 ,
        form : {
          checklist : '' ,
          contract_title : '' ,
          contract_code : '' ,
          section : '' ,
          status : ''
        }
      }
    },
    created() {
      this.setForm();
      this.getCustomersReport();
      this.$store.dispatch('admin/checklist/getChecklists')
      this.$store.dispatch('admin/section/LoadAdminSections')
    },
    methods: {
      getNewPerPageDta(perPage){
        console.log(perPage)
        this.perPage = perPage
        this.currentPage = 1
        this.getCustomersReport()
      },
      getNewData(page){
        this.currentPage = page
        this.getCustomersReport()
      },
      getCustomersReport () {
        this.dataLoading = true
        this.form.per_page = this.perPage
        this.$store.dispatch('admin/reports/customer/getCustomerReports' , {
          page : this.currentPage ,
          form : this.form
        })
        .then(() => {
          this.dataLoading = false
        })
      } ,
      setSearchData(formData) {
        this.form = formData
        this.$router.replace({path: "/admin/report-customers",
        query: {
          checklistTitle : this.form.checklist ,
          contractTitle  : this.form.contract_title ,
          contractCode   : this.form.contract_code ,
          status         : this.form.section,
          section        : this.form.status,
        }}).catch(()=>{})

        this.getCustomersReport()
      },
      setForm(){
        this.form.checklist = this.$route.query.checklistTitle
        this.form.contract_title = this.$route.query.contractTitle
        this.form.contract_code = this.$route.query.contractCode
        this.form.section = this.$route.query.status ? parseInt(this.$route.query.status) :''
        this.form.status = this.$route.query.section ? parseInt(this.$route.query.section) : ''
      }
    },
    computed : {
      ...mapState('admin/reports/customer' , ['customerReports']),
      ...mapState('admin/reports/customer' , ['customerReportsCount']),
      ...mapState('admin/checklist' , ['checklists']) ,
      ...mapState('admin/section' , ['sectionList']) ,
    }
  }
</script>
