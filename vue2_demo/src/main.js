import Vue from 'vue';
import VueRouter from 'vue-router';
import 'font-awesome/css/font-awesome.min.css';
import 'buefy/lib/buefy.css';
import Buefy from 'buefy';
import VueCookie from 'vue-cookie';

import qs from 'qs';
import axios from 'axios';

import store from './store';

Vue.use(VueCookie);
Vue.use(VueRouter);
Vue.use(Buefy, {
    defaultIconPack: 'fa'
});


import App from './App.vue';
import login from './components/login.vue';
import index from './components/index.vue';
import pmetal from './components/pmetal.vue';

const notes = {
    template: `
    <p> 备忘 </p>
    `
};


const routes = [
    {
        path: '/',
        component: index
    },

    {
        path: '/login',
        component: login
    },

    {
        path: '/pmetal',
        component: pmetal
    },

    {
        path: '/notes',
        component: notes
    },

    {
        path: '/settings',
        component: notes
    },

    {
        path: '*',
        redirect: '/'
    }
];

const router = new VueRouter({
    routes,
    mode: 'history',
    savedPosition: true,
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

router.beforeEach((to, from, next) => {
    const username = router.app.$cookie.get('loginUser');
    const storeUser = store.state.loginUser;
    if (!storeUser && to.fullPath !== '/login') {
        const path = '/login';
        if (username) {
            axios.post('/user/loginVerify', qs.stringify({username}))
                .then((response) => {
                    if (response.data.status === 'OK') {
                        store.dispatch('dologin', username);
                        next();
                    } else {
                        next({path});
                    }
                })
                .catch(() => {
                    next({path});
                });
        } else {
            next({path});
        }
    } else if (!storeUser && to.fullPath === '/login') {
        if (username) {
            axios.post('/user/loginVerify', qs.stringify({username}))
                .then((response) => {
                    if (response.data.status === 'OK') {
                        store.dispatch('dologin', username);
                        next({path: '/'});
                    } else {
                        next();
                    }
                })
                .catch(() => {
                    next();
                });
        } else {
            next();
        }
    } else if (storeUser && to.fullPath !== '/login') {
        next();
    } else {
        next({path: '/'});
    }
});

new Vue({
    el: '#app',
    store,
    router,
    render: h => h(App)
});
