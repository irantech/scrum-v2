<template>
    <div><span v-text="baseId"></span></div>
</template>
<script>
    export default{
        props : {
            baseId : null
        },
        data() {
            return {
                baseProgress : null,
            }
        },
        mounted(){
            this.getBaseProgressChild(1);
        },
        methods : {
            getBaseProgressChild(baseId){
                axios.get(`base-progress/${baseId}`)
                    .then(response=>{
                        this.baseProgress = response.data.data;
                    })
                    .catch(error=>{
                        console.log(error.response);
                    })
            }
        }
    }
</script>
