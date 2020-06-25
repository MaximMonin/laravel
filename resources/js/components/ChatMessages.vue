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
<style>
        .chat {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .chat li {
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px dotted #B3A9A9;
        }
        .chat li .chat-body p {
            margin: 0;
            color: #777777;
        }
        .chat-client-conversation {
           padding: 0 12px;
           overflow-y: auto;
           overflow-x: hidden;
           position: absolute;
           bottom: 0; left: 0; right: 0;
           max-height: 100%;
        }
        ::-webkit-scrollbar-track {
            -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
            background-color: #F5F5F5;
        }
        ::-webkit-scrollbar {
            width: 3px;
            background-color: #F5F5F5;
        }
        ::-webkit-scrollbar-thumb {
            -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
            background-color: #555;
        }
</style>
<script>
export default {
  props: ['messages'],
  data: function () {
    return {
      vmessages: this.messages,
      pages: 0,
      isLoading: false,
      allPages: false
    }
  },
  computed: {
    orderedmessages: function () {
      return _.orderBy(this.vmessages, 'id')
    },
    maxid: function () {
      if (this.orderedmessages !== null && this.vmessages.length > 0 ) {
        return this.orderedmessages[this.vmessages.length - 1].id;
      }
      return 0;
    }
  },
  destroyed () {
    this.$refs.id.removeEventListener('scroll', this.handleScroll);
  },
  mounted() {
     this.$refs.id.addEventListener('scroll', this.handleScroll);

     Echo.private('chat.0')
        .listen('ChatMessage', (e) => {
           this.vmessages.push({
             id: e.message.id,
             message: e.message.message,
             user: e.user
           });
        });

     this.$eventBus.$on('newchatmessage', (message) => {
       this.addMessage(message)
     });

     this.scrollToBottom();
     this.pages = parseInt (this.vmessages.length / 50, 10);
  },
  updated() {
     if (this.isLoading == false) {
       this.scrollToBottom();
     }
     this.pages = parseInt (this.vmessages.length / 50, 10);    
     this.isLoading = false;
  },

  methods: {
        addMessage(message) {
            message.id = this.maxid + 1;
            this.vmessages.push(message);
            axios.post('/user/chat/messages', message).then(response => {});
        },
        scrollToBottom () {
            this.$refs.id.scrollTop = this.$refs.id.scrollHeight;
        },
        // Autoload old messages while scrolling up
        handleScroll() {
           if (this.$refs.id.scrollTop < 300 && this.isLoading == false && this.allPages == false) {
             this.isLoading = true;
             axios.get('/user/chat/messages?page=' + (this.pages + 1) ).then(response => {
               if (response !== null) {
                 var i;
                 var j;
                 var isfound;
                 // Check message in array already 
                 for (i = 0; i < response.data.length; i++) {
                   isfound = false;
                   for (j = 0; j < this.vmessages.length; j++) {
                     if (this.vmessages[j].id == response.data[i].id) {
                       isfound = true;
                     }
                   }
                   if (!isfound) {
                     this.vmessages.push(response.data[i]);
                   }
                 }
               }
               if (response == null) {
                 this.allPages = true;   
               }
             });
           }
        },
  },
};
</script>
