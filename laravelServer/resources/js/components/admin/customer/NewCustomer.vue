<template>
    <div class="col-lg-9">
        <div class="new-contract card">
            <h1 class="card-header h4">فرم افزودن کاربر جدید</h1>
            <div class="card-body">
                <div class="alert alert-info" role="alert">
                    <h4 class="alert-heading">چند نکته </h4>
                    <ul>
                        <li>قبل از درج قرارداد باید مشخصات مشتری را در لیست مشتری ها درج کنید.</li>
                        <li>همه موارد الزامی هستند</li>
                    </ul>
                </div>
                <form @submit.prevent="newCustomer">
                    <div class="form-group form-row border-bottom py-2">
                        <label for="name" class="col-sm-4">نام و نام خانوادگی</label>
                        <div class="col-sm-8">
                            <input type="text" placeholder="نام و نام خانوادگی" class="form-control" v-model="form.name"
                                   id="name" name="name">
                        </div>
                    </div>
                    <div class="form-group form-row border-bottom py-2">
                        <label for="email" class="col-sm-4">ایمیل</label>
                        <div class="col-sm-8">
                            <input type="email" placeholder="ایمیل" class="form-control" v-model="form.email" id="email"
                                   name="email">
                        </div>
                    </div>
                    <div class="form-group form-row border-bottom py-2">
                        <label for="password" class="col-sm-4">رمز ورود</label>
                        <div class="col-sm-8">
                            <input type="password" placeholder="رمز ورود" class="form-control" v-model="form.password"
                                   id="password"
                                   name="password">
                        </div>
                    </div>
                    <div class="form-group form-row border-bottom py-2">
                        <label for="role" class="col-sm-4">نقش کاربری</label>
                        <div class="col-sm-8">
                            <select class="form-control" v-model="form.role" name="role" id="role">
                                <option v-for="role in roles" :value="role">{{role}}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row py-2">
                        <button class="btn btn-success btn-lg" type="submit">ارسال</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    //    import Select2 from 'v-select2-component';
    export default{
//        components: {Select2},
        data(){
            return {
                form: {
                    email: '',
                    name: '',
                    password: '',
                    role: 'customer'
                },
                roles: ['customer', 'staff', 'admin']
            }
        },
        mounted(){

        },
        methods: {
            newCustomer() {
                axios.post('Customer', this.form)
                    .then(response => {
                        this.showAlert(response.data.message);
                        this.$router.push('/admin/Customers');
                    })
                    .catch(error => {
                        console.log(error);
                    })
            }
        }
    }
</script>
