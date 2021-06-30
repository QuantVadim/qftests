<template>
<div>
  <div v-for="(item, index) in items" :key="index">
    <GroupCard :data="item" />
    <Divider />
  </div>
  <div v-if="!isLoading && !isButtonLoad && items.length == 0" class="center gray">Здесь ничего нет</div>
  <it-button @click="Load" :loading="isLoading" v-if="isButtonLoad" block
        >Еще</it-button>
  </div>
</template>

<script>
import Divider from "primevue/divider";
import GroupCard from "../Items/Group.vue";
export default {
  components:{
    Divider, GroupCard
  },
  props: ['tab'],
  data() {
    return {
      items: [],
      isLoading: false,
      isButtonLoad: true,
    };
  },
  methods:{
    async Load() {
      if (this.isLoading) return false;
      this.isLoading = true;
      let obj = {
        q: "get_my_groups",
        me: this.$store.state.ME.data,
        type: this.tab == "Управляемые"? 'my' : 'in',
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
  mounted(){
    setTimeout(() => {
      this.Load();
    }, 0);
  }
}
</script>