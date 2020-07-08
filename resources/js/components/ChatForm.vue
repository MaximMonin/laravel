<template>
    <div class="input-group">
        <b-form-input :placeholder="entertext" v-model="newMessage" @keyup.enter="sendMessage"></b-form-input>
        <b-button variant="primary" @click="getfile">📎</b-button>      
        <b-button id="removefiles" v-if="this.files.length > 0" variant="primary" @click="removeFiles">
        {{ delfiles }}
        </b-button>      
        <b-tooltip v-if="this.files.length > 0" target="removefiles" custom-class="chat-tooltip" triggers="hover">
          <li class="left clearfix" v-for="file in files">
             {{ file.name }} ( {{ Intl.NumberFormat().format(file.size / 1024 / 1024) }} Mb )
             <img v-if="file.mime.match('image*')" class="centered-and-cropped" width="100" height="100" :src="file.url"/> 
          </li>
        </b-tooltip>
        <input id="file" type="file" @change="uploadFiles" ref="myFiles" multiple="yes"/>
        <b-button variant="primary" @click="sendMessage">➣</b-button>
    </div>
</template>
<style>
input[type="file"] {
    display: none;
}
.chat-tooltip > .tooltip-inner{
  /* Removes the default max-width */
  max-width: none;
  padding: 5px;
}
</style>
<script>
  export default {
    data() {
        return {
            newMessage: '',
            files: [],
        }
    },
    computed: {
      user: function () {
        return this.$store.state.user;
      },
      lang: function () {
        return this.$store.state.lang;
      },
      baseurl: function () {
        return this.$store.state.baseurl;
      },
      entertext: function () {
        var text = {en: 'Enter message', ru: "Введите сообщение", uk: "Введіть повідомлення" };
        return text[this.lang];
      },
      delfiles: function () {
        var text = {en: 'Delete', ru: "Удалить", uk: "Видалити" };
        return text[this.lang] + ' (' + this.files.length + ')';
      },
    },
    methods: {
      getfile () {
        this.$refs.myFiles.click();
      },
      uploadFiles () {
        var filelist = this.$refs.myFiles.files;
        var fl= filelist.length;
        var i=0;

        while ( i < fl) {
          var file = filelist[i];
          i++;
          var formData = new FormData();
          formData.append("file", file);
          axios.post('/upload/local?filedir=cdn/chat&action=chat', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
          }).then(response => {
            this.files.push ({
               name: response.data.originalname,
               mime: response.data.mime_type,
               size: response.data.size,
               url: this.baseurl + '/cdn/chat/' + response.data.name,
               removeurl: '/cdn/chat/' + response.data.name,
            });
          });
        }    
      },
      removeFiles () {
        var filelist = this.files;
        var fl = filelist.length;
        var i = 0;

        while ( i < fl) {
          var file = filelist[i];
          i++;
          axios.post('/upload/local/delete', {file: file.removeurl});
        }
        this.files = [];
      },
      sendMessage() {
        if (this.newMessage || this.files.length > 0) {
                  Event.emit('newchatmessage', {
                    user: this.user,
                    message: this.newMessage,
                    files: this.files,
                    id: 0
                  });
        }
        this.newMessage = '';
        this.files = [];
      },
    }
 }
</script>
