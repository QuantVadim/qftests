<template>
  <div>
    <block>
      <h3>Мои решения:</h3>
      <Divider/>
      <div v-for="(item, index) in items" :key="index">
        <ResultCard :data="item" @delete="toDeleteResult" :index="index"/>
        <Divider/>
      </div>
      <it-button @click="Load" :loading="isLoading" v-if="isButtonLoad" block>Еще</it-button>
    </block>
    <!-- Окно удаления результата -->
    <it-modal v-model="isWinDelResult">
      <template #header>
        <h3>Удаление решения</h3>
      </template>
      <template #body>
        <p>
          Вы уверены, что хотите удалить решение пользователя
          <b> {{ curDelResult?.user_name }}</b
          >?
        </p>
      </template>
      <template #actions>
        <it-button @click="isWinDelResult = false">Отмена</it-button>
        <it-button :loading="isDeletingResult" @click="DeleteResult" type="danger">Удалить</it-button>
      </template>
    </it-modal>
    <!--END Окно удаления результата -->
  </div>
</template>

<script>
import Divider from 'primevue/divider';
import ResultCard from '../../components/Items/Result';

export default {
  components: {
    Divider, ResultCard
  },
  data(){
    return{
      items: [],
      isLoading: false,
      isButtonLoad: true,

      isWinDelResult: false,
      curDelResult: undefined,
      curDelResultIndex: undefined,
      isDeletingResult: false,
    }
  },
  methods:{
    toDeleteResult(data, index) {
      this.curDelResult = data;
      this.curDelResultIndex = index;
      this.isWinDelResult = true;
    },
    DeleteResult() {
      if (this.isDeletingResult) return false;
      this.isDeletingResult = true;
      let obj = {
        q: "delete_result",
        me: this.$store.state.ME.data,
        res_id: this.curDelResult.res_id,
      };
      this.axios.post(window.c.API, obj).then((itm) => {
        this.isDeletingResult = false;
        this.isWinDelResult = false;
        console.log(itm.data);
        if (itm.data?.data){
          this.$Notification.success({
            title: "Решение удалено",
            text: `Решение было успешно удалено`,
            placement: 'bottom-right',
          });
          this.items.splice(this.curDelResultIndex, 1);
        }else if(itm.data?.error){
          this.$Notification.danger({
            title: "Ошибка удаления",
            text: itm.data?.error,
            placement: 'bottom-right',
          });
        }
      });
    },
    async Load(){
      if(this.isLoading) return false;
      this.isLoading = true;
      let obj = {
          q: 'get_my_results',
          me: this.$store.state.ME.data,
          desc: true,
          count: 20,
      }
      if(this.items.length > 0) 
      obj.point = this.items[this.items.length-1].res_id;

      this.axios.post(window.c.API, obj).then( itm => {
        console.log(itm.data);
        if(itm.data?.data){
          if(itm.data.data.length > 0){
            let leng = itm.data?.data.length;
            for(let i = 0; i < leng; i++){
              this.items.push(itm.data?.data[i]);
            }
            if(leng < obj.count) this.isButtonLoad = false;
          }else{
            this.isButtonLoad = false;
          }
        }
        this.isLoading = false;
      });
    }
  },
  mounted(){
    setTimeout( ()=>{ this.Load()}, 0);
  }
}
</script>
