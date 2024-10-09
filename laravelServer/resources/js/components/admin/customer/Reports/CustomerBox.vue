<template>
    <div class="row">
        <div v-for="contract in customer.contracts" :key="contract.id" class="card w-100">
            <div class="card-header" v-text="`${customer.name} ${contract.contract_code}`"></div>
            <div class="card-body px-0 d-flex flex-wrap">
                <contract-chart v-for="anc in contract.ancillary" :key="anc.id" :ancillary="anc"></contract-chart>
            </div>
        </div>
    </div>
</template>

<script>
    import ContractChart from './ContractChart.vue';

    export default {
        name: 'CustomerBox',
        components: {
            ContractChart
        },
        props: ['customer'],
        data() {
            return {
            }
        },
        methods: {},
        mounted() {

        }

    }
</script>

<style>

</style>
