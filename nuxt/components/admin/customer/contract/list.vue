<template>
  <div class="col-12 col-md-10 m-auto">
    <Card>
      <p slot="title">لیست قراردادها</p>
      <div class="row mb-2">
        <div class="col-12">
          <Form>
            <FormItem>
              <Input search placeholder="جستجو عنوان قرارداد" v-model="contractTitle" />
            </FormItem>
          </Form>
        </div>
      </div>
      <Col span="24" class="px-2 my-1" v-for="(contract , index) in filtered_contract_list" :key="index">
        <adminContractSingle :contract="contract"/>
      </Col>
    </Card>
  </div>
</template>
<script>


export default {
  name : 'contractList' ,
  props : ['contract_list'],
  data() {
    return {
      contractTitle : '',
    }
  },
  computed : {
    filtered_contract_list() {
      return this.contract_list.filter( contract => {
        return contract.title.toLowerCase().includes(this.contractTitle.toLowerCase())
      })
    }
  }
}
</script>
