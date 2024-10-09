<template>
    <div class="login col-lg-6">
        <div class="card card-body shadow-lg">
            <form @submit.prevent="do_login">
                <div class="form-group">
                    <input type="text" v-model="email" name="email" value="" class="form-control"
                           placeholder="your email">
                </div>
                <div class="form-group">
                    <input type="password" v-model="password" name="password" value="" class="form-control"
                           placeholder="your password">
                </div>
                <button class="btn btn-block btn-success" type="submit">ورود</button>
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Login",
        data() {
            return {
                email: '',
                password: '',
                firstLoggin: false,
            }
        },
        methods: {
            do_login() {
                axios.post('login', {
                        email: this.email,
                        password: this.password,
                    }
                )
                    .then(response => {
                        localStorage.setItem('token', response.data.success.token);
                        this.checkRole(response.data.success.token);
                        this.firstLoggin = true;
                    })
                    .catch(error => {
                        localStorage.removeItem('token');
                        localStorage.removeItem('role');
                        this.showAlert(error.response.data.error, 'error');
                        console.log(error);
                    });
            },
            checkRole(token) {
                axios.get('current-role', {headers: {Authorization: `Bearer ${token}`}})
                    .then(response => {
                        localStorage.setItem('role', response.data);
                        if (this.firstLoggin) {
                            this.firstLoggin = false;
                            this.do_redirect();
                        }
                    })
                    .catch(error => {
                        console.log(error);
                        localStorage.removeItem('role');
                    });
            },
            do_redirect() {
                location.reload();
            }
        },
        mounted() {

            let roles = ['admin','programmer','graphic','accountant','support','office','customer'],
                token = localStorage.getItem('token'),
                role = this.getCurrentRole();

            if (typeof token !== 'undefined' && typeof role !== 'undefined') {
                if (roles.includes(role)) {
                    this.$router.push({name: 'AdminHome'});
//                    console.log([roles,role]);
//                    this.$router.push({name: 'AdminHome'});
                }
            }
        }
    }
</script>
