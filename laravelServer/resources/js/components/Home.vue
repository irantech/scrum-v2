<template>
    <div>
        <vue-header></vue-header>
        <main class="container py-5" role="main">
            <div class="row justify-content-center">
                <sidebar></sidebar>
                <router-view></router-view>
            </div>
        </main>
        <vue-footer></vue-footer>
    </div>
</template>

<script>
    export default {
        name: "Home",
        data() {
            return {currentRole: this.getCurrentRole()}
        },
        mounted() {
            let roles = ['admin', 'office', 'support', 'staff', 'accountant', 'sales'],
                token = localStorage.getItem('token');

            if (roles.includes(this.currentRole)) {
                this.$router.push({name: 'AdminHome'});
            } else if (this.currentRole === 'customer') {
                this.$router.push({name: 'contracts'});
            }else{
                this.$router.push({name: 'Login'});
            }

        }
    }
</script>

<style>
    .card {
        margin-bottom: 1rem;
    }
</style>
