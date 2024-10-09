<template>
    <div class="list-group-item list-group-item-action p-0 mb-3">
        <div class="card-header" @click="adminViewContract(contract.id)">
            {{contract.title}}
            <span class="float-left badge badge-light" v-text="contract.contract_code"></span>
            <span class="d-block small text-secondary">[{{contract.customer.name}}]</span>

        </div>
        <div class="contract-box">
            <div class="progress">
                <progress-bar v-for="(progress, index) in contract.base_progress" :key="index"
                              :title="progress.description" :status="progress.status"
                              :percentage="progress.percentage"></progress-bar>
            </div>
        </div>
        <hr>
    </div>
</template>

<script>
    import ProgressBar from '../includes/progress.vue';

    export default{
        name: "AdminContractBox",
        props: ['contract'],
        components: {'ProgressBar': ProgressBar},
        methods: {
            adminViewContract: function (contractId) {
                this.$router.push({path: `/admin/contract/${contractId}/view`});
            }
        },

    }
</script>

<style scoped>
    .contract-box {
        color: #440034;
    }

    .card-header {
        cursor: pointer;
    }
</style>
