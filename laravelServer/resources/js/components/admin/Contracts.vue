<template>
    <div id="contracts" class="contracts col-lg-9">

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">لیست قراردادها </h5>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="جستجوی قرارداد بر اساس شماره" v-model="searchContract">
                </div>
                <div class="card-text list-group list-group-flush">
                    <admin-contract-box v-for="(contract,index) in filteredList" :key="index"
                                        :contract="contract"></admin-contract-box>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import AdminContractBox from '../admin/ContractBox.vue';
    export default{
        name: "AdminContracts",
        components: {'AdminContractBox': AdminContractBox},
        data(){
            return {
                searchContract: '',
                contracts: [],
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
                this.Authorized();
                this.getComponentData();
            },
            /**
             * Get all of the personal access tokens for the user.
             */

            getComponentData(){
                axios
                    .get('contract')
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
                    return item.contract_code.toLowerCase().includes(this.searchContract.toLocaleLowerCase())});
            }
        }

    }
</script>

<style scopped>



</style>
