<template>
  <div class="col-12 col-md-10 m-auto">
    <Card :title="getTitle">
      <Form ref="formValidate" :model="formValidate" :rules="ruleValidate" class="row">
        <div class="col-12 col-md-5">
          <FormItem prop="section" class="mb-2">
            <Select v-model="formValidate.section" placeholder="انتخاب کنید" @on-change="setSelected()">
              <Option v-for="(section , index) in sectionList"  :key="index" :value="section.id">{{ section.title }}</Option>
            </Select>
          </FormItem>
        </div>
        <div class="col-12 col-md-5">
          <FormItem prop="user" class="mb-2">
            <Select v-model="formValidate.user" placeholder="انتخاب کنید">
              <Option v-for="(user , index) in userList"  :key="index" :value="user.id">
                {{ user.name }}
                <span v-if="user.role.section">
                ({{user.role.section.title}})
                </span>
              </Option>
            </Select>
          </FormItem>
        </div>
        <div class="col-2">
          <FormItem class="mb-2">
            <Button type="primary" :loading="assignLoading" :disabled="titleChecklists && titleChecklists.length === 0"
                    @click="handleAssign('formValidate')">تخصیص</Button>
          </FormItem>
        </div>
        <div class="col-12">
          <List :loading="contractChecklistSectionLoading">
            <FormItem prop="selected">
              <CheckboxGroup>
                <Checkbox :value="true"  class="ml-1 mb-0 w-100" v-for="(titleChecklist , index) in titleChecklists"
                          :key="index"
                          disabled
                          :label="titleChecklist.id">
                  {{titleChecklist.title}}
                  [ <i v-for="(section , index) in titleChecklist.section" :key="index" class="extra-list-item">
                    ({{section.title}})
                    </i> ]
                </Checkbox>
              </CheckboxGroup>
            </FormItem>
          </List>
          <span class="text-danger" v-if="titleChecklists && titleChecklists.length === 0">
            حداقل باید یک مورد چک لیست  برای اختصاص دادن وجود داشته باشد.
          </span>
        </div>
      </Form>
    </Card>
    <Card class="mt-2" title="گزارش">
       <List :loading="contractChecklistSectionLoading">

         <Alert type="info" class="text-center" v-if="contractTitleChecklistSection.length === 0">
              هنوز برای هیچ بخشی کارمندی اختصاص نیافته
         </Alert>

         <div v-for="titleChecklist in contractTitleChecklistSection" class="my-1">
           <i class="fa fa-check-circle text-success"></i>
            موارد چک لیست
           {{$store.getters['admin/section/getSectionTitle'](titleChecklist.section.id)}}
           به
           {{$store.getters['admin/user/getUserName'](titleChecklist.user.id)}}
           اختصاص یافته .

         </div>
       </List>
    </Card>
  </div>
</template>

<script>
  export default  {
    name : 'contract-titleChecklist-assign' ,
    props : [ 'checklistContract' ,'contractChecklistSectionLoading',
      'contractTitleChecklistSection' ] ,
    data() {
      return {
        formValidate : {
          user : '' ,
          section : '',
        },
        ruleValidate : {
          user: [
            { required: true, type : 'integer', message: 'فیلد کارمند الزامی است.', trigger: 'change' }
          ],
          section: [
            { required: true,type : 'integer', message: 'فیلد بخش الزامی است.', trigger: 'change' }
          ],
        },
        assignLoading : false,
      }
    },
    methods:{
      handleAssign(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.assignLoading = true
            this.$store.dispatch('admin/checklistContract/assignUserToTitleChecklist', {
              contract_checklist_id: this.checklistContract.id,
              form: {
                user: this.formValidate.user,
                titleChecklist: this.formValidate.selected,
                checklist: this.checklistContract.checklist.id ,
                section  : this.formValidate.section
              }
            }).then(response => {
              this.$Message.success(response.data.message)
              this.assignLoading = false
            }).catch(error => {
              console.log(error)
              if(error.response.status === 403)
                this.$Message.error('شما اجازه تغییر این بخش را ندارید.')
              if(error.response.status === 422)
              {
                if(error.response.data.errors.titleChecklist){
                  let errors = error.response.data.errors.titleChecklist;
                  errors.forEach(error => {
                    this.$Message.error(error)
                  })
                }
              }

              this.assignLoading = false
            })
          } else {
            this.$Message.error('ابتدا خطاهای خود را بررسی کنید.');
          }
        });
      },
      setSelected() {
        let selectedSection = this.contractTitleChecklistSection.find(titleChecklist => titleChecklist.section.id == this.formValidate.section)
        if(selectedSection) {
          this.formValidate.user = selectedSection.user.id
        }
        else
          this.formValidate.user = ''
      } ,
    } ,
    computed : {
      userList(){
        return this.$store.state.admin.user.userList
      },
      titleChecklists() {
        this.formValidate.selected = []
        let checklists = this.$store.getters['admin/checklistContract/getBySection'](this.formValidate.section)
        if(checklists)
        {
          this.formValidate.selected = checklists.map(title => title.id)
        }
        return checklists
      },
      getTitle() {
        return  ` لیست چک لیست ${Object.keys(this.checklistContract).length !== 0 ? '"'+this.checklistContract.checklist.title +'"' : ''} ${Object.keys(this.checklistContract).length !== 0 ? this.checklistContract.contract.title : '' }`
      } ,
      sectionList() {
         return this.$store.getters['admin/section/getSpecialSections'](Object.keys(this.checklistContract).length !== 0 ? this.checklistContract.checklist.sections : '')
      }

    } ,
  }
</script>
