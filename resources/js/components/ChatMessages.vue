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
      if (this.orderedmessages !== null) {
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
