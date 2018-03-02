import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        loginUser: undefined
    },

    getters: {
        getLoginUser: (state) => state.loginUser
    },

    mutations: {
        dologin (state, user) {
            state.loginUser = user;
        },

        dologout (state) {
            state.loginUser = undefined;
        }
    },

    actions: {
        dologin: ({commit}, user) => commit('dologin', user),
        dologout: ({commit}) => commit('dologout')
    }

});
