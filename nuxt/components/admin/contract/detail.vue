<template>
  <div>
    <adminIncludeCrumb :name="contract.title"/>
    <div class="row">
      <div class="col-12 col-md-7 mb-2">

        <Card :bordered="true">
          <p slot="title">{{ contract.contract_code }}</p>
          <p slot="extra" class="font-weight-bold">
            {{ getCustomer }}
          </p>
          <Col span="24" class="px-2 my-1 d-flex justify-content-center" v-if="loading">
            <Spin size="large"> </Spin>
          </Col>
          <div v-else>
            <div class="row my-3">
              <div class="col-6 col-sm-3 font-weight-bold"> تاریخ شروع قرارداد</div>
              <div class="col-6 col-sm-9" v-if="showStartDate">
                {{ contract.start_date }}
                <a class="text-info" @click="openStartDateChange()" v-if="$store.getters['auth/can']('update-date-contract')">
                  <Icon class="fa fa-edit"/>
                </a>
              </div>
              <div class="col-6 col-sm-9" v-else>
                <Form v-if="$store.getters['auth/can']('update-date-contract')">
                  <div class="d-flex justify-center align-items-center">
                    <client-only>
                      <date-picker auto-submit format="jYYYY-jMM-jDD"  v-model="newStartDate"/>
                    </client-only>
                    <a class="text-success mx-1" @click="saveNewStartDate()"><i class="fas fa-check-square"></i></a>
                    <a class="text-danger" @click="closeStartDateChange()"><i class="fas fa-window-close"></i></a>
                  </div>
                </Form>
              </div>
            </div>
            <div class="row my-3">
              <div class="col-6 col-sm-3 font-weight-bold">تاریخ پایان قرارداد</div>
              <div class="col-6 col-sm-9" v-if="showEndDate">
                {{ contract.end_date }}
                <a class="text-info" @click="openEndDateChange()" v-if="$store.getters['auth/can']('update-date-contract')">
                  <i class="fa fa-edit"/>
                </a>
              </div>
              <div class="col-6 col-sm-9" v-else>
                <Form v-if="$store.getters['auth/can']('update-date-contract')">
                  <div class="d-flex align-items-center">
                    <client-only>
                      <date-picker auto-submit format="jYYYY-jMM-jDD" v-model="newEndDate"/>
                    </client-only>
                    <a class="text-success mx-1" @click="saveNewEndDate()"><i class="fas fa-check-square"></i></a>
                    <a class="text-danger" @click="closeEndDateChange()"><i class="fas fa-window-close"></i></a>
                  </div>
                </Form>
              </div>
            </div>
            <div class="row my-3">
              <div class="col-6 col-sm-3 font-weight-bold">تاریخ عقد قرارداد</div>
              <div class="col-6 col-sm-9" v-if="showSignDate">
                {{ contract.sign_date }}
                <a class="text-info" @click="openSignDateChange" v-if="$store.getters['auth/can']('update-date-contract')">
                  <i class="fa fa-edit" />
                </a>
              </div>
              <div class="col-6 col-sm-9" v-else>
                <Form v-if="$store.getters['auth/can']('update-date-contract')">
                  <div class="d-flex align-items-center">
                    <client-only>
                      <date-picker popover="bottom-right"  auto-submit format="jYYYY-jMM-jDD"  v-model="newSignDate"/>
                    </client-only>
                    <a class="text-success mx-1" @click="saveNewSignDate()"><i class="fas fa-check-square"></i></a>
                    <a class="text-danger" @click="closeSignDateChange()"><i class="fas fa-window-close"></i></a>
                  </div>
                </Form>
              </div>
            </div>
            <div class="row my-3">
              <div class="col-6 col-sm-3 font-weight-bold">نام دامنه</div>
              <div class="col-6 col-sm-9" v-if="showDomain">
                {{ contract.domain_link }}
                <a class="text-info" @click="openDomainChange" v-if="$store.getters['auth/can']('update-date-contract')">
                  <i class="fa fa-edit" />
                </a>
              </div>
              <div class="col-6 col-sm-9" v-else>
                <Form v-model="formDomain" v-if="$store.getters['auth/can']('update-date-contract')"
                      @submit.native.prevent="saveNewDomain">
                  <div class="d-flex align-items-center">
                    <FormItem prop="domain"  :rules="{type : 'url', message: 'یک لینک معتبر وارد کنید.', trigger: 'blur'}">
                      <Input v-model="formDomain.domain" placeholder="Enter your e-mail"></Input>
                    </FormItem>
                    <a class="text-success mx-1" @click="saveNewDomain()"><i class="fas fa-check-square"></i></a>
                    <a class="text-danger" @click="closeDomainChange()"><i class="fas fa-window-close"></i></a>
                  </div>
                </Form>
              </div>
            </div>
            <div class="row my-3">
              <div class="col-6 col-sm-3 font-weight-bold">لینک طرح</div>
              <div class="col-6 col-sm-9" v-if="showThemeLink">
                {{ contract.theme_link }}
                <a class="text-info" @click="openThemeLinkChange" v-if="$store.getters['auth/can']('update-theme-link-contract')">
                  <i class="fa fa-edit" />
                </a>
              </div>
              <div class="col-6 col-sm-9" v-else>
                <Form v-model="formThemeLink" v-if="$store.getters['auth/can']('update-theme-link-contract')"
                      @submit.native.prevent="saveNewThemeLink">
                  <div class="d-flex align-items-center">
                    <FormItem prop="domain"  :rules="{type : 'url', message: 'یک لینک معتبر وارد کنید.', trigger: 'blur'}">
                      <Input v-model="formThemeLink.domain" placeholder="Enter theme link"></Input>
                    </FormItem>
                    <a class="text-success mx-1" @click="saveNewThemeLink()"><i class="fas fa-check-square"></i></a>
                    <a class="text-danger" @click="closeThemeLinkChange()"><i class="fas fa-window-close"></i></a>
                  </div>
                </Form>
              </div>
            </div>
            <div class="row my-2">
              <div class="col-6 col-sm-3 font-weight-bold"> توضیحات </div>
              <div class="col-6 col-sm-9">
                <button @click="infoModal = !infoModal" class="btn btn-link" style="font-size: 15px">مشاهده</button>
              </div>
            </div>
          </div>

        </Card>

        <Card class="mt-3" v-if="!loading">
          <p slot="title"> چک لیست های این قرارداد </p>
          <p v-if="$store.getters['auth/can']('assign-checklist-contract')"
             slot="extra" class="font-weight-bold">
            <button
              @click="openChecklistModal()" type="button" class="btn btn-link p-1 font-15">
              <i class="fa fa-edit"></i>
            </button>
          </p>
          <div class="row px-3">
            <div v-if="contract.checklists && contract.checklists.length !== 0"
                 v-for="(checklist , index) in contract.checklists" class="my-2"
                 :key="index">
              <nuxt-link  v-if="$store.getters['auth/can']('show-contract-title_checklist')"
                          :to="`/admin/contract-checklist/${$store.getters['admin/contract/getChecklistContract'](checklist.id)}`"
                          class="btn-link">
                چک لیست
                {{checklist.title}}
              </nuxt-link>
              <span v-else>
                 چک لیست
                {{checklist.title}}
              </span>
              <Divider type="vertical" />
            </div>
          </div>
        </Card>
      </div>
      <div class="col-12 col-md-5" v-if="!loading">
        <adminContractAncillaryList :ancillaryList="contract.ancillary" :contract="contract"></adminContractAncillaryList>
      </div>
    </div>
    <Modal v-model="checklistModal" title="افزودن چک لیست"  footer-hide>
      <Form ref="formValidate" :model="formValidate" :rules="ruleValidate" label-position="top">
        <div class="col-12">
          <FormItem label="چک لیست ها" prop="checklist">
            <CheckboxGroup v-model="formValidate.checklist" class="row">
              <Checkbox v-for="(checklist , index) in checklists" class="col-6" :key="index" :label="checklist.id" v-if="checklist.trashed === false">
                {{checklist.title}} ({{checklist.language.title}})
              </Checkbox>
            </CheckboxGroup>
          </FormItem>
        </div>
        <div class="col-12">
          <FormItem>
            <Button type="primary" :loading="checklistLoading" @click="addChecklist('formValidate')">ویرایش اطلاعات</Button>
          </FormItem>
        </div>
      </Form>
    </Modal>
    <Modal v-model="infoModal" title="توضیحات این قرارداد" width="800"  footer-hide>
       <p class="px-4 border"  v-html="contract.description"></p>
    </Modal>
  </div>
</template>

<script>
    export default {
      name : 'contractDetail',
      props : ['contract' , 'checklists' , 'loading']  ,
      data() {
        return {
          infoModal : false,
          value1: '' ,
          showStartDate : true ,
          showDomain : true,
          showThemeLink : true,
          newStartDate : '' ,
          showEndDate  : true  ,
          newEndDate  : '' ,
          showSignDate : true ,
          newSignDate  : '',
          checklistModal : false,
          checklistLoading : false ,
          formValidate : {
            checklist : []
          },
          ruleValidate : {
            checklist: [
              { required: true, type : 'array' ,message: 'حداقل یک چک لیست انتخاب کنید.', trigger: 'change' }
            ],
          },
          formDomain : {
            domain : ''
          },
          formThemeLink : {
            domain : ''
          },
          ruleDomain : {
            domain: [
              { required: true,type: 'email' ,  message: 'Mailbox cannot be empty', trigger: 'blur' },
              { type: 'email', message: 'Incorrect email format', trigger: 'blur' }
            ],
          },
        }
      } ,
      computed: {
         getCustomer (){
          return this.contract.customer ? this.contract.customer.name : ''
        }
      },
      methods : {
        saveNewStartDate() {
          this.$store.dispatch('admin/contract/updateContractStartdate' ,{
              id : this.contract.id ,
              params : {
                    newDate: this.newStartDate,
                    dateField: 'start'}
          })
            .then(response => {
              this.closeStartDateChange()
              this.$Message.success(response.data.message);
            }).catch(error => {
              this.closeStartDateChange()
              this.$Message.error(error.data.message);
            });
        }  ,
        openStartDateChange() {
          this.showStartDate  = false
          this.newStartDate = this.contract.start_date
        },
        closeStartDateChange () {
          this.showStartDate = true
          this.newStartDate = ''
        },
        saveNewEndDate() {
          this.$store.dispatch('admin/contract/updateContractStartdate' ,{
              id : this.contract.id ,
              params : {
                    newDate: this.newEndDate,
                    dateField: 'End'
                  }
            })
            .then(response => {
              this.closeEndDateChange()
              this.$Message.success(response.data.message);
            }).catch(error => {
              this.closeEndDateChange()
              this.$Message.error(error.data.message);
            });
        },
        openEndDateChange(){
          this.showEndDate = false
          this.newEndDate = this.contract.end_date
        },
        closeEndDateChange() {
          this.showEndDate = true
          this.newEndDate = ''
        },
        saveNewSignDate() {
          this.$store.dispatch('admin/contract/updateContractStartdate' ,{
            id : this.contract.id ,
            params : {
              newDate: this.newSignDate,
              dateField: 'Sign'
            }
          })
            .then(response => {
             this.closeSignDateChange()
              this.$Message.success(response.data.message)
            }).catch(error => {
            this.closeSignDateChange()
            this.$Message.error(error.data.message)
          });
        }  ,
        openSignDateChange() {
           this.showSignDate = false
           this.newSignDate = this.contract.sign_date
        },
        closeSignDateChange(){
            this.showSignDate = true
            this.newSignDate = ''
        } ,
        openChecklistModal(){
          this.checklistModal = true
          this.formValidate.checklist = this.contract.checklists.map(checklist => checklist.id)
        },
        openDomainChange(){
          this.showDomain = false
          this.formDomain.domain = this.contract.domain_link
        },
        openThemeLinkChange(){
          this.showThemeLink = false
          this.formThemeLink.domain = this.contract.theme_link
        },
        addChecklist(name) {
          this.$refs[name].validate((valid) => {
            if (valid) {
              this.checklistLoading = true
              this.$store.dispatch('admin/contract/addChecklistToContract' ,{
                contract_id  : this.contract.id ,
                form : {
                  checklists : this.formValidate.checklist
                }
              })
              .then(response => {
                this.checklistModal = false
                this.checklistLoading = false
                this.$Message.success(response.data.message);
              })
              .catch(error => {
                  this.$Message.error(error.response.data.message);
                this.checklistLoading = false
              })
              .finally(()=> this.checklistLoading  = false)
            } else {
              this.$Message.error('ابتدا خطاهای خود را بررسی کنید.');
            }
          })
        } ,
        closeDomainChange() {
          this.showDomain = true
          this.formDomain.domain = ''
        },
        closeThemeLinkChange() {
          this.showThemeLink = true
          this.formThemeLink.domain = ''
        },
        saveNewDomain() {
          this.$store.dispatch('admin/contract/updateContractDomain' ,{
            id : this.contract.id ,
            params : {
              domain_link: this.formDomain.domain,
            }
          })
            .then(response => {
              this.closeDomainChange()
              this.$Message.success(response.data.message)
            }).catch(error => {
            this.closeDomainChange()
            this.$Message.error(error.data.message)
          });
        },
        saveNewThemeLink() {
          this.$store.dispatch('admin/contract/updateContractThemeLink' ,{
            id : this.contract.id ,
            params : {
              theme_link: this.formThemeLink.domain,
            }
          })
            .then(response => {
              this.closeThemeLinkChange()
              this.$Message.success(response.data.message)
            }).catch(error => {
            this.closeThemeLinkChange()
            this.$Message.error(error.data.message)
          });
        }
      }

    }
</script>

<style>
 .ancilary-box{
   border: 1px solid #dcdee2;
   padding: 10px;
 }
</style>
