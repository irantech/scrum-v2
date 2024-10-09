<template>

    <div :class="`progress-circle ${bg} value-${percentage}`">
        <span>{{percentage}}%</span>
        <div class="slice">
            <div class="bar"></div>
            <div class="fill"></div>
        </div>
    </div>
</template>

<script>
    export default{
        name: "progressCircle",
        props: ['percentage', 'bg'],
        data() {
            return {}
        }
    }
</script>

<style scoped>
    .progress {
        margin-bottom: 1rem;
    }

    .bg-hold {
        background-color: rebeccapurple;
    }

    .bg-complete {
        background-color: seagreen;
    }

    .bg-running {
        background-color: #5E740B;
    }

    .bg-cancel {
        background-color: #fa4215;
    }


</style>
