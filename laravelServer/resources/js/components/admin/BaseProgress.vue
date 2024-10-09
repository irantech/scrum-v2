<template>
    <div class="col-lg-9">
        <div class="card">
            <span class="card-header-pills"></span>
            <div class="card-header">مراحل اصلی قرارداد
                <button type="button" class="btn btn-primary float-left" data-toggle="modal"
                        @click.prevent="showNewModal"
                        data-target="#modelNew">افزودن مرحله اصلی قرارداد
                </button>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item" v-for="baseProgress in allBaseProgress">
                    <div class="btn-group float-left">
                        <router-link class="btn btn-sm btn-dark" :to="{name: 'AdminSingleBaseProgress',params : {id: baseProgress.id}}">زیر مجموعه ها
                        </router-link>
                        <button type="button" class="btn btn-primary btn-sm"
                                v-if="baseProgress.trashed == false"
                                data-toggle="modal"
                                @click.prevent="showEditModal(baseProgress)"
                                data-target="#modalEdit">ویرایش
                        </button>

                        <button v-if="baseProgress.trashed == false" class="btn btn-sm btn-danger"
                                @click.prevent="removeBaseProgress(baseProgress.id)">&times;
                        </button>
                        <button v-else="" class="btn btn-sm btn-success" @click.prevent="restoreBaseProgress(baseProgress.id)"> بازگردانی &radic;
                        </button>
                    </div>
                    <span class="font-weight-bold" v-text="baseProgress.title"></span>
                    <p class="small"><span>{{baseProgress.description}}</span> - <span style="direction:ltr;">({{baseProgress.percentage}}%)</span> - [<span v-if="baseProgress.software.title" v-text="baseProgress.software.title"></span>]
                    </p>

                </li>
            </ul>
        </div>
        <!-- Button trigger modal -->


        <!-- Modal -->
        <div class="modal fade" id="modalNew" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
             aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">مرحله اصلی جدید</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="doSubmit">
                            <div class="form-row">
                                <div class="form-group col-sm-3 col-12">
                                    <label class="col-sm-12" for="section">بخش مربوطه</label>
                                    <select name="section" id="section" class="form-control"
                                            v-model="form.section_id">
                                        <option v-for="section in allSections" :value="section.id"
                                                v-text="section.title"></option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-3 col-12">
                                    <label class="col-sm-12" for="user_role">دسترسی</label>
                                    <select name="user_role" id="user_role" class="form-control" v-model="form.user_role">
                                        <option v-for="role in userRoles" :value="role.value"
                                                v-text="role.title"></option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-3 col-12">
                                    <label class="col-sm-12" for="software">نرم افزار</label>
                                    <select name="software" id="software" class="form-control"
                                            v-model="form.software_id">
                                        <option v-for="software in allSoftwares" :value="software.id"
                                                v-text="software.title"></option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-3 col-12">
                                    <label class="col-sm-12" for="percentage">درصد کلی مرحله</label>
                                    <input type="number" class="form-control" id="percentage"
                                           placeholder="درصد کلی مرحله"
                                           v-model="form.percentage">
                                </div>
                            </div>
                            <div class="form-group form-row">
                                <label class="col-sm-4" for="title">عنوان</label>
                                <input type="text" class="form-control" name="title" id="title" v-model="form.title"
                                       placeholder="عنوان">
                            </div>
                            <div class="form-group form-row">
                                <label class="col-sm-4" for="description">توضیحات</label>
                                <textarea name="description" v-model="form.description"
                                          class="form-control"
                                          id="description"></textarea>
                            </div>
                            <div class="form-group form-row">
                                <label class="col-sm-4" for="private_description">توضیحات اختصاصی</label>
                                <textarea name="private_description" v-model="form.private_description"
                                          class="form-control" id="private_description"></textarea>
                            </div>
                            <button class="btn btn-primary" type="submit">افزودن</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">ویرایش مرحله اصلی</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="update">
                            <div class="form-row">
                                <div class="form-group col-sm-3 col-12">
                                    <label class="col-sm-12" for="editSection">بخش مربوطه</label>
                                    <select name="section" id="editSection" class="form-control"
                                            v-model="editForm.section_id">
                                        <option v-for="section in allSections" :value="section.id"
                                                v-text="section.title"></option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-3 col-12">
                                    <label class="col-sm-12" for="editUser_role">دسترسی</label>
                                    <select name="section" id="editUser_role" class="form-control"
                                            v-model="editForm.user_role">
                                        <option v-for="role in userRoles" :value="role.value"
                                                v-text="role.title"></option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-3 col-12">
                                    <label class="col-sm-12" for="editSoftware">نرم افزار</label>
                                    <select name="section" id="editSoftware" class="form-control"
                                            v-model="editForm.software_id">
                                        <option v-for="software in allSoftwares" :value="software.id"
                                                v-text="software.title"></option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-3 col-12">
                                    <label class="col-sm-12" for="editPercentage">درصد کلی مرحله</label>
                                    <input type="number" class="form-control" id="editPercentage"
                                           placeholder="درصد کلی مرحله"
                                           v-model="editForm.percentage">
                                </div>
                            </div>
                            <div class="form-group form-row">
                                <label class="col-sm-4" for="editTitle">عنوان</label>
                                <input type="text" class="form-control" name="title" id="editTitle"
                                       v-model="editForm.title"
                                       placeholder="عنوان">
                            </div>
                            <div class="form-group form-row">
                                <label class="col-sm-4" for="editDescription">توضیحات</label>
                                <textarea name="description" v-model="editForm.description"
                                          class="form-control"
                                          id="editDescription"></textarea>
                            </div>
                            <div class="form-group form-row">
                                <label class="col-sm-4" for="editPrivateDescription">توضیحات اختصاصی</label>
                                <textarea name="private_description" v-model="editForm.private_description"
                                          class="form-control" id="editPrivateDescription"></textarea>
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
    import { mapState } from 'vuex'

    export default {
        name: "BaseProgress",
        data() {
            return {
                userRoles: [
                    {value: 'admin', title: 'مدیر کل'},
                    {value: 'office', title: 'اداری'},
                    {value: 'support', title: 'پشتیبانی'},
                    {value: 'accountant', title: 'حسابدار'},
                    {value: 'programmer', title: 'برنامه نویس'},
                    {value: 'graphic', title: 'گرافیست'},
                ],
                form: {
                    section_id: '',
                    user_role: '',
                    software_id: '',
                    title: '',
                    percentage: '',
                    private_description: '',
                    description: ''
                },
                editForm: {
                    id: null,
                    section_id: null,
                    user_role: null,
                    software_id: null,
                    percentage: null,
                    title: '',
                    description: '',
                    private_description: ''
                },
            }
        },
        mounted() {
            this.Authorized();
            this.getAllBaseProgress();
            this.getAllSections();
            this.getAllSoftwares();
        },
        methods: {
            /*this is method to add new base progress */
            doSubmit() {
                this.$store.dispatch('createNewBaseProgress' , { form : this.form })
                    .then(response => {
                            this.showAlert(response.data.message, 'success');
                            this.getAllBaseProgress();
                            $('#modalNew').modal('hide');
                            this.newForm.section_id = '';
                            this.newForm.software_id = '';
                            this.newForm.user_role = '';
                            this.newForm.percentage = '';
                            this.newForm.description = '';
                            this.newForm.private_description = '';
                        }
                    )
                    .catch(error => {
                        console.log(error)
                        this.showAlert(error.response);
                        this.getAllBaseProgress();
                    })
            },
            /*this method is used to parse data to doUpdate for form values, update baseProgress and hide modal after update */
            update() {
                this.doUpdate(this.editForm.id, this.editForm, '#modalEdit');
            },
            /*we get base progress object, form data and modal id, do update base progress, empty form values and hide modal*/
            doUpdate(baseProgress, form, modal) {
                axios.put(`base-progress/${baseProgress}`, form)
                    .then(response => {
                        form.section_id = '';
                        form.software_id = '';
                        form.user_role = '';
                        form.percentage = '';
                        form.description = '';
                        form.private_description = '';
                        $(modal).modal('hide');
                        this.getAllBaseProgress();
                        this.showAlert(response.data.message);
                    })
                    .catch(error => {
                        this.getAllBaseProgress();
                        this.showAlert(error.response.data.message);
                    })
            },
            /*get all base progress by axios. this method is called after update, remove and first load component*/
            async getAllBaseProgress() {
                await this.$store.dispatch('getAllBaseProgress')
                    .catch((error) => {
                        this.showAlert(error.response);
                    })
            },
            /*get all sections to parse into the list of new form modal and edit form modal. this method will be called at first load component */
            async getAllSections() {
                await this.$store.dispatch('getAllSections')
                    .catch((error)=>{
                        this.showAlert(error.response.data)
                    })
            },
            /*get all sections to parse into the list of new form modal and edit form modal. this method will be called at first load component */
            async getAllSoftwares() {
                await this.$store.dispatch('getAllSoftwares')
                    .catch(error => {
                        this.showAlert(error.response.data);
                    })
            },
            /*get base progress object, parse data to edit form and show edit modal*/
            showEditModal(baseProgress) {
                this.editForm.id = baseProgress.id;
                this.editForm.title = baseProgress.title;
                this.editForm.section_id = baseProgress.section_id;
                this.editForm.software_id = baseProgress.software_id;
                this.editForm.user_role = baseProgress.user_role;
                this.editForm.percentage = baseProgress.percentage;
                this.editForm.description = baseProgress.description;
                this.editForm.private_description = baseProgress.private_description;
                $('#modalEdit').modal('show');
            },
            /*show new modal for base controller*/
            showNewModal() {
                $('#modalNew').modal('show');
            },
            /*we get baseProgress Id and remove it (soft delete)*/
            removeBaseProgress(baseProgressId) {
                axios.delete(`base-progress/${baseProgressId}`)
                    .then(response => {
                        this.showAlert(response.data.message, 'warning');
                        this.getAllBaseProgress();
                    })
                    .catch(error => {
                        this.showAlert(error.response.data);
                    })
            },
            /*we get baseProgress Id of removed item and restore it. after that we update list of base progress */
            restoreBaseProgress(baseProgressId) {
                axios.put(`base-progress/${baseProgressId}/restore`)
                    .then(response => {
                        this.showAlert(response.data.message, 'info');
                        this.getAllBaseProgress();
                    })
                    .catch(error => {
                        this.showAlert(error.response.data);
                    })
            }

        } ,
        computed : {
            ...mapState({
                'allBaseProgress' : state => state.baseProgress.allBaseProgress ,
                'allSections'     : state => state.section.allSections ,
                'allSoftwares'    : state => state.software.allSoftwares
            })
        }
    }
</script>
