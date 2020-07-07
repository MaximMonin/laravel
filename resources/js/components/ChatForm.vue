<template>
    <div class="input-group">
        <b-form-input :placeholder="entertext" v-model="newMessage" @keyup.enter="sendMessage"></b-form-input>
        <b-button variant="primary" @click="getfile">@</b-button>
        <input id="file" type="file" @change="uploadFiles" ref="myFiles" multiple="yes"/>
        <b-button variant="primary" @click="sendMessage">>></b-button>
    </div>
</template>
<style>
input[type="file"] {
    display: none;
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
      }
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
            this.files.push (this.baseurl + '/cdn/chat/' + response.data.name);
          });
        }    
      },
      sendMessage() {
        if (this.newMessage) {
                  Event.emit('newchatmessage', {
                    user: this.user,
                    message: this.newMessage,
                    id: 0
                  });
        }
        this.newMessage = ''
      },
    }
 }
</script>
