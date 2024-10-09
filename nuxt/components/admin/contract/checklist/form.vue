<script src="../../../../../../karavel/nuxtServer/nuxt.config.js"></script>
<template>
  <div class="col-12 m-auto my-4 parent-padding">
    <nuxt-link class="btn-link" v-if="$route.params.id != 207  " :to="`/admin/contract-checklist/${checklistContract.id}/pdf`">
      دریافت فایل pdf
    </nuxt-link>
    <Alert v-if="has_stopped" type="error">این پروژه به دلیل اینکه دست مشتری است معلق است.</Alert>

    <Card ref="content" id="content" v-if="$route.params.id != 207  ">
            <p slot="title" v-if="!lastLoading">{{getTitle}}</p>
            <p v-else slot="title">موارد چک لیست</p>
            <p slot="extra">
              <nuxt-link v-if="$store.getters['auth/can']('assign-title-checklist-office') ||
                         $store.getters['auth/can']('assign-title-checklist-support')  ||
                         $store.getters['auth/can']('assign-title-checklist-design') ||
                         $store.getters['auth/can']('assign-title-checklist-programmer') ||
                         $store.getters['auth/can']('assign-title-checklist-graphic') ||
                         $store.getters['auth/can']('assign-title-checklist-sales')"
                         class="link-primary" :to="`/admin/contract-checklist/${$route.params.id}/assign`">
                اختصاص دادن کاربران
              </nuxt-link>
            </p>

            <div>
              <div class="mb-2 contract-option ">
                <div class="d-flex">
                  <div class="border p-2 d-flex justify-content-center align-items-center contract-info-cell">
                    شماره قرارداد :
                    <span>{{contract.contract_code}}</span>
                  </div>
                  <div class="border p-2 justify-content-center align-items-center contract-info-cell">
                    نام شرکت :
                    <span>{{contract.customer}}</span>
                  </div>
                  <div class="border p-2 text-center justify-content-center align-items-center contract-info-cell">
                    نام شرکت :
                    <span>{{contract.customer}}</span>
                  </div>
                  <div class="border p-2 justify-content-center align-items-center contract-info-cell">
                    نام دامنه :
                    <a :href="contract.domain_link" class="btn-link" target="_blank">{{contract.domain_link}}</a>
                  </div>
                </div>
                <div class="d-flex">
                  <div class="border p-2 justify-content-center align-items-center contract-info-cell">
                    عنوان قرارداد :
                    <span>{{contract.title}}</span>
                  </div>
                  <div class="border p-2 justify-content-center align-items-center contract-info-cell">
                    تاریخ قرارداد :
                    <span>{{contract.sign_date}}</span>
                  </div>
                  <div class="border p-2 justify-content-center align-items-center contract-info-cell">
                    تاریخ شروع قرارداد:
                    <span> {{contract.start_date}}</span>
                  </div>
                  <div class="border p-2 justify-content-center align-items-center contract-info-cell">
                    تاریخ پایان قرارداد :
                    <span>{{contract.end_date}}</span>
                  </div>
                </div>
                <div class="d-flex flex-wrap">
                  <div class="border p-2 justify-content-center align-items-center contract-info-cell" v-for="(section , index) in contractTitleChecklistSection" :key="index">
                    <span>{{ section.section.title }}</span> :  {{section.user.name}}
                  </div>
                </div>
              </div>
              <div class="demo-spin-article" v-if="contractTitleChecklistSection.length !== 0" >
                <table class="table table-striped table-bordered checklist-table">
                  <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">توضیحات</th>
                    <th scope="col" class="text-center bg-dark text-light"
                        v-for="(section , index) in sectionList" :key="index"
                        :class="{'bg-info' : activeOrder === section.order , 'bg-success' : activeOrder > section.order}">
                      {{ section.title }}
                    </th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr v-for="(item , index) in contractTitleChecklist" :key="index">
                    <th scope="row">{{ index }}</th>
                    <td>{{ item[0].titleChecklists.title }}</td>
                    <td v-for="(section , index )  in sectionList" :key="index">
                      <div class="checklist-checkbox" v-if="$store.getters['admin/checklistContract/hasSection'](section.id , item)">
                        <Checkbox v-for="(i , index) in item" :key="index"
                                  v-if="i.section.id === section.id"
                                  class="d-flex align-items-center justify-content-center"
                                  :disabled="activeSection !== section.id"
                                  :value="i.status === 1"
                                  @on-change="changeStatus(i.titleChecklists.id , section.id)"
                        ></Checkbox>
                      </div>
                      <div class="d-flex align-items-center justify-content-center" v-else>-------</div>
                    </td>
                  </tr>
                  <tr>
                    <td><Icon type="md-settings" class="checklist-icon-size"/></td>
                    <td></td>
                    <td v-for="(section , index )  in sectionList" :key="index" class="text-center">
                      <div>
                        <ButtonGroup class="d-flex justify-content-center">
                          <adminContractChecklistActionsConfirm v-if="section.order !== 1"
                                                                :type="type"  :section="section.id" :sectionOrder="section.order"
                                                                :activeSection="activeSection" @setType="changeType"/>
                          <adminContractChecklistActionsSupportApprove v-if="section.order === 2" :has_stopped="has_stopped"
                                                                       :section="section.id" @setType="changeType" :type="type"/>

                          <Tooltip content="برگشت" placement="top" v-if="section.order !== 1">
                            <Button icon="md-redo" type="warning"
                                    :disabled="activeSection !== section.id || !$store.getters['auth/canReverseToSections'](section.order)
                                || (index === 0 && type === 'confirm') || (section.order === 2 && type === 'confirm')"
                                    @click="form_type = 'reverse' ; section_on = section; gotoBottom()" ></Button>
                          </Tooltip>

                          <!--                    <adminContractChecklistActionsReverse v-if="section.order !== 1" :section="section.id"-->
                          <!--                              :sectionOrder="section.order" :index="index"-->
                          <!--                              :activeSection="activeSection" :sectionList="sectionList" :type="type"-->
                          <!--                              @setType="changeType" @setSection="changeOrder"/>-->
                          <adminContractChecklistActionsManagerConfirm  :type="type"
                                                                        :section="section" :activeSection="activeSection"
                                                                        :sectionOrder="section.order" @setSection="changeOrder" @setType="changeType" />
                        </ButtonGroup>
                        <Tooltip content="ارسال به مشتری" placement="top" v-if="section.order == 2 && !has_stopped">
                          <Button icon="md-send" type="primary" class="mt-1"
                                  :disabled="is_stopping || activeSection !== section.id || !$store.getters['auth/canReverseToSections'](section.order)
                                || (index === 0 && type === 'confirm') || (section.order === 2 && type === 'confirm')"
                                  @click="sendToCustomer(section.id)" ></Button>
                        </Tooltip>
                        <Tooltip content="برگرداندن به لیست کارها" placement="top" v-if="section.order == 2 && has_stopped">
                          <Button icon="md-return-left" type="primary" class="mt-1"
                                  :disabled="is_stopping || activeSection !== section.id || !$store.getters['auth/canReverseToSections'](section.order)
                                || (index === 0 && type === 'confirm') || (section.order === 2 && type === 'confirm')"
                                  @click="returnFromCustomer()" ></Button>
                        </Tooltip>
                      </div>
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
                      <div v-if="section.order !== 1">
                        <adminContractChecklistActionsSignStaff :section="section" :activeSection="activeSection"
                                                                :signedSections="signedSections"
                                                                :contract="contract"
                                                                :checklist="checklist"
                                                                :section-order="section.order"></adminContractChecklistActionsSignStaff>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td><i class="fas fa-signature checklist-icon-size"></i></td>
                    <td>امضای مدیر</td>
                    <td v-for="(section , index)  in sectionList" :key="index" class="text-center">
                      <div>
                        <adminContractChecklistActionsSignSupport  v-if="section.order === 2" @setSection="changeOrder" @setType="changeType" :section="section"
                                                                   :activeSection="activeSection" :signedSections="signedSections"
                                                                   :section-order="section.order"></adminContractChecklistActionsSignSupport>
                        <hr v-if="section.order === 2" class="my-2">

                        <adminContractChecklistActionsSign  @setSection="changeOrder" @setType="changeType"
                                                            :section="section" :activeSection="activeSection"
                                                            :signedSections="signedSections"
                                                            :section-order="section.order"></adminContractChecklistActionsSign>

                      </div>
                    </td>
                  </tr>
                  </tbody>
                </table>
                <Spin size="large" fix v-if="lastLoading"></Spin>
              </div>
              <div v-else>
                <alert class="text-center">برای ادامه کار این چک لیست حداقل باید به یک کارمند اختصاص داده شود.</alert>
                <!--          <div v-for="(section , index) in sectionList"  :key="index">-->
                <!--            <alert type="error" class="text-center"-->
                <!--                   v-if="!$store.getters['admin/contract/isInSection'](section.id)">-->
                <!--              هنوز به-->
                <!--              "{{section.title}}"-->
                <!--              کارمندی اختصاص داده نشده-->
                <!--            </alert>-->
                <!--          </div>-->
              </div>
            </div>
          </Card>
    <Card class="my-3">
      <p slot="title">دلایل برگشت این قرارداد</p>
      <Spin class="d-flex justify-content-center" v-if="reverseLoading" size="large"></Spin>
      <div v-if="!reverseLoading">
        <Alert type="info" v-if="reverseChecklistProcess.length === 0" class="text-center">
          برگشتی برای این چک لیست  وجود ندارد.
        </Alert>
        <Collapse v-else v-model="reverse" accordion>
          <Panel v-for="(process , index) in reverseChecklistProcess" :key="index" :name="index.toString()">
            برگشت به بخش
            {{ process.section.title }}
            ( {{process.date}} )
            توسط
            {{process.user.userName}}
            <div slot="content" class="py-3">
              <adminContractChecklistActionsReverseList :process="process" :activeSection="activeSection"
                                                        :reverse="reverse"/>

              <adminContractChecklistActionsReverseReply :process_id="process.id" :activeSection="activeSection"/>
            </div>
          </Panel>
        </Collapse>
      </div>



      <div ref="reverseForm">
        <adminContractChecklistActionsReverse v-if="form_type==='reverse'" :sectionList="sectionList"
                                              :activeSection="activeSection" :section="section_on.id"
                                              :sectionOrder="section_on.order" :type="type"
                                              @reversedItem="appendItem"
                                              @openCollapse="openCollapse"
                                              @setSection="changeOrder" @setType="changeType"/>
      </div>

    </Card>
    <adminContractTrainingSessionForm v-if="$route.params.id != 207  "
                                      :checklistContract="checklistContract"
                                      :order="activeOrder" :type="type"/>
  </div>
</template>

<script>


import ScrumInput from "../../../tools/scrumInput";

export default  {
  name : 'contract-titleChecklist-form' ,
  components: {ScrumInput},
  props : ['checklistContract','contractTitleChecklist','contractTitleChecklistSection', 'customer_hold',
    'signedSections','section_list' , 'reverseChecklistProcess' , 'reverseLoading' , 'sumLoading'] ,
  data() {
    return {
      checklist_reverse : [] ,
      section_on : '',
      contract : '' ,
      checklist : '' ,
      activeOrder: 1,
      counter : 1 ,
      lastLoading : false ,
      activeSection : 1,
      type : 'confirm' ,
      close_table_row : '</tr><tr>' ,
      reverse  : '' ,
      form_type : '' ,
      has_stopped : false ,
      is_stopping : false
    }
  },
  methods : {
    openCollapse(){
      let number = this.reverseChecklistProcess.length - 1
      this.reverse = number.toString()
    },
    appendItem(value) {
      this.checklist_reverse.push(value)
    },
    changeStatus(titleChecklist , sectionId) {
      this.$store.dispatch('admin/checklistContract/changeContractTitleChecklistStatus' , {
        checklistContract  : this.$route.params.id ,
        titleChecklist  :titleChecklist ,
        form : {section : sectionId}
      })
      .then(response => {
        this.$Message.success(response.data.message)
      }).catch(error => {
        if(error.response.status === 403)
        {
          this.$Message.error(error.response.data.message)
        }
        else
          this.$Message.error(error.message)
      })
    },
    changeType(data){
      this.type = data
    },
    changeOrder(data) {
      if(data.length > 1 ) {
        this.activeOrder = data[0].order
        this.activeSection = data[0].id
      }else{
      this.activeOrder = data.order
      this.activeSection = data.id
      }
    },
    getLastProcess() {
      this.lastLoading = true
      this.$axios.get(`process/checklist-contract/${this.$route.params.id}`)
        .then(response => {
          this.lastLoading = false
          let res = response.data.data
          if(response.data.nextSection){
              this.activeOrder = response.data.nextSection.order
              this.activeSection = response.data.nextSection.id
              if(this.activeOrder === 2 && res.status === 1 )
                this.type = 'supportApprove'
              else if(this.activeOrder === 2 && res.status === 3 )
                this.type = 'managerConfirm'
              // else if(this.activeOrder === 6 &&  res.status == 2 && res.section.id !== response.data.nextSection.id)
              //   this.type = 'managerConfirm'
              else if(this.activeOrder === 1)
                this.type = 'managerConfirm'
              else{
                if(res){
                  if( res.status === 1 )
                    this.type = 'managerConfirm'
                  else if(res.status === 2 &&  res.section.id !== response.data.nextSection.id)
                    this.type = 'confirm'
                  else if( res.status === 0 )
                    this.type = 'confirm'
                  else
                    this.type = ''
                }

              }
            }
        })
    },
    gotoBottom() {
      this.$store.commit('admin/checklistContract/SET_ACTIVE_REVERSE' , 0)
      setTimeout(function(){
          window.scrollTo({ left: 0, top: document.body.scrollHeight , behavior: "smooth" });
      }, 300);

    },
    sendToCustomer(section){
      this.is_stopping = true;
      let request = {
        'contract_checklist_id' :  this.checklistContract.id,
        'section_id' : section ,
      }
      this.$axios.post('stopTodoList' , request)
        .then(response => {
          this.has_stopped =  true
          this.is_stopping = false
          this.$Message.success(response.data.message);
        }).catch(error=> {
          this.$Message.error(error.response.data.message)
      })
    },
    returnFromCustomer(){
      this.is_stopping = true
      this.$axios.get(`returnTodoList/${this.checklistContract.id}`)
        .then(response => {
          this.$Message.success(response.data.message);
          this.has_stopped = false
          this.is_stopping = false
        }).catch(error=> {
        this.$Message.error(error.response.data.message)
      })
    }
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

       // return this.$store.getters['admin/section/getSpecialSections'](this.checklist.sections)
    },
    getTitle() {
      return ` موارد چک لیست ${ this.checklist?  '"'+this.checklist.title+ '"' : ''} ${this.contract ? '('+ this.contract.title + ')' : ''}  ${this.contract ? this.contract.customer : ''}`
    },
  },
  created() {
    this.getLastProcess()
    if(this.customer_hold && this.customer_hold.ending_time == null) {
      this.has_stopped = true
    }
    this.contract = this.checklistContract.contract
    this.checklist = this.checklistContract.checklist
  } ,
}
</script>
<style>
@media (max-width: 576px) {
  .ivu-collapse-content > .ivu-collapse-content-box{
    margin-top: 0;
    padding-top: 0;
  }
  .ivu-collapse-content{
    padding: 0;
  }
  .reply-option{
    padding: 0;
  }
  .ivu-tooltip-rel i{
    font-size: 16px;
  }
  .contract-date, .contract-option{
    font-size: 10px;
    margin-right: 7px !important;
  }
  .parent-padding{
    padding: 0;
  }
  .ivu-modal-body img{
    width: 100%;
  }
  .ivu-collapse > .ivu-collapse-item.ivu-collapse-item-active > .ivu-collapse-header{
    border-bottom: none;
  }
  .ivu-collapse-content > .ivu-collapse-content-box{
    padding-bottom: 0;
  }
  .ivu-collapse > .ivu-collapse-item{
    border-top: none;
  }
  .ivu-collapse-item{
    background: #f7f7f7;
    border-radius: 8px;
  }
  .ivu-collapse{
    display: flex;
    flex-direction: column;
    gap: 10px;
    background: none;
  }
  .ivu-collapse > .ivu-collapse-item > .ivu-collapse-header{
    font-size: 11px;
    padding: 0 10px !important;
    border-bottom: none;
  }
  .ivu-collapse-content-box>div{
    padding: 5px 0 !important;
  }
  .reply-box{
    border-radius: 8px;
  }
  .ivu-menu-dark{
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 5px;
  }
  .ivu-menu-dark>i{
    order: 3;
  }
  .ivu-menu-dark>a{
    order: 1;
  }
  .ivu-menu-dark>li{
    order: 2;
    margin-right: auto;
  }
  .ivu-menu-horizontal .ivu-menu-item{
    padding: 0;
  }
}
</style>
