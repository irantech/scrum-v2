<template>
    <div v-if="this.generatedToken() != null" class="col-lg-3">
        <aside v-if="checkAccess" id="admin-sidebar">
            <div class="card card-default">
                <div class="card-header">
                    لیست دسترسی های ادمین
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <router-link class="list-group-item" :to="{name : 'AdminContracts'}">مدیریت قراردادها</router-link>
                        <!--<router-link class="list-group-item" to="/admin/contract/new">افزودن قرارداد جدید</router-link>-->
                        <!--<router-link class="list-group-item" to="/admin/contract-types">مدیریت نوع قرارداد</router-link>-->
                        <router-link class="list-group-item" v-if="this.getCurrentRole() == 'admin'" :to="{name : 'AdminBaseProgress'}">مدیریت مراحل اصلی</router-link>
                        <router-link class="list-group-item" :to="{name : 'AdminCustomers'}">لیست مشتریان</router-link>
                        <!--<router-link class="list-group-item rounded-bottom" @click.prevent="alertUser('این قسمت در دست طراحی است')" :to="{name : 'AdminUsers'}">لیست کاربران</router-link>-->

                        <router-link class="list-group-item" :to="{name : 'ReportCustomers'}">گزارشات قراردادها</router-link>

                        <a href="#" class="list-group-item rounded-bottom" @click.prevent="alertUser('این بخش از سایت هنوز به طور کامل آماده نشده است. در آینده میتوانید از این بخش استفاده کنید','در دست بازسازی','error');">مدیریت کاربران</a>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</template>

<script>
    export default {
        name: "sidebar",
        data() {
            return {
                hasAccess: false,
            }
        },
        methods: {
            setAccess() {
                let roles = ['admin', 'office', 'support', 'staff', 'accountant', 'sales'],
                    token = localStorage.getItem('token'),
                    role = localStorage.getItem('role');
                if (typeof token !== 'undefined' && typeof role !== 'undefined') {
                    if (roles.includes(role)) {
                        this.hasAccess = true;
                    }
                }
            },
            alertUser(message,title = '',type = ''){
                return this.showAlert(message,type,title);
            },
            checkAccess() {
                return this.hasAccess;
            }
        },
        mounted() {
            this.setAccess();
            this.setAccess();
        }
    }
</script>

<style>
    aside#admin-sidebar {
        position: sticky;
        top: 70px;
    }
</style>
