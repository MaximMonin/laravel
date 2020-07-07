/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

window.Vue = require('vue');

import BootstrapVue from 'bootstrap-vue';
Vue.use(BootstrapVue); 
import VueRouter from 'vue-router';
Vue.use(VueRouter); 

import Vuex from 'vuex';
Vue.use(Vuex);
const store = new Vuex.Store({
  state: {
    lang: '',
    user: null,
  },
  mutations: {
    changelanguage (state, lang) {
      state.lang = lang;
    },
    login (state, user) {
      state.user = user;
    },
    logout (state) {
      state.user = null;
    },
  }
})

import Vue2Editor from "vue2-editor";
Vue.use (Vue2Editor);

// Vue event bus
window.Event = new class {
  constructor () { this.vue = new Vue (); }
  emit (event, data = null) { this.vue.$emit (event, data); }
  listen (event, callback) { this.vue.$on (event, callback); }
}
// Response errors
class Errors {
  constructor () { this.errors = {}; }
  get (field) { 
    if (this.errors[field]) { return this.errors[field][0]; }
    return "";
  }
  record (errors) { this.errors = errors; }
  clear (field) { delete this.errors[field]; }
  has (field) { return this.errors.hasOwnProperty (field); }
  any () { return Object.keys(this.errors).length > 0; }
}

Vue.component('init-store', require('./components/InitStore.vue').default);
Vue.component('chat-messages', require('./components/ChatMessages.vue').default);
Vue.component('chat-form', require('./components/ChatForm.vue').default);

const app = new Vue({
    el: '#app',
    store: store,
});
