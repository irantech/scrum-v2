<template>
    <div id="contracts" class="contracts col-lg-9">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">لیست قراردادها </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="نام مشتری"
                                   v-model="searchContract">
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="شماره قرارداد"
                                   v-model="searchCode">
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <select title="سال" class="form-control" name="searchYear" id="searchYear" v-model="searchYear">
                                <option v-for="year in yearsList" :value="year" v-text="year" :selected="year == searchYear"></option>
                            </select>
                        </div>
                    </div>
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
    import AdminContractBox from './ContractBox.vue';

    export default {
        name: "AdminContracts",
        components: {'AdminContractBox': AdminContractBox},
        data() {
            return {
                searchContract: '',
                searchCode: '',
                searchYear: '1400',
                contracts: [],
                yearsList : [
                    '1400',
                    '1399',
                    '1398',
                    '1397',
                    '1396',
                    '1395',
                    '1394',
                    '1393',
                    '1392',
                    '1391',
                ],
                loading: true,
                tokens: null,
                scopes: null,
                headers: {}
            }
        },
        mounted() {
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

            getComponentData() {
                axios
                    .get('contract')
                    .then(response => {
                        this.contracts = response.data.data;
                    })
                    .catch(error => console.log(error))
                    .finally(() => this.loading = false)
            },
            filteredListByCode() {
                return item.contract_code.toLowerCase().includes(this.searchCode.toLocaleLowerCase())
            },
            filteredByTitle() {
                return item.title.toLowerCase().includes(this.searchContract.toLocaleLowerCase())
            },
            filteredByYear() {
                return item.sign_date.toLowerCase().includes(this.searchYear.toLocaleLowerCase())
            }
        },
        computed: {
            filteredList() {
                return this.contracts.filter(item => {
                    if (this.searchContract != '') {
                        return item.customer.name.toLowerCase().includes(this.searchContract.toLocaleLowerCase())
                    } else if (this.searchCode) {
                        return item.contract_code.toLowerCase().includes(this.searchCode.toLocaleLowerCase())
                    } else {
                        return item.sign_date.toLowerCase().includes(this.searchYear.toLocaleLowerCase());
                    }
                });
            }
        }

    }
</script>

<style scopped>


</style>
