<template>
  <div v-if="data">
    <div>
      <div class="itm-test-main-info">
        <it-avatar square size="45px" :text="data.name" />
        <h3 class="card-test-title">
          <span>{{ data.name }}</span>
        </h3>
        <span v-if="data?.description?.length" class="card-description">
          <pre>{{ data.description }}</pre>
        </span>
      </div>
    </div>
    <it-textarea class="it-share-test-comment" v-model="comment" :resize-on-write="true" :rows="1" placeholder="Комментарий"/>
    <TestPublishingSettings :data="cashSettings" class="enter-show" @cash="toCashSettings" />
    <br>
    <it-button @click="saveGTest" :loading="sharingTest" size="big" block >Сохранить</it-button>
  </div>
</template>

<script>
import TestPublishingSettings from "./TestPublishingSettings.vue";

export default {
  components: {
    TestPublishingSettings,
  },
  props: ["data", "index"],
  data() {
    return {
      comment: this.data.comment,
      sharingTest: false,
      cashSettings: {
        is_limit_attempts: this.data.attempts != null,
        limit_attempts: this.data.attempts ? this.data.attempts : 1,
      }, 
      isBtnShare: false,
    };
  },
  watch:{
  },
  mounted(){
    console.log(this.data);
  },
  methods:{
    toCashSettings(data){
      this.cashSettings = data;
    },
    saveGTest(){
      if(this.sharingTest) return false;
      this.sharingTest = true;
      let obj = {
        q: 'edit_gtest',
        me: this.$store.state.ME.data,
        gt_id: this.data.gt_id,
        gr_id: this.$route.params.id,
        settings: this.cashSettings,
        comment: this.comment,
      }
      console.log(obj);
      this.axios.post(window.c.API, obj).then(itm=>{
        this.sharingTest = false;
        console.log(itm.data);
        if(itm.data?.data){
          this.$Notification.success({
            title: 'Тест изменен',
            placement: 'bottom-right',
            text: "Вы успешно изменили тест",
          });
          this.$emit('on-edit', itm.data.data, this?.index);
          this.$emit('on-close');
        }
      });
    }

  }
};
</script>

<style scoped>
.it-share-test-comment{
  margin: 8px 0px;
}
.it-avatar {
  float: left;
  margin-right: 6px;
}
.itm-test-main-info {
  min-height: 45px;
}

pre {
  margin: 0px;
}
h3 {
  margin: 0px;
}
.card-description {
  max-height: 300px;
  overflow-y: auto;
}
</style>
