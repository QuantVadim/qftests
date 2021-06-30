<template>
  <div v-if="MYID">
    <div v-if="test != undefined">
      <block>
        <it-input v-model="test.name" placeholder="Название"></it-input>
        <it-textarea
          v-model="test.description"
          :resize-on-write="true"
          placeholder="Описание"
        ></it-textarea>
      </block>
      <TestQuests :data="test.body" :mode="'editor'" />
      <block>
        <it-button block @click="SaveTest">Сохранить</it-button>
      </block>
    </div>
    <div v-else-if="error == false" class="center-loading">
      <it-loading></it-loading><br />
      <div>Загрузка</div>
    </div>
    <div v-else>
      <block>
      <it-alert type="danger" :title="'Ошибка'" :body="'У вас нет доступа к этопу объекту'"/>
      </block>
    </div>
  </div>
  <div v-else>
    <block>
      <it-alert type="danger" :title="'Нет доступа'" :body="'Необходимо авторизироваться'"/>
    </block>
  </div>
</template>

<script>
import TestQuests from "../../components/TestQuests";

export default {
  components: {
    TestQuests,
  },
  computed:{
    MYID(){return this.$store.getters.MYID;}
  },
  data() {
    return {
      test: undefined,
      error: false,
    };
  },
  beforeRouteLeave(){
    return confirm('Вы уверены, что хотите уйти?');
  },
  methods:{
    SaveTest(){
      let obj = {
        q: 'test_save',
        me: this.$store.state.ME.data,
        test: this.test,
      }
      this.axios.post(window.c.API, obj).then(itm => {
        console.log(itm.data);
        if(itm.data?.data){
          let test_id = itm.data?.data;
          this.$router.replace({path: `/test/${test_id}/editor`})
        }
      });
    }
  },
  created(){
    if(this.$route.params.id == 'new'){
      this.test = {
        test_id: this.$route.params.id,
        usr_id: this.MYID,
        name: 'Новый тест',
        description: '',
        body: []
      }
    }else{
      let obj = {
        q: 'get_test_editor',
        me: this.$store.state.ME.data,
        test_id: this.$route.params.id,
      }
      this.axios.post(window.c.API, obj).then(itm => {
        console.log(itm.data);
        if(itm.data?.data){
          this.test = itm.data.data;
        }else if(itm.data?.error){
          this.error = itm.data.error;
        }
      });
    }
  },
};
</script>

<style>
textarea.it-textarea {
  overflow: hidden !important;
}
</style>
