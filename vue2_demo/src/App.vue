<template>
    <div id="app">
        <headercomponent></headercomponent>
        <transition>
            <router-view></router-view>
        </transition>
    </div>
</template>
<style>
body {
    margin: 0px;
}
</style>

<script>
import headercomponent from './components/headercomponent.vue';
export default {
    components: {headercomponent}
};
</script>
