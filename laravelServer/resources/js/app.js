require('./bootstrap');
import Header from './components/includes/VueHeader.vue';
import Footer from './components/includes/VueFooter.vue';
import Sidebar from './components/includes/VueSidebar.vue';
import Customers from './components/admin/customer/Customers.vue';
import Login from './components/user/Login.vue';
import Logout from './components/user/Logout.vue';
import Contracts from './components/Contracts.vue';
import AdminContracts from './components/admin/contract/Contracts.vue';
import PersonalAccessTokens from './components/passport/PersonalAccessTokens.vue';
import AuthorizedClients from './components/passport/AuthorizedClients.vue';
import Clients from './components/passport/Clients.vue';
import AdminSidebar from './components/includes/AdminSidebar.vue';
import router from './routes';
import NProgress from 'nprogress';
import store from './stores/store'
// router.beforeResolve((to, from, next) => {
//     if (to.name) {
//         NProgress.start()
//     }
//     next()
// });
//
// router.afterEach((to, from) => {
//     NProgress.done()
// });

axios.interceptors.request.use(config => {
    NProgress.start();
    return config
});

// before a response is returned stop nprogress
axios.interceptors.response.use(response => {
    setTimeout(
        function () {
            NProgress.done();
        }, 1000
    );
    return response
}, error => {
    NProgress.done();
    return Promise.reject(error);
});

import VuePersianDatetimePicker from 'vue-persian-datetime-picker';

Vue.component('DatePicker', VuePersianDatetimePicker);
Vue.component('VueHeader', Header);
Vue.component('VueFooter', Footer);
Vue.component('Sidebar', Sidebar);
Vue.component('AdminSidebar', AdminSidebar);
Vue.component('AdminContracts', AdminContracts);
Vue.component('Customers', Customers);
Vue.component('Contracts', Contracts);
Vue.component('Login', Login);
Vue.component('Logout', Logout);

Vue.component('PersonalAccessTokens', PersonalAccessTokens);
Vue.component('AuthorizedClients', AuthorizedClients);
Vue.component('Clients', Clients);

Vue.mixin({
    methods: {
        baseProgressAccess(sectionIds, roles) {
            let userRoles = [
                {section: 'office', ids: [1],},
                {section: 'admin', ids: [1, 2, 3, 4, 5]},
                {section: 'support', ids: [3]},
                {section: 'accountant', ids: [4]},
                {section: 'staff', ids: [5]},
            ];
            let returnIds = false;
            userRoles.forEach((element)=>{
                if(roles.includes(element.section)){
                    console.log('included ' + element.section);
                    returnIds = sectionIds.some(sectionId => (element.ids).indexOf(sectionId)>=0);
                }else{
                    returnIds = false;
                }
            });
            return returnIds;
        },
        getRole(role, roles) {
            return roles.filter(item => {
                return item.section == role;
            })
        },
        showLoading() {

        },
        showAlert(message, type = 'success', title = '',toast = true) {
            let options = {toast : toast,
                timer: 3000,
                timerProgressBar : true,
                showConfirmButton: false,
                position: 'bottom-end',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', this.$Swal.stopTimer)
                    toast.addEventListener('mouseleave', this.$Swal.resumeTimer)
            }};

            this.$alert(message, title, type,options);
        },

        getCurrentRole() {
            return localStorage.getItem('role');
        },

        generatedToken() {
            return localStorage.getItem('token');
        },

        checkRole(token) {
            axios.get('current-role', {headers: {Authorization: `Bearer ${token}`}})
                .then(response => {
                    localStorage.setItem('role', response.data);
                })
                .catch(error => {
                    console.log(error);
                    localStorage.removeItem('role');
                })
        },

        Authorized() {
            if (this.generatedToken() != null) {
                axios.get('user', {headers: {Authorization: `Bearer ${this.generatedToken()}`}})
                    .then(() => {
                        return true;
                    })
                    .catch(() => {
                        return false;
                    })
            } else {
                if (this.$route.name != 'Login') this.$router.push({name: 'Login'}).catch(() => {

                });
            }
        },
    }
})
;

const app = new Vue({
    el: '#app',
    router,
    store
});
