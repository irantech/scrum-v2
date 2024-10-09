<template>
    <div class="col-lg-9">
        <div class="card">
            <div class="card-header"> مرحله اصلی : <span
                class="text-secondary font-weight-bold">{{baseProgress.title}}</span>

                <span class="btn btn-sm float-left btn-outline-success btn-show-new" @click="showNewModal">افزودن زیر مجموعه</span>
            </div>
            <div class="card-body">
                <div class="card-text">
                    <div class="list-group">
                        <div class="list-group-item" v-for="progress in baseProgress.allProgress" :key="progress.id"
                             :data-id="progress.id">
                            <span v-text="progress.title"></span>
                            <div class="btn-group float-left">

                                <button v-if="progress.trashed" class="btn btn-success btn-sm"
                                        @click="restoreSubProgress(progress.id)">بازگردانی
                                </button>
                                <button v-else class="btn btn-danger btn-sm"
                                        @click="removeSubProgress(progress.id)">حذف
                                </button>
                                <button class="btn btn-warning btn-sm btn-show-edit"
                                        @click="showEditModal(progress)">ویرایش
                                </button>


                            </div>
                            <p class="text-muted" v-text="progress.description"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="modalNew" tabindex="-1" role="dialog" aria-labelledby="modalTitleNew"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">افزودن زیر مجموعه جدید</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="save">
                            <div class="form-row">
                                <div class="form-group col-6">
                                    <label for="newTitle">عنوان</label>
                                    <input type="text" id="newTitle" v-model="newForm.title" class="form-control">
                                </div>
                                <div class="form-group col-6">
                                    <label for="newSection">بخش مربوطه</label>
                                    <select name="section" id="newSection" class="form-control"
                                            v-model="newForm.section_id">
                                        <option v-for="section in allSections" :value="section.id"
                                                v-text="section.title"></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group w-100">
                                    <label for="newDescription">توضیحات</label>
                                    <textarea name="newDescription" id="newDescription"
                                              rows="3" v-model="newForm.description" class="form-control"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">بستن</button>
                        <button type="button" class="btn btn-primary" @click="save">ذخیره</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="modalTitleEdit"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">ویرایش زیر مجموعه <span>{{editForm.title}}</span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="update">
                            <div class="form-row">
                                <div class="form-group col-6">
                                    <label for="editTitle">عنوان</label>
                                    <input type="text" id="editTitle" v-model="editForm.title" class="form-control">
                                </div>
                                <div class="form-group col-6">
                                    <label for="editSection">بخش مربوطه</label>
                                    <select name="section" id="editSection" class="form-control"
                                            v-model="editForm.section_id">
                                        <option v-for="section in allSections" :value="section.id"
                                                v-text="section.title"></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group w-100">
                                    <label for="editDescription">توضیحات</label>
                                    <textarea name="editDescription" id="editDescription"
                                              rows="3" v-model="editForm.description" class="form-control"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">بستن</button>
                        <button type="button" class="btn btn-primary" @click="update">ذخیره</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default{
        name: "SingleBaseProgress",
        data(){
            return {
                baseProgress: {
                    title: '',
                    description: '',
                    allProgress: [],
                },

                allSections: [],

                editForm: {
                    id: null,
                    title: null,
                    description: null,
                    section_id: null,
                    base_progress_id: this.$route.params.id
                },
                newForm: {
                    title: '',
                    description: '',
                    section_id: null,
                    base_progress_id: this.$route.params.id
                }
            }
        },
        mounted(){

            this.getAllProgress(this.$route.params.id);
            this.getAllSections();
        },
        methods: {

            getAllProgress(baseProgressId){
                axios.get(`base-progress/${baseProgressId}`)
                    .then(response => {
                        this.baseProgress.title = response.data.data.title;
                        this.baseProgress.description = response.data.data.description;
                        this.baseProgress.allProgress = response.data.data.progress;
                        this.showAlert(response.data.message);
                    })
                    .catch(error=>console.log(error.response))
            },

            getAllSections(){
                axios.get('section')
                    .then(response => this.allSections = response.data)
                    .catch(error=>console.log(error))
            },

            update(){
                this.updateProgress(this.editForm.id, this.editForm, '#modalEdit');
            },

            save(){
                console.log(this.form);
                axios.post('progress', this.newForm)
                    .then(response=> {
                        $('#modalNew').modal('hide');
                        this.showAlert(response.data.message);
                        this.getAllProgress(this.$route.params.id);

                    })
            },

            updateProgress(progressId, editForm, modal){
                axios.put(`progress/${progressId}`, editForm)
                    .then(response => {
                        editForm.id = null;
                        editForm.title = '';
                        editForm.description = '';
                        editForm.section_id = null;
                        editForm.base_progress_id = null;
                        $(modal).modal('hide');
                        this.getAllProgress(this.$route.params.id);
                        console.log(response.data);
                        this.showAlert(response.data.message);
                    })
                    .catch(error=> {
                        console.log(error.response);
                    })
            },

            showNewModal(){
                $('#modalNew').modal('show');
            },

            restoreSubProgress(progressId){
                axios.put(`progress/${progressId}/restore`)
                    .then(response=> {
//                    this.showAlert(response.data.message, 'info');
                this.getAllProgress(this.$route.params.id);
                })
            .catch(error => {
                    this.showAlert(error.response.data);
                })
            },
            showEditModal(progress){

                this.editForm.id = progress.id;
                this.editForm.title = progress.title;
                this.editForm.description = progress.description;
                this.editForm.section_id = progress.section_id;
                this.editForm.base_progress_id = progress.base_progress_id;
                $('#modalEdit').modal('show');
            },

            removeSubProgress(baseProgressId){
                axios.delete(`progress/${baseProgressId}`)
                    .then(response => {
//                        this.showAlert(response.data.message, 'warning');
                        this.getAllProgress(this.$route.params.id);
                    })
                    .catch(error => {
                        this.showAlert(error.response.data);
                    })
            },
        }
    }
</script>

<style scoped>
    .btn-show-new,
    .btn-show-edit {
        -webkit-border-radius: 10rem;
        -moz-border-radius: 10rem;
        border-radius: 10rem;;
        padding: 0.25em 0.75em;
        font-weight: normal;
        font-size: 0.75em;
        line-height: 1;
    }
</style>
