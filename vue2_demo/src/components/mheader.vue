<template>
    <nav class="navbar is-primary">
        <div class="navbar-brand">
            <router-link class="navbar-item" to="/">
                <i class="fa fa-home" aria-hidden="true"></i>
            </router-link>
            <div class="navbar-burger burger" :class="{'is-active': isActive}" data-target="navMenu">
                <span @click="toggleActive"></span>
                <span @click="toggleActive"></span>
                <span @click="toggleActive"></span>
            </div>
        </div>

        <div id="navMenu" class="navbar-menu" :class="{'is-active': isActive}">
            <div class="navbar-start">
                <router-link v-for="(v, k, i) in menus" :key="i" class="navbar-item" :to="'/'+k" @click.native="toggleActive">{{v}}</router-link>
            </div>

            <div v-if="$store.state.loginUser" class="navbar-end">
                <div class="navbar-item has-dropdown is-hoverable">
                    <a class="navbar-link" href="javascript:void(0);">
                        {{$store.state.loginUser}}
                    </a>
                    <div class="navbar-dropdown is-boxed">
                        <router-link class="navbar-item" to="/settings">设置</router-link>
                        <hr class="navbar-divider">
                        <a class="navbar-item" href="javascript:void(0);" @click="dologout">退出 </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
import axios from 'axios';
export default {
    data () {
        return {
            isActive: false,
            menus: {
                home: '首页',
                pmetal: '贵金属',
                notes: '备忘'
            }
        }
    },

    methods: {
        toggleActive (){
            this.isActive = !this.isActive;
        },

        dosettings(){
            this.toggleActive();
        },

        dologout(){
            this.$store.dispatch('dologout');
            this.$cookie.delete('loginUser');

            axios.post('/user/dologout')
                .then((response) => {
                    if(response.data.status == 'OK'){
                        this.$router.push({path: '/login'});
                    }else{
                        this.$dialog.alert('退出失败')
                    }
                })
                .catch((error)=>{
                    this.$dialog.alert('未知错误: ' + error)
                });
        }
    }
};
</script>
