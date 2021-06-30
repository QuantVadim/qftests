<template>
  <Block>
    <div v-if="USER == undefined" class="center-loading">
      <it-loading></it-loading><br />
      <div>Загрузка</div>
    </div>
    <div v-else>
      <div class="center">
      <it-avatar :src="USER.avatar" size="100px" />
      <h3>{{ USER.first_name + " " + USER.last_name }}</h3>
      </div>
    </div>
    <div v-if="isMe">
      <it-button block @click="logout">Выход</it-button>
    </div>
  </Block>
</template>

<script>
import Block from "../../components/Block"

export default {
  components:{Block},
  data() {
    return {
      USER: undefined,
    };
  },
  computed: {
    isMe(){
      return ((this.USER?.usr_id || -10) == this.$store.state.ME.data?.usr_id);
    },
    usr_id(){
      this.load();
      return this.$route.params.usr_id;
    }
  },
  methods: {
    async load() {
      let obj = {
        q: "get_user",
        data: {
          usr_id: this.$route.params?.usr_id
        },
      };
      await this.axios.post(window.c.API, obj).then((itm) => {
        console.log(obj, itm.data)
        if (itm.data?.data) {
          this.USER = itm.data.data;
          console.log(this.USER);
        }
      });
    },
    logout(){
      document.cookie = "usrid=null; max-age=-1; path=/";
      document.cookie = "mykey=null; max-age=-1; path=/";
      document.location = "/";
    }
  },
  watch:{
  },
   beforeRouteUpdate(){ 
    setTimeout( ()=> {this.load()}, 0);
  },
  mounted() {
   this.load()
  },
};
</script>

<style scoped>
.center{
  text-align: center;
}
</style>