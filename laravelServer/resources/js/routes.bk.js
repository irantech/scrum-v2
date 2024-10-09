import VueRouter from 'vue-router';


let routes = [
    {
        path: '/',
        name: 'Home',
        component: require('./components/Home.vue').default,
    },

    {
        path: '/admin',
        name: 'Admin',
        component: require('./components/Home.vue').default,
        children: [
            {
                path: '/contract-types',
                name: 'ContractTypes',
                component: require('./components/admin/ContractTypes.vue').default
            },
            {
                path: '/base-progress',
                name: 'BaseProgress',
                component: require('./components/admin/BaseProgress.vue').default,
            },
            {
                path: '/contracts',
                name: 'Contracts',
                component: require('./components/Contracts.vue').default,
                children: [
                    {
                        path: '/new',
                        name: 'NewContract',
                        component: require('./components/admin/NewContract.vue').default
                    },
                    {
                        path: ':id/view',
                        name: 'SingleContract',
                        component: require('./components/singleContract.vue').default
                    },
                    {
                        path: ':id/edit',
                        name: 'EditSingleContract',
                        component: require('./components/singleContract.vue').default
                    }
                ],
            },

        ]
    },

    {
        path: '/login',
        name: 'Login',
        component: require('./components/user/Login.vue').default
    },

    {
        path: '/contracts/:id/view',
        name: 'singleContract',
        component: require('./components/singleContract.vue').default
    },
    {
        path: '/profile',
        name: 'Profile',
        component: require('./components/user/Profile.vue').default
    },


];
export default new VueRouter({mode: 'history', routes});
