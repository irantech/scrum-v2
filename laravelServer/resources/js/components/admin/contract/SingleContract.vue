<template>
    <div class="col-lg-9">
        <div class="card">
            <h1 class="card-header h4">اطلاعات قرارداد
                <a href="#ancillaries" class="badge badge-secondary badge-pill float-left edit-link" v-smooth-scroll>قراردادها</a>
            </h1>
            <div class="card-body" v-if="contract">
                <div class="form-group form-row border-bottom py-2">
                    <div class="col-sm-4">عنوان قرارداد</div>
                    <div class="col-sm-8">
                        <span v-text="contract.title"></span>
                    </div>
                </div>
                <div class="form-group form-row border-bottom py-2">
                    <label class="col-sm-4">مشتری </label>
                    <span class="form-control-plaintext col-sm-8">
                    <router-link :to="{name : 'AdminCustomerContracts',params : {id: customer.id}}" tag="span"
                                 class="btn btn-link">{{customer.name}} - {{customer.email}}</router-link>
                </span>
                </div>
                <div class="form-row border-bottom py-2">

                    <div class="form-group col-sm-7 d-flex no-gutters">
                        <label class="font-weight-bold col-sm-3">کد قرارداد</label>
                        <div class="col-sm-9">
                            {{contract.contract_code}}
                        </div>
                    </div>
                </div>
                <div class="form-row border-bottom py-2">
                    <div class="form-group col-sm-6 d-flex no-gutters">
                        <label class="col-sm-4 font-weight-bold p-0">تاریخ شروع قرارداد</label>
                        <div class="col-sm-8">
                            {{contract.start_date}}
                            <form class="form-inline edit-date-form" @submit.prevent="editStartDate(contract.id)" v-if="showStartInput">
                                <date-picker v-model="newStartDate" placeholder="تاریخ : سال-ماه-روز"
                                             input-class="w-100 form-control form-control-sm rounded-0" format="jYYYY-jMM-jDD" color="#5c6bc0"
                                             :auto-submit="true">
                                    <span slot="label"></span>
                                </date-picker>
                                <span class="fas fa-save text-success px-2" style="cursor: pointer;"
                                      @click.prevent="editStartDate(contract.id)"></span>
                                <i class="fas fa-times text-danger px-2" style="cursor: pointer;" @click.prevent="showStart"></i>
                            </form>
                            <i v-if="!showStartInput" class="fa fa-edit text-primary px-2" style="cursor: pointer;" @click.prevent="showStart"></i>
                        </div>
                    </div>

                    <div class="form-group col-sm-6 d-flex no-gutters">
                        <label class="col-sm-4 font-weight-bold p-0">تاریخ پایان قرارداد</label>
                        <div class="col-sm-8">
                            {{contract.end_date}}

                            <form class="form-inline edit-date-form" @submit.prevent="editEndDate(contract.id)" v-if="showEndInput">
                                <date-picker v-model="newEndDate" placeholder="تاریخ : سال-ماه-روز"
                                             input-class="w-100 form-control form-control-sm rounded-0" format="jYYYY-jMM-jDD" color="#5c6bc0"
                                             :auto-submit="true">
                                    <span slot="label"></span>
                                </date-picker>
                                <span class="fas fa-save text-success px-2" style="cursor: pointer;" @click.prevent="editEndDate(contract.id)"></span>
                                <i class="fas fa-times text-danger px-2" style="cursor: pointer;" @click.prevent="showEnd"></i>
                            </form>
                            <i v-if="!showEndInput" class="fa fa-edit text-primary px-2" style="cursor: pointer;" @click.prevent="showEnd"></i>
                        </div>
                    </div>
                </div>
                <div class="form-row border-bottom py-2">
                    <div class="form-group col-sm-7 d-flex no-gutters">
                        <label class="col-sm-4 font-weight-bold text-nowrap">تاریخ عقد قرارداد</label>
                        <div class="col-sm-8">
                            {{contract.sign_date}}

                            <form class="form-inline edit-date-form" @submit.prevent="editSignDate(contract.id)" v-if="showSignInput">
                                <date-picker v-model="newSignDate" placeholder="تاریخ : سال-ماه-روز"
                                             input-class="w-100 form-control form-control-sm rounded-0" format="jYYYY-jMM-jDD" color="#5c6bc0"
                                             :auto-submit="true">
                                    <span slot="label"></span>
                                </date-picker>
                                <span class="fas fa-save text-success px-2" style="cursor: pointer;"
                                      @click.prevent="editSignDate(contract.id)"></span>
                                <i class="fas fa-times text-danger px-2" style="cursor: pointer;" @click.prevent="showSign"></i>
                            </form>
                            <i v-if="!showSignInput" class="fa fa-edit text-primary px-2" style="cursor: pointer;" @click.prevent="showSign"></i>
                        </div>
                    </div>
                </div>
                <div v-if="this.getCurrentRole() == 'admin'" class="form-row border-bottom py-2">
                    <div class="form-group col-12">
                        <label class="font-weight-bold">توضیحات قرارداد</label>
                        <div class="border-top contract-description py-3 pr-3" v-html="contract.description">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card" id="ancillaries" v-if="contract.ancillary">
            <h4 class="card-header h4">همه قراردادهای این شماره قرارداد
                <span @click.prevent="showNewAncillary(contract)" class="float-left edit-link text-success" id="newAncillaryTitleId"><i
                    class="fas fa-plus"></i> </span></h4>
            <div class="card-body">
                <div v-if="contract.ancillary.length > 0" class="row row-cols-1 row-cols-sm-2">
                    <div class="col mb-2" v-for="ancillary in contract.ancillary" :key="ancillary.id">
                        <div class="btn-group btn-group-sm w-100">
                            <router-link :to="{name : 'AdminSingleAncillaryView',params: {id: ancillary.id}}"
                                         class="btn btn-outline-dark border-0 border-bottom btn-block">{{ancillary.title}}
                            </router-link>
                            <button class="btn border-0 text-danger" @click.prevent="removeAncillary(ancillary)"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="newAncillary" tabindex="-1" role="dialog" aria-labelledby="newAncillaryTitleId" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <form @submit.prevent="addNewAncillary(contract)">
                        <div class="modal-header">
                            <h5 class="modal-title">افزودن قرارداد جدید</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="text" required="required" aria-required="true" class="form-control" placeholder="عنوان قرارداد"
                                   v-model="ancillaryTitle">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">انصراف</button>
                            <button type="submit" class="btn btn-primary">افزودن</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "AdminSingleContract",
        data() {
            return {
                allStatuses: [
                    {value: 'hold', title: 'در دست بررسی'},
                    {value: 'cancel', title: 'لغو'},
                    {value: 'running', title: 'در حال اجرا'},
                    {value: 'complete', title: 'تکمیل'},
                ],
                contract: {
                    title: '',
                    id: null,
                    type_id: null,
                    user_id: null,
                },
                type: {
                    title: '',
                    description: ''
                },
                customer: {
                    name: '',
                    email: ''
                },
                ancillaryTitle: '',
                ancillaryEdit: {
                    id: null,
                    ancillary_title: '',
                    contract_id: null,
                    contract_code: null
                },
                showStartInput: false,
                showEndInput: false,
                showSignInput: false,
                newStartDate: '',
                newEndDate: '',
                newSignDate: ''
            }
        },
        mounted() {
//            console.log(this.contract);
            this.getContractData();
        },
        methods: {
            showAncillaryEdit(ancillary) {
                this.ancillaryEdit.id = ancillary.id;
                this.ancillaryEdit.ancillary_title = ancillary.title;
                this.ancillaryEdit.contract_id = ancillary.contract_id;
                this.ancillaryEdit.contract_code = ancillary.contract_code;
                $('#ancillaryEdit').modal('show');
            },
            update() {
                this.submitAncillaryChange(this.ancillaryEdit.id, this.ancillaryEdit, '#modalEdit');
            },

            submitAncillaryChange(form, modal) {
                ancillary = form.id;
                axios.put(`ancillaries/${ancillary}`, form)
                    .then(response => {
                        form.ancillary_title = '';
                        form.contract_id = '';
                        form.contract_code = '';
                        $(modal).modal('hide');
                        this.getContractData();
                        this.showAlert(response.data.message);
                    })
                    .catch(error => {
                        this.getContractData();
                        $(modal).modal('hide');
                        this.showAlert(error.response.data.message);
                    })
            },

            showNewAncillary(contract) {
                $('#newAncillary').modal('show');
            },

            addNewAncillary(contract) {
                let params = {'title': this.ancillaryTitle, contract_code: contract.contract_code, contract_id: contract.id};
                axios.post('ancillary', params)
                    .then(response => {
                        this.ancillaryTitle = '';
                        this.getContractData();
                        $("#newAncillary").modal('hide');
                        this.showAlert(response.data.message);
                    })
                    .catch(error => {
                        this.showAlert('خطایی در هنگام ثبت قرارداد رخ داده است', 'error');
                        console.log(error);
                    })
            },

            removeAncillary(ancillary) {
                if (confirm('آیا مطمئن هستید ؟ ')) {
                    axios.delete(`ancillary/${ancillary.id}`)
                        .then(response => {
                            this.getContractData();
                            $('#ancillaryEdit').modal('hide');
                            this.showAlert(response.data.message);
                        })
                        .catch(error => {
                            console.log(error);
                            $('#ancillaryEdit').modal('hide');
                        })
                } else {
                    $('#ancillaryEdit').modal('hide');
                }
            },

            statusTitle(status) {
                return this.allStatuses.forEach(stat => {
                    if (stat.value == status) {
                        console.log(stat.title);
                        return (stat.title);
                    }
                });
//                object.entries(this.allStatus).map(([key,value]) =>[key]);
//                return this.allStatuses.title = status;
            },

            getContractData() {
                axios.get(`contract/${this.$route.params.id}`)
                    .then(response => {
                        this.contract = response.data.data;
                        this.customer = response.data.data.customer;
                        this.type = response.data.data.type;
                        this.sub_progress = response.data.data.sub_progress;
//                        this.contract.id = response.data.data.id;
//                        this.showAlert(response.data.message);
//                        console.log(this.contract.title);

                    })
                    .catch(error => {
                        console.log(error)
                    })
            },

            /*getting contract customer data from API*/
            getCustomerData(customer_id) {
                axios.get(`user/${customer_id}`)
                    .then(response => {
                        this.customer = response.data.data;
                    })
                    .catch(error => {
                        console.log(error.response);
                    })
            },

            /*get baseProgress children Items*/

            getBaseProgressChild(baseId) {
                this.bp_child = [];
                this.sub_progress.forEach(item => {
                    if (item.base_progress_id == baseId) {
                        this.bp_child.push(item)
                    }
                });
                $(`#bp-${baseId}`).collapse('toggle');
            },

            /*get contract type by id */
            getContractType(type_id) {
                axios.get(`contract-type/${type_id}`)
                    .then(response => {
                        this.type = response.data.data;
                    })
                    .catch(error => {
                        console.log(error.response)
                    });
            }
            ,
            /*change the status of child progress */
            changeSubStatus(status, child) {
                axios.patch('change-sub-progress-status', {
                    'status': status, 'sub_progress_id': child.id, 'contract_id': this.$route.params.id
                })
                    .then(response => {
                        this.showAlert(response.data.message);
                        $('.baseProgressCollapse').collapse('hide');
                        this.getContractData();
                    })
                    .catch(error => {
                        this.showAlert(error.response.data.message);
                        $('.baseProgressCollapse').collapse('hide');
                        this.getContractData();
                    })
            },

            changeBaseStatus(baseProgressId, event) {
                status = event.target.value;
                console.log(baseProgressId, status);
                axios.patch('change-base-progress-status', {
                    'status': status, 'base_progress_id': baseProgressId, 'contract_id': this.$route.params.id
                })
                    .then(response => {
                        this.showAlert(response.data.message);
                        $('.baseProgressCollapse').collapse('hide');
                        this.getContractData();
                    })
                    .catch(error => {
                        console.log(error);
                    })

            },

            showStart() {
                this.showStartInput = !this.showStartInput;
            },
            showEnd() {
                this.showEndInput = !this.showEndInput;
            },
            showSign() {
                this.showSignInput = !this.showSignInput;
            },

            editStartDate(contractId) {
                axios.put(`contracts/update-date/${contractId}`, {
                    newDate: this.newStartDate,
                    dateField: 'start'
                }).then(response => {
                    this.getContractData();
                    this.showStart();
                    console.log(contractId);
                }).catch(error => {
                    this.getContractData();
                    this.showStart();
                    console.log(contractId);
                });
            },
            editEndDate(contractId) {
                axios.put(`contracts/update-date/${contractId}`, {
                    newDate: this.newEndDate,
                    dateField: 'End'
                }).then(response => {
                    this.getContractData();
                    this.showEnd();
                    console.log(contractId);
                }).catch(error => {
                    this.getContractData();
                    this.showEnd();
                    console.log(contractId);
                });
            },
            editSignDate(contractId) {
                axios.put(`contracts/update-date/${contractId}`, {
                    newDate: this.newSignDate,
                    dateField: 'Sign'
                }).then(response => {
                    this.getContractData();
                    this.showSign();
                    console.log(contractId);
                }).catch(error => {
                    this.getContractData();
                    this.showSign();
                    console.log(contractId);
                });
            },
            checkApi() {
                axios.get('http://192.168.1.16:8000/api/customer/684865465/contract/64564/stats')
                    .then(response => {
                        console.log(response.data);
                    })
                    .catch(error => {
                        console.log(error);
                    })
            }
        },
        computed: {
            contractDescription() {
                return this.contract.description;
            }
        }
    }
</script>

<style scoped>
    .edit-date-form {
        margin-top: -1.5rem;
        position: absolute;
    }

    .vpd-main {
        width: 70%;
    }

    .edit-link {
        cursor: pointer;
        font-size: small;
        font-weight: normal;
        padding: 0.5em 0.75em;
    }

    .child-label {
        margin: 0.5rem;
        display: flex;
        justify-content: space-between;
        margin-bottom: 1rem;
        flex-direction: column;
        align-items: flex-start;
    }

    .btn-status {
        opacity: 0.5;
        padding: .125rem .25rem;
        font-size: 0.75rem;
        color: #000000;
    }

    .btn-status:hover {
        opacity: 0.7;
    }

    .btn-status.active {
        opacity: 1;
        color: #ffffff;
        font-weight: bolder;
        -webkit-box-shadow: 0 0 12px -4px #000000;
        -moz-box-shadow: 0 0 12px -4px #000000;
        box-shadow: 0 0 12px -4px #000000;
    }

    .btn-status.hold {
        background: #f66d9b;
    }

    .btn-status.complete {
        background: #38c172;
    }

    .btn-status.running {
        background: #4dc0b5;
    }

    .btn-status.cancel {
        background: #e3342f;
    }

    .contract-description {
        overflow-x: auto;
        height: 200px;
    }
</style>
