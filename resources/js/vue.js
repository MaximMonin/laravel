/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

window.Vue = require('vue');

import Vuetify from "vuetify";
Vue.use(Vuetify);
import Vue2Editor from "vue2-editor";
Vue.use (Vue2Editor);

Vue.component('chat-messages', require('./components/ChatMessages.vue').default);
Vue.component('chat-form', require('./components/ChatForm.vue').default);

const app = new Vue({
    el: '#app',
    vuetify: new Vuetify(),    
    data: {
        messages: []
    },

    mounted() {
        this.fetchMessages();

        Echo.private('chat.0')
            .listen('ChatMessage', (e) => {
                this.messages.push({
                    message: e.message.message,
                    user: e.user
                });
            });
    },

    methods: {
        fetchMessages() {
            axios.get('/user/chat/messages').then(response => {
                this.messages = response.data;
            });
        },
        addMessage(message) {
            this.messages.push(message);

            axios.post('/user/chat/messages', message).then(response => {});
        }
    }
});
