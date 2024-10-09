<template>
    <div class="col-lg-9">
        <div class="card">
            <div class="card-header">
                <h1 class="h4 d-inline-block">انتخاب نوع نرم افزار </h1>
                <h4 class="h6 d-inline-block"> افزودن مراحل اصلی به قرارداد</h4>
                <router-link class="badge badge-danger float-left" :to="`/admin/contract/${contract.id}/view`">بازگشت </router-link>
            </div>
            <form class="card-body" @submit.prevent="doSubmit">
                <div class="form-group">
                    <div class="d-flex flex-wrap">
                        <div v-for="software in allSoftwares" :key="software.id"
                             class="col-sm-6">
                            <div class="form-check">
                                <input :checked="isChecked(software.id)" type="checkbox" class="form-check-input"
                                       :id="`base_progress-${software.id}`" v-model="form.softwares"
                                       :value="software.id">
                                <label :title="software.title" class="form-check-label"
                                       :for="`base_progress-${software.id}`">{{software.title}}</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group form-row">
                    <button class="btn btn-success btn-lg w-25" type="submit">ذخیره</button>
                </div>

            </form>
        </div>
    </div>
</template>

<script>
    //    import AdminBaseProgressTab from './AdminBaseProgressTab.vue';
    export default{
        name: "SingleContractEdit",
//        components: {'admin-base-progress-tab': AdminBaseProgressTab},
        data(){
            return {
                contract: {
                    type_id: null,
                    user_id: null,
                    base_progress: [
                        {id: null}
                    ],
                    sub_progress: [],
                },
                allSoftwares: [],
//                allBaseProgress: [],
                form: {
                    softwares: [],
                },
                checkedBaseProgress: []
            }
        },
        mounted(){
            this.getContractData();
            this.getBaseProgressList();
            this.selectedBaseProgress();
        },
        methods: {
            getContractData(){
                axios.get(`contract/${this.$route.params.id}`)
                    .then(response => {
                        this.contract = response.data.data;
//                        console.log(this.contract);
                        response.data.data.base_progress.forEach(cbp=> {
                            this.form.progress.push(cbp.base_progress_id);
                            this.checkedBaseProgress.push(cbp.base_progress_id)
                        });
                        //this.contract.base_progress = response.data.data.base_progress;
//                        console.log(response.data.user_id, response.data.type_id);
                    })
                    .catch(error=>console.log(error))
            },
            getBaseProgressList(){
                axios.get('softwares')
                    .then(response => {
                        this.allSoftwares = response.data;

                    })
                    .catch(error=> {
                        this.showAlert(error.response.data);
                    })
            },
            doSubmit(){
                axios.put(`contract/${this.$route.params.id}`, this.form)
                    .then(response => {
                        this.showAlert(response.data.message);
                        this.getContractData();
//                        this.$router.push(`/admin/contract/${this.$route.params.id}/view`);
                    })
                    .catch(error => {
                        console.log(error);
                    })
            },
            selectedBaseProgress(){
//                console.log(this.contract.base_progress);
            },
            isChecked(baseProgressId){
                console.log(baseProgressId);
                return (this.checkedBaseProgress.includes(baseProgressId));

//                console.log(baseProgressId);
//                console.log(this.checkedBaseProgress);

            }
        }
    }
</script>
