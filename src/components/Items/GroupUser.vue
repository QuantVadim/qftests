<template>
  <div>
    <div class="user-item-name-row">
      <it-avatar :src="data?.avatar" />
      <div class="user-name">
        <span>{{ userName }}</span>
        <span v-if="data.req_id"
          ><SplitButton
            v-if="data?.accepted != undefined"
            :label="data?.accepted == 0 ? 'Принять' : 'Отклонить'"
            :model="ReqActions"
            @click="acceptUser"
            :class="{'p-button-gray': this.data.accepted == 1}"
            class="p-button-secondary"
        /></span>
      </div>
    </div>
  </div>
</template>

<script>
import SplitButton from "primevue/splitbutton";
export default {
  components: {
    SplitButton,
  },
  props: ["data", "index"],
  data() {
    return {
      isWaitingAction: false,
      ReqActions: [
        {
          label: "Изменить",
          icon: "pi pi-pencil",
          command: () => {
            if(this.isWaitingAction) return;
            this.$emit('edit', this.index);
          },
        },
        {
          label: "Удалить",
          icon: "pi pi-times",
          command: () => {
            if(this.isWaitingAction) return;
            this.$emit('delete', this.index);
          },
        },
        
      ],
    };
  },
  created(){},
  computed: {
    userName() {
      return this.data?.name
        ? this.data.name
        : this.data.first_name + this.data.last_name;
    },
  },
  methods:{
    acceptUser(){
      if(this.isWaitingAction) return;
      this.isWaitingAction = true;
      let obj = {
        q: 'join_request_action',
        me: this.$store.state.ME.data,
        action: 'set_accepted',
        accepted: this.data.accepted == false,
        req_id: this.data.req_id,
      }
      console.log(obj);
      this.axios.post(window.c.API, obj).then(itm=>{
        console.log(itm.data);
        if(itm.data?.data){
          this.$emit('update_item', itm.data.data, this?.index);
          this.isWaitingAction = false;
        }
      });
    }
  }
};
</script>

<style>

.p-button-gray.p-splitbutton button{
  background-color: #F1F0F5 !important;
  color: rgb(124, 124, 124) !important;
  border-color: rgb(204, 204, 204) !important;
}
.user-item-name-row {
  display: grid;
  grid-template-columns: auto 1fr;
}

.user-item-name-row .user-name {
  display: grid;
  grid-template-columns: 1fr auto;
  align-items: center;
  margin: 3px 0px 3px 6px;
}
</style>