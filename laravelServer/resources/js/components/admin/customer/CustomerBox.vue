<template>
    <div class="customer-box list-group-item">
        <span class="badge name">{{customer.name}}</span>
        <span class="email">{{customer.email}}</span>
        <!--<router-link :to="`/admin/customers/${customer.id}/details`" class="badge badge-primary">مشخصات</router-link>-->
        <router-link :to="`/admin/customer/${customer.id}/contracts`" class="badge badge-secondary">قراردادها</router-link>
    </div>
</template>

<script>
    export default{
        name: "CustomerBox",
        props: {customer: {}},
    }
</script>

<style scoped>
    .customer-box {
        /*border-bottom: 1px solid crimson;*/
        /*line-height: 2;*/

    }
</style>
