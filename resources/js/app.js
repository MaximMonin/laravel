/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

window.Vue = require('vue');

Vue.component('chat-messages', require('./components/ChatMessages.vue').default);
Vue.component('chat-form', require('./components/ChatForm.vue').default);

const app = new Vue({
    el: '#app',
    
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
