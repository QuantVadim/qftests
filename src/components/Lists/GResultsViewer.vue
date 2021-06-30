<template>
  <div>
    <!--Окно просмотра результатов -->
    <it-modal v-model="isOpen">
      <template #header
        ><h3 class="modal-header">{{ data.name }}</h3></template
      >
      <template #body>
        <it-button
          class="btn-update"
          icon="update"
          :class="{ 'opacity-none': isLoading }"
          @click="updateList"
          text
          >Обновить</it-button
        >
        <div v-for="(item, index) in items" :key="index">
          <ResultCard :data="item" @delete="toDeleteResult" :index="index" />
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
      </template>
      <template #actions>
        <it-button @click="setEnable(false)">Закрыть</it-button>
        <!-- <it-button :loading="isWaitingUserActionLoading" @click="saveUserName" type="primary"
          >Сохранить</it-button> -->
      </template>
    </it-modal>
    <!-- /Окно просмотра результатов -->
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
        <it-button @click="this.isWinDelResult = false">Отмена</it-button>
        <it-button :loading="isDeletingResult" @click="DeleteResult" type="danger">Удалить</it-button>
      </template>
    </it-modal>
  </div>
</template>

<script>
import Divider from "primevue/divider";
import ResultCard from "../Items/GResult.vue";

export default {
  components: {
    Divider,
    ResultCard,
  },
  props: ["data"],
  data() {
    return {
      items: [],
      isLoading: false,
      isButtonLoad: true,
      isOpen: true,

      isWinDelResult: false,
      curDelResult: undefined,
      curDelResultIndex: undefined,
      isDeletingResult: false,
    };
  },
  mounted() {
    setTimeout(() => {
      this.Load();
    }, 0);
  },
  methods: {
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
    setEnable(enable) {
      this.$emit("set-enable", enable);
    },
    updateList() {
      this.items = [];
      this.Load();
    },
    async Load() {
      if (this.isLoading) return false;
      this.isLoading = true;
      let obj = {
        q: "get_group_results",
        me: this.$store.state.ME.data,
        gr_id: this.$route.params.id,
        desc: true,
        count: 30,
      };
      if (this.data?.gt_id) {
        obj.gt_id = this.data.gt_id;
      }
      if (this.items.length > 0)
        obj.point = this.items[this.items.length - 1].res_id;
      this.axios.post(window.c.API, obj).then((itm) => {
        console.log(itm.data);
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
  watch: {
    isOpen() {
      this.setEnable(this.isOpen);
    },
  },
};
</script>