<template>
    <div class="input-group">
        <input id="btn-input" type="text" name="message" class="form-control input-sm" placeholder="EnterText" v-model="newMessage" @keyup.enter="sendMessage">
    </div>
</template>

<script>
  export default {
    data() {
        return {
            newMessage: '',
        }
    },
    computed: {
      user: function () {
        return this.$store.state.user;
      },
      lang: function () {
        return this.$store.state.lang;
      },
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
