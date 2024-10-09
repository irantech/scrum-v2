<template>
    <div id="customers" class="mb-2 col-lg-9">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">لیست کاربران سایت
                    <!-- <router-link to="/admin/customers/new" class="float-left badge badge-success">افزودن کاربر جدید</router-link>-->
                </h5>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <input type="text" placeholder="جستجوی کاربر" class="form-control" v-model="searchCustomer">
                </div>
                <div v-if="customers" class="card-text list-group list-group-sm">
                    <customer-box v-for="(customer,index) in filteredList" :key="index" :customer="customer"></customer-box>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    import customerBox from './CustomerBox.vue';
    export default{
        name: "customers",
        data(){
            return {
                searchCustomer: '',
                customers: []
            }
        },
        mounted(){
            this.Authorized();
            this.getCustomerData();
        },
        methods: {
            getCustomerData(){
                axios.get('customers')
                    .then(response => this.customers = response.data.data)
                    .catch(error => console.log(error))
                    .finally(() => this.loading = false)
            }
        },
        components: {
            customerBox
        },
        computed: {

            filteredList() {
                let searchCustomer = this.searchCustomer.toLowerCase();
                if (searchCustomer === '') {
                    return this.customers;
                }
                return this.customers.filter(item=> {
                    if (item.email != null) {
                        return item.name.toLowerCase().includes(searchCustomer) || item.email.toLowerCase().includes(searchCustomer);
                    }
                    return item.name.toLowerCase().includes(searchCustomer);
                });
            }
        }
    }
</script>

<style scoped>
    #customers {
        text-align: right;
    }
</style>
