<template>
    <div class="example">
        {{ test }}
    </div>
</template>

<script>
    export default{
        name : 'Example',
        data(){
            return {
                test: 'test placeholder',
            }
        },
        mounted(){
            console.log('test data ' + this.test);
        }
    }
</script>
