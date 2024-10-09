<template>
    <div class="list-group-item list-group-item-action p-0">
        <div class="card-header" @click="go_to_contract(contract.id)">
            {{contract.title}}
            <span class="float-left badge badge-light" v-text="contract.contract_code"></span>
        </div>
        <div class="contract-box">
            <div class="progress">
                <progress-bar v-for="(progress, index) in contract.base_progress" :key="index"
                              :title="progress.description" :status="progress.status"
                              :percentage="progress.percentage"></progress-bar>
            </div>
        </div>
    </div>
</template>

<script>
    import ProgressBar from './includes/progress.vue';

    export default{
        name: "contractBox",
        props: ['contract'],
        components: {'ProgressBar': ProgressBar},
        methods: {
            go_to_contract: function (contractId) {
                console.log(contractId);
                this.$router.push({name : 'singleContract'});
            }
        },
        mounted(){
            this.Authorized();
        }
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
