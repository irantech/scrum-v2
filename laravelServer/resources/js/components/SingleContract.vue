<template>
    <div class="singleContract col-lg-9">
        <div class="card">
            <div class="card-header">اطلاعات قرارداد</div>
            <div class="card-body">
                <p class="card-text list-group">
                    <span class="list-group-item">
                        <span class="float-left font-weight-bold">کد سیستمی قرارداد: </span> {{contract.contract_code}}
                    </span>
                    <span class="list-group-item">
                        <span class="float-left font-weight-bold">تاریخ عقد قرارداد :</span> {{contract.sign_date}}
                    </span>
                    <span class="list-group-item">
                        <span class="float-left font-weight-bold">تاریخ شروع:</span> {{contract.start_date}}
                    </span>
                    <span class="list-group-item">
                        <span class="float-left font-weight-bold">تاریخ پایان قرارداد:</span> {{contract.end_date}}
                    </span>
                </p>
            </div>
        </div>

    </div>
</template>

<script>
    export default{
        name: "singleContract",
        data(){
            return {
                contract: {
                    start_date: 'loading',
                    end_date: 'loading',
                    contract_code: 'loading',
                    sign_date: 'loading',
                },
            }
        },
        mounted(){
            this.Authorized();
        },
        created(){
            axios.get(`contract/${this.$route.params.id}`)
                .then(response => this.contract = response.data)
                .catch(error => console.log(error))
        }
    }
</script>

<style scoped>
    .singleContract * {
        text-align: right;
    }
</style>
