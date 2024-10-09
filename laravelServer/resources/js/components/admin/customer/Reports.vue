<template>
    <div class="col-lg-9">
        <customer-box  v-for="customer in customers" :key="customer.id" :customer="customer"></customer-box>
    </div>
</template>

<script>
    import CustomerBox from './Reports/CustomerBox.vue';

    export default {
        components: {
            CustomerBox,
        },
        name: 'Reports',
        data() {
            return {
                customers: []
            }
        },
        methods: {
            getReportsData() {
                axios.get('report-customers').then(response => {
                    this.customers = response.data.data;
                }).catch(error => {
                    console.log(error);
                });
            }
        },
        mounted() {
            this.getReportsData();
        }

    }
</script>

<style>

</style>
