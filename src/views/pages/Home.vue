<template>
  <div class="home">
    <block>
      <h3>Главная</h3>

      <it-button
      class="btn-update"
      icon="update"
      :class="{ 'opacity-none': isLoading }"
      @click="updateList"
      text
      >Обновить</it-button
    >
    <div v-for="(item, index) in items" :key="index">
      <GTest :data="item" />
      <Divider />
    </div>
    <div
      v-if="!isLoading && !isButtonLoad && items.length == 0"
      class="center gray"
    >
      Здесь ничего нет
    </div>
    <it-button @click="Load" :loading="isLoading" v-if="isButtonLoad" block
      >Еще</it-button>
    </block>
    
  </div>
</template>

<script>
import GTest from "../../components/Items/GTest.vue";
import Divider from "primevue/divider";

export default {
  name: 'Home',
  components: {
    GTest,
    Divider,
  },
  props: ["data"],
  data() {
    return {
      items: [],
      isLoading: false,
      isButtonLoad: true,
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

    updateList() {
      this.isButtonLoad = true;
      this.items = [];
      this.Load();
    },
    async Load() {
      if (this.isLoading) return false;
      this.isLoading = true;
      let obj = {
        q: "get_all_groups_tests",
        me: this.$store.state.ME.data,
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
.btn-update {
    color: gray !important;
    padding-left: 3px !important;
    padding-right: 3px !important;
}

</style>