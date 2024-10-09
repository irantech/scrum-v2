<template>

    <div :class="`progress-bar progress-bar-success status-${status}`" role="progressbar"
         :style="`width:${percentage}%`" data-toggle="tooltip" :title="title"
         v-text="title">
    </div>
</template>

<script>
    export default{
        name: "progressBar",
        props: {
            percentage: {default: 0},
            title: {default: ''},
            status: {default: 'hold'}

        },
        data(){
            return {}
        },

        created(){
            return {
                percentage: 0,
                title: this.title,
                status: this.status
            }
        },
        methods: {
            showTooltip(msg) {
                alert(msg);
            },

            scale: function (percentage) {

            }
        }
    }
</script>

<style>
    .progress-bar {
        width: 0;
        -webkit-transition: width .3s linear;
        -moz-transition: width .3s linear;
        -ms-transition: width .3s linear;
        -o-transition: width .3s linear;
        transition: width 3s linear;
        cursor: pointer;
    }

</style>
