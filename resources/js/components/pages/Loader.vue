<template>
    <div class="">
        <ring-loader :loading="loading" class="page_loader" :color="color" :size="size"></ring-loader>
    </div>
</template>

<script>
import RingLoader from 'vue-spinner/src/RingLoader.vue'
    export default {
        props: ['loading'],
        name: 'Loader',
        components: {
            RingLoader,
        },
        data(){
            return{
                color: '#2667bc',
                size: '65px',
            }
        },
    }
</script>
