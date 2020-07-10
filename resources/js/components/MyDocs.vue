<template>
  <div class="docs-view">
    <div class="docs" v-for="file in files">
       <p>
          {{ file.filename }}
          {{ file.created_at }}
       </p>

       <a :href="baseurl + '/' + file.file"/> 
    </div>
  </div>
</template>
<style>
        .centered-and-cropped { 
           object-fit: cover; 
        }
        .docs-view {
           display: flex;
           flex-wrap: wrap;
           overflow-y: auto;
           overflow-x: hidden;
           max-height: 100%;
        }
        .docs {
            margin: 0;
            padding: 5px;
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
      files: [],
      pages: 0,
      isLoading: false,
      allPages: false,
    }
  },
  computed: {
    baseurl: function () {
      return this.$store.state.baseurl;
    },
  },
  mounted() {
     this.fetchFiles ();
     this.pages = parseInt (this.files.length / 20, 10);
     window.addEventListener("scroll", this.handleScroll); 
  },
  updated() {
     this.pages = parseInt (this.files.length / 20, 10);    
     this.isLoading = false;
  },
  beforeDestroy() {
     window.removeEventListener('scroll', this.handleScroll);
  },
  methods: {
        fetchFiles () {
          axios.get('/user/docs').then(response => {
            if (response !== null) {
              var i;
              for (i = 0; i < response.data.length; i++) {
                 this.files.push(response.data[i]);
              }
            }
          });
        },
        handleScroll: _.throttle (function () {
           // Autoload new files while scrolling down
           const scrollY = window.scrollY;
           const visible = document.documentElement.clientHeight;
           const pageHeight = document.documentElement.scrollHeight;
           if (visible + scrollY + 100 >= pageHeight && this.isLoading == false && this.allPages == false) 
           {
             this.isLoading = true;
             axios.get('/user/docs?page=' + (this.pages + 1) ).then(response => {
               if (response !== null) {
                 var i;
                 var j;
                 var isfound;
                 // Check files in array already 
                 for (i = 0; i < response.data.length; i++) {
                   isfound = false;
                   for (j = 0; j < this.files.length; j++) {
                     if (this.files[j].id == response.data[i].id) {
                       isfound = true;
                     }
                   }
                   if (!isfound) {
                     this.files.push(response.data[i]);
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
