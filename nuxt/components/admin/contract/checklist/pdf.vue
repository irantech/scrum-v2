<template>
  <div class="col-10 m-auto mt-4">
    <Card ref="content" id="content" >
      <p slot="title">{{getTitle}}</p>
      <div>
        <div class="mb-2 contract-pdf-font">
          <div class="d-flex">
            <div class="border p-1 d-flex justify-content-center align-items-center contract-info-cell">
              شماره قرارداد :
              <span>{{contract.contract_code}}</span>
            </div>
            <div class="border p-1 d-flex justify-content-center align-items-center contract-info-cell">
              نام شرکت :
              <span>{{contract.customer}}</span>
            </div>
            <div class="border p-1 d-flex justify-content-center align-items-center contract-info-cell">
              نام شرکت :
              <span>{{contract.customer}}</span>
            </div>
            <div class="border p-1 d-flex justify-content-center align-items-center contract-info-cell">
              نام دامنه :
              <a :href="contract.domain_link" class="btn-link" target="_blank">{{contract.domain_link}}</a>
            </div>
          </div>
          <div class="d-flex">
            <div class="border p-1 d-flex justify-content-center align-items-center contract-info-cell">
              عنوان قرارداد :
              <span>{{contract.title}}</span>
            </div>
            <div class="border p-1 d-flex justify-content-center align-items-center contract-info-cell">
              تاریخ قرارداد :
              <span>{{contract.sign_date}}</span>
            </div>
            <div class="border p-1 d-flex justify-content-center align-items-center contract-info-cell">
              تاریخ شروع قرارداد:
              <span> {{contract.start_date}}</span>
            </div>
            <div class="border p-1 d-flex justify-content-center align-items-center contract-info-cell">
              تاریخ پایان قرارداد :
              <span>{{contract.end_date}}</span>
            </div>
          </div>
          <div class="d-flex flex-wrap">
            <div class="border p-1 d-flex justify-content-center align-items-center contract-info-cell" v-for="(section , index) in contractTitleChecklistSection" :key="index">
              <span>{{ section.section.title }}</span> :  {{section.user.name}}
            </div>
          </div>
        </div>
        <div class="demo-spin-article" v-if="contractTitleChecklistSection.length !== 0" >
          <table class="table table-striped table-bordered contract-pdf-font">
            <thead>
            <tr>
              <th scope="col" class="contract-pdf-cell-size">#</th>
              <th scope="col">توضیحات</th>
              <th scope="col" class="text-center contract-pdf-info-cell-size"
                  v-for="(section , index) in sectionList" :key="index">
                <span v-if="section.order === 3">
                    فنی
                </span>
                <span v-else-if="section.order === 4">
                   گرافیک
                </span>
                <span v-else>
                  {{ section.title }}
                </span>
              </th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(item , index) in contractTitleChecklist" :key="index">
              <th scope="row">{{ index }}</th>
              <td>{{ item[0].titleChecklists.title }}</td>
              <td v-for="(section , index )  in sectionList" :key="index">
                <div v-if="$store.getters['admin/checklistContract/hasSection'](section.id , item)">
                  <div v-for="(i , index) in item" :key="index"
                            v-if="i.section.id === section.id"
                            class="d-flex align-items-center justify-content-center">
                    <span v-if="i.status === 1">
                      🗹
                    </span>
                    <span v-else>
                       ☐
                    </span>
                  </div>
                </div>
                <div class="d-flex align-items-center justify-content-center" v-else>---</div>
              </td>
            </tr>
            <tr>
              <td><Icon type="md-clock" class="checklist-icon-size"/></td>
              <td>مدت زمان انجام پروژه</td>
              <td v-for="(section , index)  in sectionList" :key="index" class="text-center">
                <div v-if="sumLoading" class="d-flex justify-content-center align-items-center">
                  <Spin size="small"></Spin>
                </div>
                <div v-else>
                  {{$store.getters["admin/checklistContract/getSectionDuration"](section.id)}}
                </div>
              </td>
            </tr>
            <tr>
              <td><i class="fas fa-signature checklist-icon-size"></i></td>
              <td>امضای کارمندان</td>
              <td v-for="(section , index)  in sectionList" :key="index" class="text-center">
                <div v-if="section.order !== 1 && section.order !== 6">
                  <adminContractChecklistSignsStaff :section="section" />
                </div>
              </td>
            </tr>
            <tr>
              <td><i class="fas fa-signature checklist-icon-size"></i></td>
              <td>امضای مدیر</td>
              <td v-for="(section , index)  in sectionList" :key="index" class="text-center">
                <div>
                  <adminContractChecklistSignsSupport  v-if="section.order === 2"  :section="section"/>
                  <hr v-if="section.order === 2" class="my-2">
                  <adminContractChecklistSignsManager :section="section" />

                </div>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
        <div v-else>
          <alert class="text-center">برای ادامه کار این چک لیست حداقل باید به یک کارمند اختصاص داده شود.</alert>
        </div>
      </div>
    </Card>
    <Button type="info" @click="createPDF" class="mt-2" :loading="pdf_loading">
      ذخیره pdf
    </Button>

  </div>
</template>

<script>

export default  {
  name : 'contract-titleChecklist-form' ,
  props : ['checklistContract','contractTitleChecklist','contractTitleChecklistSection', 'section_list' , 'sumLoading'] ,
  data() {
    return {
      contract : '' ,
      checklist : '' ,
      pdf_loading : false
    }
  },
  methods : {
    async createPDF () {
      if (process.client) {
        const { default: html2pdf } = await import(/* webpackChunkName: "flickity" */ 'html2pdf.js');
        this.pdf_loading = true
        var element = document.getElementById('content');
        var opt = {
          margin : 0 ,

          filename: this.contract.customer + '(' + this.checklist.title + ')',

          image: {
            type: 'jpeg',
            quality: 0.98
          },
          enableLinks: true,
          html2canvas: {
            scale: 2,
            dpi: 800,
            useCORS: true ,
          },
          jsPDF: {
            unit: 'in',
            format: 'a4',
            orientation: 'portrait'
          }
        };
        html2pdf(element, opt).then(() =>{
          this.pdf_loading = false
        }).catch(error => {
           this.pdf_loading = false
          console.log(error)
        });
      }
    },
  },
  computed : {
    sectionList(){
      if(this.checklist) {
        let sections = this.checklist.sections.map(x => x.id)
        if(this.section_list)
          return this.section_list.filter(item => {
            return sections.includes(item.id)
          })
      }
    },
    getTitle() {
      return `   چک لیست  ${ this.checklist? this.checklist.title : ''} `
    },
  },
  created() {
    this.contract = this.checklistContract.contract
    this.checklist = this.checklistContract.checklist
  } ,
}
</script>
