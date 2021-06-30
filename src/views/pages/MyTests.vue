<template>
  <div>
      <Sidebar v-model:visible="isShareWindow" position="bottom" class="my-win"> 
        <div class="sidebar-conent-center">
          <block>
            <ShareTest :data="CurTestData" @on-close="closeShareTest" />
          </block>
        </div>
      </Sidebar>
    <block>
      <h3>Мои тесты:</h3>
      <Divider />
      <div v-for="(item, index) in items" :key="index">
        <TestCard :data="item" @share-test="ShareTest" :index="index" @delete="onDeleteTest" />
        <Divider />
      </div>
      <div
        v-if="!isLoading && !isButtonLoad && items.length == 0"
        class="center gray"
      >
        Здесь ничего нет
      </div>
      <it-button @click="Load" :loading="isLoading" v-if="isButtonLoad" block
        >Еще</it-button
      >
    </block>
    <!-- Окно удаления Теста -->
    <it-modal v-model="isWinDeleteTest">
      <template #header>
        <h3>Удаление теста</h3>
      </template>
      <template #body>
        <p>
          Вы уверены, что хотите удалить тест <b>{{ CurTestData?.name }}</b>?
          В этом случае он исчезнет из групп, в которых был опубликован.<br><br>
          <b>Возможности восстановить не будет!</b>
        </p>
      </template>
      <template #actions>
        <it-button @click="isWinDeleteTest = false">Отмена</it-button>
        <it-button :loading="isDeletingTest" @click="DeleteTest" type="danger">Удалить</it-button>
      </template>
    </it-modal>
    <!--END Окно удаления Теста -->
  </div>
</template>

<script>
import Divider from "primevue/divider";
import TestCard from "../../components/Items/Test";
import Sidebar from 'primevue/sidebar';
import ShareTest from "../../components/Menus/ShareTest.vue";

export default {
  components: {
    Divider,
    TestCard,
    ShareTest, 
    Sidebar
  },
  data() {
    return {
      items: [],
      isLoading: false,
      isButtonLoad: true,

      isShareWindow: false,
      CurTestData: undefined,
      CurTestIndex: undefined,

      isWinDeleteTest: false,
      isDeletingTest: false,
    };
  },
  methods: {
    onDeleteTest(data, index){
      this.CurTestData = data;
      this.CurTestIndex = index;
      this.isWinDeleteTest = true;
    },
    DeleteTest(){
      if (this.isDeletingTest) return false;
      this.isDeletingTest = true;
      let obj = {
        q: "delete_test",
        me: this.$store.state.ME.data,
        test_id: this.CurTestData.test_id,
      };
      this.axios.post(window.c.API, obj).then((itm) => {
        this.isDeletingTest = false;
        this.isWinDeleteTest = false;
        console.log(itm.data);
        if (itm.data?.data){
          this.$Notification.success({
            title: "Тест удален",
            text: `Тест был успешно удален`,
            placement: 'bottom-right',
          });
          this.items.splice(this.CurTestIndex, 1);
        }else if(itm.data?.error){
          this.$Notification.danger({
            title: "Ошибка удаления",
            text: itm.data?.error,
            placement: 'bottom-right',
          });
        }
      });
    },
    closeShareTest(){
      this.isShareWindow = false
    },
    ShareTest(data){
      this.isShareWindow = true;
      this.CurTestData = data;
    },
    async Load() {
      if (this.isLoading) return false;
      this.isLoading = true;
      let obj = {
        q: "get_my_tests",
        me: this.$store.state.ME.data,
        desc: true,
        count: 20,
      };
      if (this.items.length > 0)
        obj.point = this.items[this.items.length - 1].test_id;

      this.axios.post(window.c.API, obj).then((itm) => {
        if (itm.data?.data) {
          if (itm.data.data.length > 0) {
            let leng = itm.data?.data.length;
            for (let i = 0; i < leng; i++) {
              this.items.push(itm.data?.data[i]);
            }
            if (leng < obj.count) this.isButtonLoad = false;
          } else {
            this.isButtonLoad = false;
          }
        }
        this.isLoading = false;
      });
    },
  },
  mounted() {
    setTimeout(() => {
      this.Load();
    }, 0);
  },
};
</script>

<style>
.p-sidebar-bottom.my-win{
  height: 80% !important;
  background-color: rgb(230, 230, 230);
}
.my-win .p-sidebar-content{
  padding: 0px;
}
.my-win .p-sidebar-header{
  padding-top: 6px;
}
.sidebar-conent-center{
  max-width: 400px;
  display: block;
  margin: auto;
}
</style>
