<template>
    <div class="input-group">
        <input id="btn-input" type="text" name="message" class="form-control input-sm" :placeholder="message" v-model="newMessage" @keyup.enter="sendMessage">
    </div>
</template>

<script>
    export default {
      props: {
        message: String,
        user: Object,
        },
        data() {
            return {
                newMessage: ''
            }
        },
        methods: {
            sendMessage() {
                Event.emit('newchatmessage', {
                    user: this.user,
                    message: this.newMessage,
                    id: 0
                });
                this.newMessage = ''
            }
        }
    }
</script>
