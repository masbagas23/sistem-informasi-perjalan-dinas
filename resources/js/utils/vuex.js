import Vue from "vue"
import Vuex from "vuex"
import createPersistedState from "vuex-persistedstate"
import appModules from '@app/state'

Vue.use(Vuex)

const store = new Vuex.Store({
    modules: appModules,
    state : {
        user: null,
        token: localStorage.getItem('token'),
        errors: []
    },
    getters: {
        user: (state) => {
            return state.user
        },
        isAuth: state => {
            return state.token != "null" && state.token != null
        },
    },
    actions: {
        user: (context, user) => {
            context.commit('user', user)
        }
    },
    mutations: {
        user: (state, user) => {
            state.user = user
        },
        SET_TOKEN(state, payload) {
            state.token = payload
        },
        SET_ERRORS(state, payload) {
            state.errors = payload
        },
        CLEAR_ERRORS(state) {
            state.errors = []
        }
    },
    plugins: [createPersistedState()]
})

export default store
