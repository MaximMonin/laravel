/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

window.Vue = require('vue');

import BootstrapVue from 'bootstrap-vue';
Vue.use(BootstrapVue); 
import Vue2Editor from "vue2-editor";
Vue.use (Vue2Editor);

Vue.prototype.$eventBus = new Vue(); // Global event bus

Vue.component('chat-messages', require('./components/ChatMessages.vue').default);
Vue.component('chat-form', require('./components/ChatForm.vue').default);

const app = new Vue({
    el: '#app',
});
