<template>
  <div class="chat-client-conversation" ref="id" @scroll="handleScroll">
    <ul class="chat">
        <li class="left clearfix" v-for="message in orderedmessages">
           <div class="chatrow">
 	     <div v-if="(message.user.avatar)">
               <img class="centered-and-cropped" width="30" height="30" style="border-radius:50%" :src="message.user.avatar"> 
	     </div>
	     <div v-else>
               <b-avatar variant="info" :text="avatartext(message.user.name)"></b-avatar>
	     </div>
             <p>
                  {{ message.message }}
                  <span class="chattime">
                     {{ realdate(message.created_at) }}
                  </span>
             </p>
             
           </div>
           <div v-if="(message.files)">
             <div class="chatfiles" v-for="file in JSON.parse(message.files)">
                <img v-if="file.mime.match('image*')" class="centered-and-cropped" width="300" height="300" :src="file.url"/> 
                <a :href="file.url" target="_blank">
                   {{file.name}}
                </a>
             </div>
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
        .chatrow {
           display: flex;
           flex-wrap: nowrap;
        }
        .chatfiles {
            padding: 5px;
        }
        .chattime {
           padding-left: 30px;
           font-size: 50%;
        }
        .chat li p {
	    border-radius: 0.5rem 0.5rem 0.5rem 0.5rem;
	    background: white;
            margin-left: 10px;
            margin-right: 20px;
            font-size: 100%;
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
  data: function () {
    return {
      messages: [],
      pages: 0,
      isLoading: false,
      allPages: false,
    }
  },
  computed: {
    user: function () {
      return this.$store.state.user;
    },
    orderedmessages: function () {
      return _.orderBy(this.messages, 'id')
    },
    timezone: function () {
      return this.$store.state.timezone;  // in minutes (+3 = 180)
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
             files: e.message.files,
             created_at: e.message.created_at,
             user: e.user,
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
        realdate: function (dtstring) {
           var d = new Date(dtstring);
           var dt = new Date();
           if (((dt - d) / 1000 / 60) > 12 * 60 ) { 
             // More than 12 hours ago 
             dtstring = this.pad2(d.getDate()) + '-' + this.pad2(d.getMonth()+1) + '-' + d.getFullYear()
               + ' ' + this.pad2(d.getHours())  + ':' + this.pad2(d.getMinutes());
             return dtstring;
           }
           return this.pad2(d.getHours())  + ':' + this.pad2(d.getMinutes());
        },
        pad2: function (number) {
           var length = 2;
           var str = '' + number;
           while (str.length < length) {
             str = '0' + str;
           } 
           return str;
        },
        addMessage(message) {
            message.id = this.maxid + 1;
            this.messages.push(message);

            var files = message.files;
            message.files = JSON.stringify(files);

            var d = message.created_at;
            var MS_PER_MINUTE = 60000;
            var dt = new Date(d.getTime() - this.timezone * MS_PER_MINUTE);
            var dtstring = dt.getFullYear()
              + '-' + this.pad2(dt.getMonth()+1)
              + '-' + this.pad2(dt.getDate())
              + 'T' + this.pad2(dt.getHours())
              + ':' + this.pad2(dt.getMinutes())
              + ':' + this.pad2(dt.getSeconds()) + '.000000Z';
            message.created_at = dtstring;

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
