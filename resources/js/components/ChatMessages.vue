<template>
  <div class="chat-client-conversation" ref="id" @scroll="handleScroll">
    <ul class="chat">
        <li class="left clearfix" v-for="message in orderedmessages">
           <div class="row">
 	     <div v-if="(message.user.avatar)">
               <img class="centered-and-cropped" width="30" height="30" style="border-radius:50%" :src="message.user.avatar"> 
	     </div>
	     <div v-else>
               <b-avatar variant="info" :text="avatartext(message.user.name)"></b-avatar>
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
        .centered-and-cropped { 
           object-fit: cover; 
        }
        .chat {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .chat li {
            padding-bottom: 0px;
        }
        .chat li div {
            margin-left: 1px;
            color: #777777;
        }
        .row {
           display: flex;
           flex-wrap: nowrap;
        }
        .chat li p {
	    border-radius: 0.5rem 0.5rem 0.5rem 0.5rem;
	    background: white;
            margin-left: 10px;
            margin-right: 20px;
            color: ;
        }
        .chat-client-conversation {
           padding: 0 5px;
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
  props: ['user'],
  data: function () {
    return {
      messages: [],
      pages: 0,
      isLoading: false,
      allPages: false
    }
  },
  computed: {
    orderedmessages: function () {
      return _.orderBy(this.messages, 'id')
    },
    maxid: function () {
      if (this.orderedmessages !== null && this.messages.length > 0 ) {
        return this.orderedmessages[this.messages.length - 1].id;
      }
      return 0;
    },
  },
  mounted() {
     this.fetchMessages ();
     Echo.private('chat.0')
        .listen('ChatMessage', (e) => {
           this.messages.push({
             id: e.message.id,
             message: e.message.message,
             user: e.user
           });
        });

     Event.listen('newchatmessage', (message) => {
       this.addMessage(message)
     });

     this.scrollToBottom();
     this.pages = parseInt (this.messages.length / 50, 10);
  },
  updated() {
     if (this.isLoading == false) {
       this.scrollToBottom();
     }
     this.pages = parseInt (this.messages.length / 50, 10);    
     this.isLoading = false;
  },

  methods: {
        avatartext: function (name) {
          return name.split(' ').map(function(str) { return str ? str[0].toUpperCase() : "";}).join('');
        },

        addMessage(message) {
            message.id = this.maxid + 1;
            this.messages.push(message);
            axios.post('/user/chat/messages', message).then(response => {});
        },
        scrollToBottom () {
            this.$refs.id.scrollTop = this.$refs.id.scrollHeight;
        },
        fetchMessages () {
          axios.get('/user/chat/messages').then(response => {
            if (response !== null) {
              var i;
              for (i = 0; i < response.data.length; i++) {
                 this.messages.push(response.data[i]);
              }
            }
          });
        },
        handleScroll: _.throttle (function () {
           // Autoload old messages while scrolling up
           if (this.$refs.id.scrollTop < 500 && this.isLoading == false && this.allPages == false) 
           {
             this.isLoading = true;
             axios.get('/user/chat/messages?page=' + (this.pages + 1) ).then(response => {
               if (response !== null) {
                 var i;
                 var j;
                 var isfound;
                 // Check message in array already 
                 for (i = 0; i < response.data.length; i++) {
                   isfound = false;
                   for (j = 0; j < this.messages.length; j++) {
                     if (this.messages[j].id == response.data[i].id) {
                       isfound = true;
                     }
                   }
                   if (!isfound) {
                     this.messages.push(response.data[i]);
                   }
                 }
               }
               if (response == null) {
                 this.allPages = true;   
               }
             });
           }
        }, 1000),
  },
};
</script>
