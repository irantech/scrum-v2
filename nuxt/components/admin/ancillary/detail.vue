<template>
  <div>
    <adminIncludeCrumb :name="title"/>
    <div class="row">
      <div class="col-12 col-md-8 mb-2">
        <Card :bordered="true">
          <p slot="title">
            جزییات
          </p>
          <p slot="extra" v-if="$store.getters['auth/can']('delete-ancillary')">
           <Button type="error" @click="deleteModel = true" >حذف این قرارداد</Button>
          </p>
          <div class="row my-3">
            <div class="col-sm-3 font-weight-bold">عنوان قرارداد</div>
            <div class="col-sm-9" v-if="showAncillary">
              {{ ancillary.title }}
              <a class="text-info" @click="openAncillaryTitleEdit()" v-if="$store.getters['auth/can']('update-title-ancillary')">
                <i class="fa fa-edit" />
              </a>
            </div>
            <div class="col-sm-9" v-else>
              <Form class="d-flex align-items-center" v-if="$store.getters['auth/can']('update-title-ancillary')"
                    @submit.native.prevent="editAncillaryTitle">
                  <Input type="text" v-model="ancillaryTitle" class="col-8" />
                  <a class="text-danger pl-2" @click="closeAncillaryTitleEdit()"><i class="fas fa-window-close"></i></a>
                  <a class="text-info" @click="editAncillaryTitle()" :loading="anclillaryTitleEditLoading"><i class="fa fa-check-square"/></a>
              </Form>
            </div>
          </div>
          <div class="row my-3">
            <div class="col-sm-3 font-weight-bold"> مشتری </div>
            <div class="col-sm-9" v-if="contract.customer">{{ contract.customer.name }}</div>
          </div>
          <div class="row my-3">
            <div class="col-sm-3 font-weight-bold"> شماره قرارداد</div>
            <div class="col-sm-9">{{ ancillary.contract_code }}</div>
          </div>

          <div class="border-bottom w-100 px-n1"></div>
          <div class="my-3">
            <div class="font-weight-bold border-bottom mb-2">توضیحات</div>
            <div style="height: 200px ; overflow-y: scroll" v-html="contract.description"></div>
          </div>


        </Card>
      </div>
      <div class="col-12 col-md-4">
        <adminAncillaryBaseProgressList :ancillary="ancillary"/>
      </div>
    </div>

    <Modal v-model="deleteModel" width="360">
      <p slot="header" style="color:#f60;text-align:center">
        <Icon type="ios-information-circle"></Icon>
        <span>حذف قرارداد انتخابی</span>
      </p>
      <div style="text-align:center">
        <p>آیا از حذف این قرارداد اطمینان دارید؟</p>
      </div>
      <div slot="footer">
        <Button type="error" size="large" long :loading="deleteLoading" @click="removeAncillary">حذف</Button>
      </div>
    </Modal>
  </div>
</template>
<script>
export default  {
  name : 'ancillry-detail',
  props : ['ancillary' , 'contract'] ,
  data() {
    return {
      ancillaryTitle : '' ,
      showAncillary : true,
      anclillaryTitleEditLoading : false ,
      removeAncillaryLoading : false ,
      deleteModel : false ,
      deleteLoading : false
    }
  },
  computed :{
    title () {
      return ` اطلاعات قرارداد ${this.ancillary ? this.ancillary.title : ''}`
    }
  },
  methods : {
    openAncillaryTitleEdit() {
      this.showAncillary = false
      this.ancillaryTitle = this.ancillary.title
    },
    closeAncillaryTitleEdit() {
      this.showAncillary = true
      this.ancillaryTitle = ''
    },
    editAncillaryTitle(){
      this.anclillaryTitleEditLoading = true
      this.$store.dispatch('admin/ancillary/updateAncillaryTitle' , {
        id  : this.ancillary.id ,
        params : {
          title : this.ancillaryTitle,
          contract_id : this.ancillary.contract_id,
          contract_code : this.ancillary.contract_code,
        }
      })
        .then(response => {
          this.ancillaryTitle = '';
          this.closeAncillaryTitleEdit();
          this.anclillaryTitleEditLoading = false ;
          this.$Message.success(response.data.message);
        })
        .catch(error => {
          this.closeAncillaryTitleEdit() ;
          this.anclillaryTitleEditLoading = false
          this.$Message.error(error.response.data.message);
        })
    } ,
    removeAncillary(){
      this.deleteLoading = true
      this.$store.dispatch('admin/ancillary/removeAncillary',  { id : this.ancillary.id})
        .then(response => {
          this.$Message.success(response.data.message);
          this.$router.push({path : `/admin/contract/${this.contract.id}/view`})
          this.deleteLoading = false
        })
        .catch(error => {
          this.$Message.error(error.data.message);
        })
    }
  }
}
</script>
