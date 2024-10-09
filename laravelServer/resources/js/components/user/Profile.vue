<template>
    <div class="profile col-lg-9">
        <div v-if="Auth" class="card">
            if
        </div>
        <div class="card2" v-else>
            else
        </div>
    </div>
</template>

<script>
    export default{
        data(){
            return {
                Auth: window.Laravel.Auth
            }
        }
    }
</script>
