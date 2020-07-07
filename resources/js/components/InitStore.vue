<template>
</template>

<script>
  export default {
     mounted() {
        this.initstore ();
     },
     methods: {
        initstore () {
          axios.get('/initstore').then(response => {
            if (response !== null) 
            {
              this.$store.commit ('changelanguage', response.data.lang);
              if (response.data.user)
                this.$store.commit ('login', response.data.user);
              else
                this.$store.commit ('logout');
            }
          });
        },
     }
  }
</script>
