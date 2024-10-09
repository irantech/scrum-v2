<template>
    <div class="col-lg-9">
        <div class="new-contract card">
            <h1 class="card-header h4">فرم افزودن قرارداد جدید</h1>
            <div class="card-body">
                <div class="alert alert-info" role="alert">
                    <h4 class="alert-heading">چند نکته </h4>
                    <ul>
                        <li>قبل از درج قرارداد باید مشخصات مشتری را در لیست مشتری ها درج کنید.</li>
                        <li>همه موارد الزامی هستند</li>
                    </ul>
                </div>
                <form @submit.prevent="newContract">

                    <div class="form-group form-row border-bottom py-2">
                        <label for="title" class="col-sm-4">عنوان قرارداد</label>
                        <div class="col-sm-8">
                            <input type="text" placeholder="عنوان قرارداد" class="form-control" v-model="form.title"
                                   id="title"
                                   name="title">
                        </div>
                    </div>

                    <div class="form-group form-row border-bottom py-2">
                        <label class="col-sm-4" for="customer">مشتری </label>
                        <div class="col-sm-8">
                            <Select2 v-model="form.customer" name="customer" id="customer" :options="customers"/>
                        </div>
                    </div>
                    <div class="form-row border-bottom py-2">
                        <div class="form-group col-sm-5 d-flex no-gutters">
                            <div class="col-sm-4 font-weight-bold p-0">نوع قرارداد</div>
                            <div class="col-sm-8">
                                <select name="type" id="type" v-model="form.type" class="form-control">
                                    <option v-for="contractType in contractTypes" :value="contractType.id"
                                            v-text="contractType.title"></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-sm-7 d-flex no-gutters">
                            <label for="code" class="font-weight-bold col-sm-3">کد قرارداد</label>
                            <div class="col-sm-9">
                                <input type="text"
                                       class="form-control form-control-sm form-control-plaintext text-danger"
                                       placeholder="کد قرارداد"
                                       v-model="form.code"
                                       name="code"
                                       id="code">
                            </div>
                        </div>
                    </div>

                    <div class="form-row border-bottom py-2">
                        <div class="form-group col-sm-6 d-flex no-gutters">
                            <label class="col-sm-4 font-weight-bold p-0" for="start_date">تاریخ شروع قرارداد</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" v-model="form.start_date"
                                       placeholder="تاریخ پایان قرارداد" id="start_date">
                                <date-picker input-class="form-control" v-model="form.start_date" element="start_date"
                                             format="jYYYY/jMM/jDD" :auto-submit="true"></date-picker>
                            </div>
                        </div>

                        <div class="form-group col-sm-6 d-flex no-gutters">
                            <label class="col-sm-4 font-weight-bold p-0" for="end_date">تاریخ پایان قرارداد</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" v-model="form.end_date"
                                       placeholder="تاریخ پایان قرارداد" id="end_date">
                                <date-picker input-class="form-control" :min="form.start_date" v-model="form.end_date"
                                             element="end_date" format="jYYYY/jMM/jDD"
                                             :auto-submit="true"></date-picker>
                            </div>
                        </div>
                    </div>
                    <div class="form-row border-bottom py-2">
                        <div class="form-group col-sm-7 d-flex no-gutters">
                            <label class="col-sm-4 font-weight-bold text-nowrap" for="sign_date">تاریخ عقد
                                قرارداد</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" v-model="form.sign_date"
                                       placeholder="تاریخ پایان قرارداد" id="sign_date">
                                <date-picker input-class="form-control" :max="form.start_date" v-model="form.sign_date"
                                             element="sign_date" format="jYYYY/jMM/jDD"
                                             :auto-submit="true"></date-picker>
                            </div>
                        </div>
                    </div>
                    <div class="form-row border-bottom py-2">
                        <div class="form-group col-12">
                            <label for="description" class="font-weight-bold">توضیحات قرارداد</label>
                            <textarea name="description" class="form-control"
                                      placeholder="متن کلی قرارداد در اینجا درج میگردد" id="description" rows="10"
                                      v-model="form.description"></textarea>
                        </div>
                    </div>
                    <div class="form-row border-bottom py-2">
                        <div class="form-group col-sm-12 d-flex no-gutters">
                            <div class="col-sm-3 font-weight-bold p-0">مراحل کلی قرارداد</div>
                            <div class="col-sm-9">
                                <div class="d-flex flex-wrap">
                                    <div v-for="baseProgress in allBaseProgress" :key="baseProgress.id"
                                         class="col-sm-6">
                                        <div class="form-check">

                                            <input v-model="form.progress" type="checkbox" class="form-check-input"
                                                   :id="`base_progress-${baseProgress.id}`"
                                                   :value="baseProgress.id">
                                            <label :title="baseProgress.description" class="form-check-label"
                                                   :for="`base_progress-${baseProgress.id}`">{{baseProgress.title}}</label>
                                        </div>
                                    </div>
                                </div>

                            </div>
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
    import Select2 from 'v-select2-component';
    export default{
        components: {Select2},
        data(){
            return {
                allBaseProgress: [],
                contractTypes: [],
                customers: [
                    {id: 1, email: ''}
                ],
                form: {
                    customer: '',
                    title: '',
                    sign_date: '',
                    start_date: '',
                    end_date: '',
                    description: '',
                    code: '',
                    type: [],
                    progress: []

                },
                customer: null,
            }
        },
        mounted(){
            this.generateCode();
            this.getCustomers();
            this.getContractTypes();
            this.getAllBaseProgress();
        },
        methods: {
            newContract() {
                axios.post('contract', this.form)
                    .then(response => {
                        this.showAlert(response.data.message, 'success', 'Complete');
                        this.$router.push('/admin/contract');
                    })
                    .catch(error => {
                        console.log(error);
                    })
            },
            getCustomers(){
                axios.get('customers')
                    .then(response => {
                        this.customers = [];
                        response.data.forEach((item) => {
                            this.customers.push({id: item.id, text: item.name + ' ' + item.email});
                        });

//                        console.log(response.data,this.customers);
//                        this.customers = response.data
                    })
                    .catch(error=>console.log(error))
            },
            getContractTypes(){
                axios.get('contract-type')
                    .then(response => this.contractTypes = response.data)
                    .catch(error=>console.log(error))
            },
            getAllBaseProgress(){
                axios.get('base-progress')
                    .then(response => this.allBaseProgress = response.data)
                    .catch(error=>console.log(error))
            },

            generateCode(){
                axios.get('generate-contract-code')
                    .then(response=> {
                        this.form.code = response.data.code
                    })
                    .catch(error => {
                        console.log(error);
                    })
            }
        }
    }
</script>
