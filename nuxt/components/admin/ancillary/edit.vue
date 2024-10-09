<template>
    <div class="row pt-5">
      <div class="col-8 m-auto">
        <Card :bordered="true">
          <p slot="title">
            انتخاب نوع نرم افزار (افزودن مراحل اصلی به قرارداد)
          </p>
          <Form ref="formInline" v-model="formInline" :rules="ruleInline">
            <FormItem>
              <CheckboxGroup v-model="formInline.softwares"  class="row">
                <Checkbox v-for="(software , index) in softwareList"
                          :key="index" :label="software.id"
                           class="col-6 mr-0">{{software.title}}</Checkbox>
              </CheckboxGroup>
            </FormItem>
            <Button type="success" size="large" @click="doSubmit('formInline')" :loading="submitLoading">ذخیره</Button>
          </Form>
        </Card>
      </div>
    </div>
</template>

<script>
  export default {
    name :'single-ancillary-edit' ,
    props : ['softwareList'] ,
    data() {
      return {
        submitLoading : false,
        formInline :{
          softwares :[] ,
        },
        ruleInline :  {
          softwares: [
            { required: true, type: 'array', min: 1, message: 'لطفا قبل از ذخیره حداقل یک نرم افزار انتخاب کنید', trigger: 'change' }
          ],

        }
      }
    } ,
    methods : {
      doSubmit(name){
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.submitLoading = true
            this.$store.dispatch('admin/ancillary/addAncillaryBaseProgress' , {
              id : this.$route.params.id ,
              params : this.formInline
            })
              .then(response => {
                this.$Message.success(response.data.message);
                this.$router.push({path : `/admin/ancillary/${this.$route.params.id}/view`})
                this.submitLoading = false
              })
              .catch(error => {
                this.submitLoading = false
                console.log(error);
              })
          } else {
            this.$Message.error('حداقل یک مورد انتخاب کنید!');
          }
        })

      },
    }

  }
</script>
