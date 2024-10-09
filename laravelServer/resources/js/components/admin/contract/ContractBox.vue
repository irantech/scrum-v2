<template>
    <div class="list-group-item list-group-item-action p-0 mb-3">
        <router-link tag="div" class="card-header" :to="{name: 'AdminSingleContractView',params : {id:contract.id}}">
            {{contract.title}}
            <span class="float-left badge badge-light" v-text="contract.sign_date"></span>
            <br>
            <span class="float-left badge badge-light" v-text="contract.contract_code"></span>
            <span class="d-block small text-secondary">[{{contract.customer.name}}]</span>

        </router-link>
    </div>
</template>

<script>
    import ProgressBar from '../../includes/progress.vue';

    export default {
        name: "AdminContractBox",
        props: ['contract'],
        components: {'ProgressBar': ProgressBar},
        methods: {},

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
