<template>
    <footer class="site-footer bg-light text-center text-black-50">
        <div class="container">
            <p v-text="copyrightText"></p>
        </div>
    </footer>
</template>

<script>
    export default{
        name: "VueFooter",
        data(){
            return {
                copyrightText: 'تمام حقوق مادی و معنوی سایت مربوط به ایران تکنولوژی میباشد',
            }
        }
    }
</script>

<style scoped>
    .site-footer {
        position: sticky;
        bottom: 0;
        left: 0;
        right: 0;

    }
</style>
