<template>
  <div class="chat-client-conversation" ref="id">
    <ul class="chat">
        <li class="left clearfix" v-for="message in orderedmessages">
            <div class="chat-body clearfix">
                <div class="header">
                    <strong class="primary-font">
                        {{ message.user.name }}
                    </strong>
                </div>
                <p>
                    {{ message.message }}
                </p>
            </div>
        </li>
    </ul>
  </div>
</template>

<script>
export default {
  props: ['messages'],
  data: function () {
    return {
      vmessages: this.messages
    }
  },
  computed: {
    orderedmessages: function () {
      return _.orderBy(this.vmessages, 'id')
    }
  },
  mounted() {
     Echo.private('chat.0')
        .listen('ChatMessage', (e) => {
           this.vmessages.push({
             message: e.message.message,
             user: e.user
           });
        });
     this.$eventBus.$on('newchatmessage', (message) => {
       this.addMessage(message)
     })
     this.scrollToBottom();
  },
  updated() {
     this.scrollToBottom();
  },

  methods: {
        addMessage(message) {
            this.vmessages.push(message);
            axios.post('/user/chat/messages', message).then(response => {});
        },
        scrollToBottom () {
            this.$refs.id.scrollTop = this.$refs.id.scrollHeight;
        }
  },
};
</script>
