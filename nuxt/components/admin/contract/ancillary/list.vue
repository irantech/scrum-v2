<template>
  <div>

    <Card :bordered="true">
      <p slot="title">همه قراردادهای این شماره قرارداد </p>
      <p slot="extra" v-if="$store.getters['auth/can']('create-ancillary')">
        <Button type="success" @click="createModal = true">افزودن</Button>
      </p>
      <Alert show-icon v-if="ancillaryList && ancillaryList.length  == 0">
        <p>هنوز برای این قرارداد مرحله اصلی ثبت نشده است.</p>
        <Button type="success" @click="createModal = true">ایجاد قرار داد جدید</Button>
      </Alert>
      <List border  v-else>
        <ListItem v-for="(ancillary , index) in ancillaryList"
                  :key="index">
          <ListItemMeta  :title="ancillary.title" />
          <template slot="action">
            <li v-if="$store.getters['auth/can']('delete-ancillary')">
              <a class="text-danger"  @click.prevent="deleteAncillary(ancillary)">حذف</a>
            </li>
            <li>
              <nuxt-link :to="`/admin/ancillary/${ancillary.id}/view`" class="text-info"> مشاهده</nuxt-link>
            </li>
          </template>
        </ListItem>

      </List>
    </Card>


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

    <Modal
      v-model="createModal"
      title="افزودن قرارداد جدید">
      <p slot="header" style="color:#f60;text-align:center">
        <Icon type="ios-information-circle"></Icon>
        <span>اضافه کردن قرارداد جدید</span>
      </p>
      <Form ref="createAncillaryForm" :model="formInline" :rules="ruleInline" @submit.native.prevent="addNewAncillary('createAncillaryForm')">
        <FormItem prop="ancillaryTitle">
          <Input type="text" v-model="formInline.ancillaryTitle" placeholder="عنوان قرارداد"  required="required" aria-required="true">
          </Input>
        </FormItem>
      </Form>
      <div slot="footer">
        <Button type="error"  @click="createModal = false">انصراف</Button>
        <Button type="success" @click="addNewAncillary('createAncillaryForm')"
                :loading="createLoading">افزودن</Button>
      </div>
    </Modal>
  </div>
</template>

<script>
  export default {
    name : 'ancillary-list' ,
    props : ['ancillaryList' , 'contract'],
    data(){
      return {
          deleteModel : false,
          deleteLoading : false ,
          createModal : false ,
          createLoading : false,
          choosedAncillary : '',
          formInline: {
            ancillaryTitle: '',
          },
          ruleInline: {
            ancillaryTitle: [
              { required: true, message: 'پر کردن این فیلد الزامی است', trigger: 'blur' }
            ],
          }
      }
    },
    methods : {
      deleteAncillary(ancillary) {
        this.deleteModel = true
        this.choosedAncillary = ancillary
      },
      removeAncillary() {
        this.deleteLoading = true
        this.$store.dispatch('admin/contract/removeAncillaryFromContract' ,
          {ancillary : this.choosedAncillary })
            .then(response => {
              this.$Message.success(response.data.message);
              this.deleteLoading = true
            })
            .catch(error =>{
              this.$Message.success(error);
              this.deleteLoading = true
            })
            .finally(()=> {
              this.deleteLoading = false
              this.deleteModel = false
            })
      },
      addNewAncillary(name) {
        this.createLoading = true
          this.$refs[name].validate((valid) => {
            if (valid) {
              let params = {'title': this.formInline.ancillaryTitle,
                'contract_code': this.contract.contract_code,
                'contract_id': this.contract.id};
              this.$store.dispatch('admin/contract/addAncillaryToContract' , {params : params})
                .then(response => {
                  this.formInline.ancillaryTitle = '';
                  this.$Message.success(response.data.message);
                  this.createLoading = false
                  this.createModal = false
                })
                .catch(error => {
                  this.createLoading = false
                  this.$Message.error('خطایی در هنگام ثبت قرارداد رخ داده است');
                  this.createModal = false
                })
            } else{
              this.createLoading = false
              this.$Message.error('لطفا ابتدا خطاها را بررسی کنید.');
            }
          })
      }
    }
  }
</script>

<style>
 .delete-contract-icon {
   position: absolute;
   left: 10px;
   top: 14px;
 }
 .contract-title:hover {
   background: #17233d;
   color: #fff;
 }
 .contract-title {
   padding: 5px;
   width: 90%;
   border-radius: 3px;
 }

</style>
