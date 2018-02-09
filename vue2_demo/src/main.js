import Vue from 'vue';
import VueRouter from 'vue-router';

import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';

Vue.use(VueRouter);
Vue.use(ElementUI);

import App from './App.vue';
import home from './components/home.vue';


const routes = [
    {
        path: '/home',
        component: home
    },
    {
        path: '*',
        redirect: '/home'
    }
];

const router = new VueRouter({
    routes,

    mode: 'history',

    scrollBehavior (to, from, savedPosition) {
        return new Promise((resolve, reject) => {
            setTimeout(() => {
                if (savedPosition) {
                    resolve(savedPosition);
                } else {
                    resolve({x: 0, y: 0});
                }
            }, 500);
        });
    }

});

new Vue({
    el: '#app',
    router,
    render: h => h(App)
});
