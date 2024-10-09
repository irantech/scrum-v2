<template>
</template>

<script>
    export default {
        methods: {
            doLogout() {
                if (this.generatedToken() == null) {
                    this.$router.push({name: 'Login'});
                } else {
                    axios.get('logout', this.Authorization)
                        .then(() => {
                            localStorage.removeItem('token');
                            localStorage.removeItem('role');
                            this.$router.push({name: 'Login'});
                            this.do_redirect();
                        })
                        .catch(()=>{
                            localStorage.removeItem('token');
                            localStorage.removeItem('role');
                            this.$router.push({name: 'Login'});
                            this.do_redirect();
                        });
                }
            },
            do_redirect() {
                location.reload();
            }
        },
        mounted() {
            this.doLogout();
        }
    }
</script>
