<template>
    <div class="col-lg-9">
        <div class="card">
            <div class="card-header h4">اطلاعات قرارداد
                <router-link class="badge badge-secondary badge-pill float-left edit-link"
                             :to="{name: 'SingleAncillaryEdit',params : {id: ancillary.id}}"><i class="fas fa-pencil-alt"></i> ویرایش </router-link>
                <router-link class="badge badge-danger badge-pill float-left edit-link" :to="{name: 'AdminSingleContractView', params:{id: ancillary.contract_id}}"> بازگشت </router-link>
            </div>
            <div class="card-body" v-if="ancillary">

                <div v-if="ancillary.base_progress && ancillary.base_progress.length  === 0" class="alert alert-success"
                     role="alert">
                    <p>هنوز برای این قرارداد مرحله اصلی ثبت نشده است. لطفا به صفحه ویرایش رفته و مراحل را اضافه
                        نمایید </p>
                    <router-link class="btn btn-sm btn-info"
                                 :to="{name: 'SingleAncillaryEdit',params : {id: ancillary.id}}">رفتن به صفحه ویرایش
                    </router-link>
                </div>

                <div class="form-group form-row border-bottom py-2">
                    <div class="col-sm-4">عنوان قرارداد</div>
                    <div class="col-sm-8">
                        <span v-text="ancillary.title"></span>
                        <!-- Button trigger modal -->
                        <a href="ancillaryEdit" class="badge badge-pill text-warning" @click.prevent="showAncillaryEdit(ancillary)"><i class="far fa-edit"></i></a>
                        <a href="ancillaryDelete" @click.prevent="removeAncillary(ancillary)" class="badge badge-pill text-danger"><i class="fas fa-times"></i></a>
                        <!-- Modal -->
                        <div class="modal fade" id="editForm" tabindex="-1" role="dialog" aria-labelledby="editFormTitleId"
                             aria-hidden="true">
                            <div class="modal-dialog modal-sm" role="document">
                                <form @submit.prevent="update()">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">ویرایش عنوان </h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="text" class="form-control" placeholder="عنوان قرارداد" v-model="editForm.title">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">بستن </button>
                                            <button type="submit" class="btn btn-primary">ذخیره </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group form-row border-bottom py-2">
                    <label class="col-sm-4">مشتری </label>
                    <span class="form-control-plaintext col-sm-8">
                    <router-link :to="{name: 'AdminCustomerContracts',params : {id: customer.id}}"
                                 class="btn btn-link">{{customer.name}} - {{customer.email}}</router-link>
                </span>
                </div>
                <div class="form-row border-bottom py-2">
                    <div class="form-group col-sm-7 d-flex no-gutters">
                        <label class="font-weight-bold col-sm-3">کد قرارداد</label>
                        <div class="col-sm-9">
                            {{ancillary.contract_code}}
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

                <div class="col font-weight-bold p-0">مراحل کلی قرارداد</div>
                <div class="col mt-3 px-0">
                    <div id="base-progress-group" class="row row-cols-1 row-cols-sm-2">
                        <div v-for="base in ancillary.base_progress" :key="base.pivot.base_progress_id"
                             v-if="userCanSee(base.user_role)" class="col">
                            <div class="card">
                                <a :class="['card-link', 'card-header',cardHeaderClass(base.pivot.status) ]"
                                   @click.prevent="getBaseProgressChild(base.id)"
                                   :href="`#bp-${base.id}`">
                                    {{base.description}} - <span v-for="stat in allStatuses"
                                                                 v-if="base.pivot.status == stat.value"
                                                                 v-text="stat.title"></span>
                                </a>
                                <div :id="`bp-${base.id}`" class="baseProgressCollapse collapse"
                                     data-parent="#base-progress-group">
                                    <div class="card-body">
                                        <div class="card-text">
                                            <div class="form-group form-inline">
                                                <select @change="changeBaseStatus(base.id,$event)"
                                                        class="form-control">
                                                    <option v-for="stat in allStatuses"
                                                            :value="stat.value"
                                                            :selected=" base.pivot.status == `${stat.value}`">
                                                        {{stat.title}}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-wrap">
                                            <div class="child-label" v-for="child in bp_child">
                                                <span class="font-weight-bold">{{child.title}}</span>
                                                <div class="btn-group btn-group-sm" v-if="child">
                                                    <button v-for="(status,index) in allStatuses" :key="index"
                                                            @click="changeSubStatus(status.value,child)"
                                                            :class="['btn','btn-sm','btn-status',`${status.value}`, {'active' : child.pivot.status == `${status.value}`}]"
                                                            v-text="status.title"></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "AdminSingleAncillary",
        data() {
            return {
                editForm: {
                    title: '',
                    contract_code: null,
                    contract_id: null,
                    id: null
                },
                allStatuses: [
                    {value: 'hold', title: 'در دست بررسی'},
                    {value: 'cancel', title: 'لغو'},
                    {value: 'running', title: 'در حال اجرا'},
                    {value: 'complete', title: 'تکمیل'},
                ],
                ancillary: {
                    title: null,
                    contract_code: null,
                    contract_id: null,
                    id: null,
                },
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
                bp_child: [],
            }
        },
        mounted() {
            this.getAncillaryData();
        },
        methods: {

            userCanSee(baseRole) {
                let access = ['admin', baseRole];
                return access.includes(this.getCurrentRole());
//                return this.getCurrentRole() == 'admin' || this.getCurrentRole() == baseRole
            },

            showAncillaryEdit(ancillary) {
                this.editForm.id = ancillary.id;
                this.editForm.title = ancillary.title;
                this.editForm.contract_id = ancillary.contract_id;
                this.editForm.contract_code = ancillary.contract_code;
                $('#editForm').modal('show');
            },
            update() {
                this.submitAncillaryChange(this.editForm.id,this.editForm, '#editForm');
            },

            cardHeaderClass(baseStatus) {
                if (baseStatus == 'complete') {
                    return 'text-white bg-success';
                }
                if (baseStatus == 'running') {
                    return 'text-white bg-info';
                }
                if (baseStatus == 'hold') {
                    return 'text-white bg-warning';
                }
                if (baseStatus == 'cancel') {
                    return 'text-white bg-danger';
                }

                return '';
            },

            submitAncillaryChange(id,data, modal) {
                axios.put(`ancillary/update-title/${id}`, data)
                    .then(response => {
                        data.title = '';
                        data.contract_id = '';
                        data.contract_code = '';
                        $(modal).modal('hide');
                        this.getAncillaryData();
                        this.showAlert(response.data.message);
                    })
                    .catch(error => {
                        this.getAncillaryData();
                        $(modal).modal('hide');
                        this.showAlert(error.response.data.message);
                    })
            },

            removeAncillary(ancillary) {
                if (confirm('آیا مطمئن هستید ؟ ')) {
                    axios.delete(`ancillary/${ancillary.id}`)
                        .then(response => {
//                            this.getAncillaryData();
                            $('#editForm').modal('hide');
                            this.showAlert(response.data.message);
                            this.$router.push({name : 'AdminSingleContractView',params : {id : ancillary.contract_id}})
                        })
                        .catch(error => {
                            console.log(error);
                            $('#editForm').modal('hide');
                        })
                } else {
                    $('#editForm').modal('hide');
                }
            },

            statusTitle(status) {
                return this.allStatuses.forEach(stat => {
                    if (stat.value == status) {
//                        console.log(stat.title);
                        return (stat.title);
                    }
                });
//                object.entries(this.allStatus).map(([key,value]) =>[key]);
//                return this.allStatuses.title = status;
            },

            getAncillaryData() {
                axios.get(`ancillary/${this.$route.params.id}`)
                    .then(response => {
                        this.ancillary = response.data.data;
                        this.sub_progress = response.data.data.sub_progress;
                        this.getContractData(response.data.data.contract_id);

                    })
                    .catch(error => {
                        console.log(error)
                    })
            },
            getContractData(contract_id) {
                axios.get(`contract/${contract_id}`)
                    .then(response => {
                        this.contract = response.data.data;
                        this.customer = response.data.data.customer;
                        this.type = response.data.data.type;
//                        this.getCustomerData(response.data.data.customer_id)
                    })
                    .catch(error => {
                        console.log(error);
                    })
            },

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
//                console.log(this.sub_progress);
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
                axios.patch('ancillary-change-sub-progress', {
                    'status': status, 'sub_progress_id': child.id, 'ancillary_id': this.$route.params.id
                })
                    .then(response => {
                        this.showAlert(response.data.message);
                        $('.baseProgressCollapse').collapse('hide');
                        this.getAncillaryData();
                    })
                    .catch(error => {
                        this.showAlert(error.response.data.message);
                        $('.baseProgressCollapse').collapse('hide');
                        this.getAncillaryData();
                    })
            },

            changeBaseStatus(baseProgressId, event) {
                status = event.target.value;
//                console.log(baseProgressId, status);
                axios.patch('ancillary-change-base-progress', {
                    'status': status, 'base_progress_id': baseProgressId, 'ancillary_id': this.$route.params.id
                })
                    .then(response => {
                        this.showAlert(response.data.message);
                        $('.baseProgressCollapse').collapse('hide');
                        this.getAncillaryData();
                    })
                    .catch(error => {
                        console.log(error);
                    })

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
    .edit-link {
        cursor: pointer;
        font-size: xx-small;
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
