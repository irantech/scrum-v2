<template>
    <div class="col-lg-9">
        <div class="card">
            <span class="card-header-pills"></span>
            <div class="card-header">انواع قراردادها
                <button type="button" class="btn btn-primary float-left" data-toggle="modal" @click="showNewModal"
                        data-target="#modelNewContractType">
                    افزودن نوع قرارداد جدید
                </button>
            </div>
            <ul class="list-group">
                <li class="list-group-item" v-for="contractType in contractTypes">
                    <div class="btn-group float-left">
                        <button type="button" class="btn btn-primary btn-sm"
                                v-show="contractType.trashed == false"
                                data-toggle="modal"
                                @click.prevent="showEditModal(contractType)"
                                data-target="#modelEditContractType">ویرایش
                        </button>
                        <button v-if="contractType.trashed == false" class="btn btn-sm btn-danger"
                                @click.prevent="removeContractType(contractType.id)">&times;</button>
                        <button v-if="contractType.trashed" class="btn btn-sm btn-success"
                                @click.prevent="restoreContractType(contractType.id)"> بازگردانی &radic;</button>
                    </div>
                    <span v-text="contractType.title"></span>
                    <p class="text-muted small" v-text="contractType.description"></p>

                </li>
            </ul>
        </div>
        <!-- Button trigger modal -->


        <!-- Modal -->
        <div class="modal fade" id="modelNewContractType" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">نوع قرارداد جدید</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="doSave">
                            <div class="form-group form-row">
                                <label class="col-sm-4" for="contractTypeTitle">عنوان</label>
                                <input type="text" name="title" v-model="form.title"
                                       class="form-control"
                                       id="contractTypeTitle">
                            </div>
                            <div class="form-group form-row">
                                <label class="col-sm-4" for="contractTypeDescription">توضیحات</label>
                                <textarea type="text" name="description" v-model="form.description"
                                          class="form-control"
                                          id="contractTypeDescription"></textarea>
                            </div>
                            <button class="btn btn-primary" type="submit">افزودن</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modelEditContractType" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">ویرایش نوع قرارداد</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="update">
                            <div class="form-group form-row">
                                <label class="col-sm-4" for="contractTypeEditTitle">عنوان</label>
                                <input type="text" name="title" v-model="editForm.title"
                                       class="form-control"
                                       id="contractTypeEditTitle">
                            </div>
                            <div class="form-group form-row">
                                <label class="col-sm-4" for="contractTypeEditDescription">توضیحات</label>
                                <textarea type="text" name="description" v-model="editForm.description"
                                          class="form-control"
                                          id="contractTypeEditDescription"></textarea>
                            </div>
                            <button class="btn btn-primary" type="submit">بروزرسانی</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default{
        name: "ContractTypes",
        data(){
            return {
                form: {
                    title: '',
                    description: '',
                },
                editForm: {
                    id: null,
                    title: '',
                    description: '',
                },
                contractTypes: [],
            }
        },
        mounted(){
            this.getContractTypes();
        },
        methods: {
            doSave(){
                axios.post('/contract-type', this.form)
                    .then(response => {
                            $('#modelNewContractType').modal('hide');
                            this.getContractTypes();
                            this.showAlert(response.data.message);
                        }
                    )
                    .catch(error => {
                        this.getContractTypes();
                        this.showAlert(error.response.data.message, 'danger');
                    })
            },
            update(){
                this.doUpdate(this.editForm.id, this.editForm, '#modelEditContractType');
            },
            doUpdate(contractType, form, modal){
                axios.put(`contract-type/${contractType}`, form)
                    .then(response => {
                        form.title = '';
                        form.description = '';
                        this.getContractTypes();
                        $(modal).modal('hide');
                        this.showAlert(response.data.message);
                    })
                    .catch(error => {
                        this.getContractTypes();
                        this.showAlert(response.data.message);
                    })
            },
            getContractTypes(){
                axios.get('contract-type/list')
                    .then(response => {
                        this.contractTypes = response.data
                    })
                    .catch(error => {
                        console.log(error);
                        this.showAlert(error.response.data.message, 'danger');
                    })
            },

            showEditModal(contractType){
                this.editForm.id = contractType.id;
                this.editForm.title = contractType.title;
                this.editForm.description = contractType.description;
                $('#modelEditContractType').modal('show');
            },

            showNewModal(){
                $('#modelNewContractType').modal('show');
            },


            removeContractType(contractTypeId){
                axios.delete(`contract-type/${contractTypeId}`)
                    .then(response=> {
                        this.getContractTypes();
                        this.showAlert(response.data.message,'warning');
                    })
                    .catch(error => {
                        this.showAlert(error.response.data.message, 'danger');
                    })
            },
            restoreContractType(contractTypeId){
                axios.put(`contract-type/${contractTypeId}/restore`)
                    .then(response=> {
                        this.getContractTypes();
                        this.showAlert(response.data.message);
                    })
                    .catch(error => {
                        this.showAlert(error.response.data, 'danger');
                    })
            }
        }
    }
</script>

<style>
    .card {
        text-align: right;
    }
</style>
