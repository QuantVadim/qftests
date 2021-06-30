<template>
<div>
  <component v-if="$store.state.ME.data?.usr_id || layout == 'empty-layout'" :is="layout"></component>
  <div v-else-if="isUserLoading && !$store.state.ME.data?.usr_id">
    <div class="center"><it-loading/></div>
  </div>
  <div v-else>
    <block class="center window-login">
      <br>
      <div>Необходимо выполнить вход</div><br>
      <it-button block :type="'primary'" class='h-enter-vk' @click="enterVK">Войти через Вконтакте</it-button>
    </block>
  </div>
</div>
</template>


<script>
import EmptyLayout from './views/layouts/EmptyLayout';
import MainLayout from './views/layouts/MainLayout';
import Block from './components/Block';
import CONST from './others/Constants';

export default {
  computed:{
    layout(){
      return (this.$route.meta?.layout || 'empty')+'-layout';
    }
  },
  data(){
    return{
      isUserLoading: true,
    }
  },
  async beforeCreate(){
    window.c = CONST;
    await this.$store.dispatch('LOAD_USER');
    this.isUserLoading = false;
  },
  methods:{
   // ...mapActions(['LOAD_USER']),
   enterVK(){
        document.location = 'https://oauth.vk.com/authorize?client_id=7870026&display=page&redirect_uri=http://localhost:8080/auth/vk&scope=offline&response_type=token&v=5.52';
    }
  },
  components:{
    EmptyLayout, MainLayout, Block
  }
}
</script>

<style>
*{
  -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
}
pre{
  white-space:pre-wrap;
    font-family: Inter,system-ui,-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,Noto Sans,sans-serif;
  -webkit-font-smoothing: antialiased;
  font-size: 14px;
}

.it-toggle:focus{
  box-shadow: none !important;
}
.gray{
  color: gray;
}
.window-login{
  width: 300px;
  margin: auto;
  margin-top: 60px;
}
.h-enter-vk{
display: block;
height: 41px;
}
.center{
  text-align: center;
}
body{
  padding: 0px;
  margin: 0px;
  background-color: rgb(206, 206, 206);
}
#app {
  font-family: Inter,system-ui,-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,Noto Sans,sans-serif;
  -webkit-font-smoothing: antialiased;

}

#nav {
  padding: 30px;
}

#nav a {
  font-weight: bold;
  color: #2c3e50;
}

#nav a.router-link-exact-active {
  color: #42b983;
}
</style>
