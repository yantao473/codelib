<template>
    <div id="app">
        <mheader v-if="$store.state.loginUser"></mheader>
        <transition>
            <router-view></router-view>
        </transition>
    </div>
</template>

<style>
html, body{
    width: 100%;
    height: 100%;
    margin: 0;
    padding: 0;
}

#app {
    width:100%;
    height:100%;
}
</style>

<script>
import mheader from './components/mheader.vue';
export default {
    data (){
        return {
            isLogin: false
        };
    },

    methods:{},

    components: { mheader }
};
</script>
