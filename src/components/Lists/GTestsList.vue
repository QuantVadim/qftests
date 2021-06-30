<template>
  <div>
    <GResultsViewer
      v-if="isWinResults"
      :data="curDataResults"
      @set-enable="WinShowResults"
    />
    <it-button
      class="btn-update"
      icon="update"
      :class="{ 'opacity-none': isLoading }"
      @click="updateList"
      text
      >Обновить</it-button
    >
    <div v-for="(item, index) in items" :key="index">
      <GTest :data="item" @show-results="showResults" :index="index" @edit-gtest="editGtest" @delete="onDeleteGTest" />
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
    <Sidebar v-model:visible="isWinEditorGTest" position="bottom" class="my-win"> 
        <div class="sidebar-conent-center">
          <block>
            <EditorGTest :data="CurGTestData" @on-edit="OnEdit" @on-close="closeEditorGTest" :index="curEditingGTest" />
          </block>
        </div>
    </Sidebar>
    <!-- Окно удаления Теста -->
    <it-modal v-model="isWinDelGTest">
      <template #header>
        <h3>Удаление теста из группы</h3>
      </template>
      <template #body>
        <p>
          Вы уверены, что хотите удалить тест <b>{{ CurGTestData?.name }}</b> из группы?
          <br>В этом случае вы потеряете доступ к решениям.
        </p>
      </template>
      <template #actions>
        <it-button @click="this.isWinDelGTest = false">Отмена</it-button>
        <it-button :loading="isDeletingGTest" @click="DeleteGTest" type="danger">Удалить</it-button>
      </template>
    </it-modal>
    <!--END Окно удаления Теста -->
  </div>
</template>

<script>
import GTest from "../Items/GTest.vue";
import Divider from "primevue/divider";
import GResultsViewer from "./GResultsViewer.vue";
import Sidebar from 'primevue/sidebar';
import EditorGTest from "../Menus/EditorGTest.vue";

export default {
  components: {
    GTest,
    Divider,
    GResultsViewer,
    EditorGTest,
    Sidebar
  },
  props: ["data"],
  data() {
    return {
      items: [],
      isLoading: false,
      isButtonLoad: true,

      isWinResults: false,
      curDataResults: null,

      isWinEditorGTest: false,
      CurGTestData: undefined,
      curEditingGTest: undefined,

      isWinDelGTest: false,
      isDeletingGTest: false,
    };
  },
  mounted() {
    if (this?.data) {
      this.items = this.data;
      if (this.items.length <= 30) {
        this.isButtonLoad = false;
      }
    } else {
      setTimeout(() => {
        this.Load();
      }, 0);
    }
  },
  methods: {
    onDeleteGTest(data, index){
      this.CurGTestData = data;
      this.curEditingGTest = index;
      this.isWinDelGTest = true;
    },
    DeleteGTest() {
      if (this.isDeletingGTest) return false;
      this.isDeletingGTest = true;
      let obj = {
        q: "delete_gtest",
        me: this.$store.state.ME.data,
        gt_id: this.CurGTestData.gt_id,
      };
      this.axios.post(window.c.API, obj).then((itm) => {
        this.isDeletingGTest = false;
        this.isWinDelGTest = false;
        console.log(itm.data);
        if (itm.data?.data){
          this.$Notification.success({
            title: "Тест удален",
            text: `Тест был успешно удален из группы`,
            placement: 'bottom-right',
          });
          this.items.splice(this.curEditingGTest, 1);
        }else if(itm.data?.error){
          this.$Notification.danger({
            title: "Ошибка удаления",
            text: itm.data?.error,
            placement: 'bottom-right',
          });
        }
      });
    },
    OnEdit(data, index){
      this.items[index] = data;
    },
    editGtest(data, index){
      this.CurGTestData = data;
      this.curEditingGTest = index;
      this.isWinEditorGTest = true;
      console.log(data);
    },
    closeEditorGTest(){
      this.isWinEditorGTest = false;
    },
    showResults(data) {
      this.isWinResults = true;
      this.curDataResults = data;
      console.log(data);
    },
    WinShowResults(show) {
      this.isWinResults = show;
    },
    updateList() {
      this.isButtonLoad = true;
      this.items = [];
      this.Load();
    },
    async Load() {
      if (this.isLoading) return false;
      this.isLoading = true;
      let obj = {
        q: "get_group_tests",
        me: this.$store.state.ME.data,
        gr_id: this.$route.params.id,
        desc: true,
        count: 30,
      };
      if (this.items.length > 0)
        obj.point = this.items[this.items.length - 1].gt_id;

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
          this.$emit("cash", this.items);
          console.log(this.items);
        }
        this.isLoading = false;
      });
    },
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