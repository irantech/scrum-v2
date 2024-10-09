import VueRouter from 'vue-router';


let routes = [
    {
        path: '',
        name: 'Home',
        component: require('./components/Home.vue').default,
        children: [
            {
                path: 'login',
                name: 'Login',
                component: require('./components/user/Login.vue').default
            },
            {
                path: 'logout',
                name: 'Logout',
                component: require('./components/user/Logout.vue').default
            },
            {
                path: '/contracts',
                component: {
                    template: '<router-view></router-view>'
                },
                children: [
                    {
                        path: '',
                        name: 'Contracts',
                        component: require('./components/Contracts.vue').default,
                    },
                    {
                        path: 'view/:id',
                        name: 'singleContract',
                        component: require('./components/singleContract.vue').default
                    }
                ],
            },
            {
                path: 'profile',
                name: 'Profile',
                component: require('./components/user/Profile.vue').default
            },
        ]
    },


    //admin

    {
        path: '/admin',
        component: {
            template: '<div><vue-header></vue-header>' +
            '<main class="container py-5" role="main">' +
            '<div class="row justify-content-center">' +
            '<sidebar></sidebar>' +
            '<router-view></router-view>' +
            '</div>' +
            '</main>' +
            '<vue-footer></vue-footer></div>',
        },
        children: [
            {
                path: '',
                name: 'AdminHome',
                component: require('./components/admin/Home.vue').default,
            },
            {
                path: 'base-progress',
                component: {
                    template: '<router-view></router-view>',
                },
                children: [
                    {
                        path: '',
                        name: 'AdminBaseProgress',
                        component: require('./components/admin/BaseProgress.vue').default,
                    },
                    {
                        path: ':id',
                        name: 'AdminSingleBaseProgress',
                        params: ['id'],
                        component: require('./components/admin/SingleBaseProgress.vue').default,
                    },
                ],
            },
            {
                path: 'contract-types',
                name: 'ContractTypes',
                component: require('./components/admin/ContractTypes.vue').default
            },
            {
                path: 'contract',
                component: {
                    template: '<router-view></router-view>'
                },
                children: [
                    {
                        path: '',
                        name: 'AdminContracts',
                        component: require('./components/admin/contract/Contracts.vue').default,
                    },
                    {
                        path: 'new',
                        name: 'AdminNewContract',
                        component: require('./components/admin/contract/NewContract.vue').default
                    },
                    {
                        path: ':id',
                        name: 'AdminSingleContract',
                        component: {
                            template: '<router-view></router-view>',
                        },
                        children: [
                            {
                                path: 'view',
                                name: 'AdminSingleContractView',
                                component: require('./components/admin/contract/SingleContract.vue').default
                            },
                            {
                                path: 'edit',
                                name: 'SingleContractEdit',
                                component: require('./components/admin/contract/SingleContractEdit.vue').default
                            },
                        ]
                    },


                ]
            }, {
                path: 'ancillary',
                component: {
                    template: '<router-view></router-view>'
                },
                children: [
                    {
                        path: '',
                        name: 'AdminAncillary',
                        component: {
                            template: '<router-view></router-view>',
                        },
                    },
                    {
                        path: ':id/view',
                        name: 'AdminSingleAncillaryView',
                        component: require('./components/admin/ancillary/SingleAncillary.vue').default
                    },
                    {
                        path: ':id/edit',
                        name: 'SingleAncillaryEdit',
                        component: require('./components/admin/ancillary/SingleAncillaryEdit.vue').default

                    },

                ]
            },


            {
                path: 'customer',
                component: {
                    template: '<router-view></router-view>'
                },
                children: [
                    {
                        path: '',
                        name: 'AdminCustomers',
                        component: require('./components/admin/customer/Customers.vue').default,
                    },
                    {
                        path: 'new',
                        name: 'AdminNewCustomer',
                        component: require('./components/admin/customer/NewCustomer.vue').default,
                    },
                    {
                        path: ':id',
                        component: {
                            template: '<router-view></router-view>',
                        },
                        children: [
                            {
                                path: '',
                                name: 'AdminCustomerDetails',
                                component: require('./components/admin/customer/CustomerDetails.vue').default,
                            },
                            {
                                path: 'contracts',
                                name: 'AdminCustomerContracts',
                                component: require('./components/admin/customer/CustomerContracts.vue').default,
                            },
                        ],
                    },
                ],

            },
            {
                path: 'user',
                component: {
                    template: '<router-view></router-view>',
                },
                children: [
                    {
                        path: '',
                        name: 'AdminUsers',
                    }
                ],
            },
            {
                path: 'report-customers',
                name: 'ReportCustomers',
                component: require('./components/admin/customer/Reports.vue').default,
            }
        ]
    },


    // not found
    {path: "*", component: require('./components/NotFound.vue').default}

];
export default new VueRouter({
    mode: 'history', routes,
    linkActiveClass: 'active',
    linkExactActiveClass: 'exact-active',

});
