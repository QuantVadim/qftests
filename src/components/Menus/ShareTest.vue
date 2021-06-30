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
    <it-toggle
      v-model="curTab"
      :options="['Группы', 'Параметры']"
    /><br>
    <SelectGroups v-if="curTab=='Группы'" :data="cashGroups" class="enter-show" @change="onSelectedItems" @cash="toCashGroups" />
    <TestPublishingSettings v-if="curTab=='Параметры'" :data="cashSettings" class="enter-show" @cash="toCashSettings" />
    <br>
    <it-button @click="shareTests" :loading="sharingTest" size="big" block :disabled="!isBtnShare">Отправить</it-button>
  </div>
</template>

<script>
import SelectGroups from "./SelectGroups.vue";
import TestPublishingSettings from "./TestPublishingSettings.vue";

export default {
  components: {
    SelectGroups,
    TestPublishingSettings,
  },
  props: ["data"],
  data() {
    return {
      curTab: 'Группы',
      comment: '',
      sharingTest: false,
      cashGroups: undefined,
      cashSettings: undefined, 
      isBtnShare: false,
      selectedGroups: [],
    };
  },
  watch:{
    selectedGroups(){
      this.isBtnShare = this.selectedGroups.length > 0;
    }
  },
  mounted(){
    console.log(this.data);
  },
  methods:{
    onSelectedItems(arr){
      this.selectedGroups = arr;
    },
    toCashSettings(data){
      this.cashSettings = data;
    },
    toCashGroups(data){
      this.cashGroups = data;
    },
    shareTests(){
      if(this.selectedGroups.length == 0 || this.sharingTest) return false;
      this.sharingTest = true;
      let obj = {
        q: 'share_tests',
        me: this.$store.state.ME.data,
        groups: this.selectedGroups,
        test_id: this.data.test_id,
        settings: this.cashSettings,
        comment: this.comment,
      }
      console.log(obj);
      this.axios.post(window.c.API, obj).then(itm=>{
        this.sharingTest = false;
        if(itm.data?.data){
          this.$Notification.success({
            title: 'Тест опубликован',
            placement: 'bottom-right',
            text: "Вы успешно опубликовали тест в выбранных группах",
          });
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
