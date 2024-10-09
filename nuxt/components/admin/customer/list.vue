<template>
  <div>
    <adminIncludeCrumb name="لیست مشتریان سایت" />
    <div class="col-12 col-md-10 m-auto">
      <Card :bordered="true">
        <Form>
          <FormItem>
            <Input search placeholder="جستجو مشتریان" v-model="searchCustomer" />
          </FormItem>
        </Form>
        <List border :loading="customerLoading">
          <ListItem v-for="(customer , index) in filtered_customer_list"  :class="{'list-color' : index%2 == 0}"
                    :key="index">
            <div class="w-100">
              {{ customer.name }}
            </div>
           <div>
             <nuxt-link :to="`/admin/customer/${customer.id}/contracts`" class="bg-subColor rounded">
               قراردادها
             </nuxt-link>
           </div>
          </ListItem>
        </List>
      </Card>
    </div>
  </div>
</template>

<script>

  export default  {
    name : 'customer-list' ,

    data() {
      return {
        searchCustomer : '',
        customerLoading :  false ,
        customer_list : []
      }
    },
    created() {
      this.customerLoading = true
      this.$axios.get('customers')
        .then(response=> {
          this.customer_list = response.data.data
          this.customerLoading = false
        }).catch(error => console.log(error))
    },
    computed :{
       filtered_customer_list() {
           return this.customer_list.filter( customer => {
             return customer.name.toLowerCase().includes(this.searchCustomer.toLowerCase())
           })
       },
    }
  }
</script>

<style>
  .list-color{
      background : #f8f8f9;
  }
</style>
