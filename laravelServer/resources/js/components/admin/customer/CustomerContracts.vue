<template>
    <div id="contracts" class="contracts col-lg-9">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">لیست قراردادها
                    <!--<router-link to="/admin/contract/new" class="btn badge badge-primary">قرارداد جدید</router-link>-->
                    <!--<span class="btn badge badge-primary">قرارداد جدید</span>-->
                </h5>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <input type="text" placeholder="جستجوی قرارداد" class="form-control" v-model="searchContract">
                </div>
                <div v-if="contracts" class="card-text list-group list-group-flush">
                    <admin-contract-box v-for="(contract,index) in filteredList" :key="index"
                                        :contract="contract"></admin-contract-box>
                </div>
                <div class="alert alert-warning" v-else>
                    هیچ قراردادی برای این مشتری وجود ندارد
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import AdminContractBox from '../contract/ContractBox.vue';
    export default{
        name: "AdminCustomerContracts",
        components: {AdminContractBox},
        data(){
            return {
                searchContract: '',
                contracts: null,
                loading: true,
                tokens: null,
                scopes: null,
                headers: {}
            }
        },
        mounted(){
            this.prepareComponent();
        },

        methods: {
            /**
             * Prepare the component.
             */
            prepareComponent() {
                this.getComponentData();
                this.Authorized();
            },
            /**
             * Get all of the personal access tokens for the Customer.
             */

            getComponentData(){
                axios
//                    .get()
                    .get(`customer/${this.$route.params.id}/contracts`)
                    .then(response => {
                        this.contracts = response.data.data;
                    })
                    .catch(error => console.log(error))
                    .finally(() => this.loading = false)
            },
        },

        computed: {

            filteredList() {
                return this.contracts.filter(item=>{
                    return item.title.toLowerCase().includes(this.searchContract.toLocaleLowerCase())});
            }
        }

    }
</script>

<style scopped>

    .contracts {
        text-align: right;
    }

    .contracts .card {
    }


</style>
