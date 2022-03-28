<template>
    <div>
        <Menus></Menus>
        <banner></banner>
        <div class="">
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
    import Menus from './menu';
    import Banner from './banner';
    export default {
        components: {
            Menus,Banner
        },
        data() {
            return {}
        },
        created() {

        }
    };
</script>
