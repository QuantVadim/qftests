<template>
  <div>
    <!--Окно редактирования имени пользователя -->
    <it-modal v-model="isWindowEditUserName">
      <template #header
        ><h3 class="modal-header">Изменить имя пользователя</h3></template
      >
      <template #body>
        <it-input v-model="curUserName" placeholder="Новое имя пользователя" /><br />
      </template>
      <template #actions>
        <it-button @click="isWindowEditUserName = false">Отмена</it-button>
        <it-button :loading="isWaitingUserActionLoading" @click="saveUserName" type="primary"
          >Сохранить</it-button>
      </template>
    </it-modal>
    <!-- /Окно редактирования имени пользователя -->
    <!--Окно Удаления Польователя -->
    <it-modal v-model="isWindowDeleteUser">
      <template #header>
        <h3 class="modal-header">Удаление пользователя</h3>
        </template>
      <template #body>
        Вы уверены, что хотите удалить пользователя <b>{{curUserName}}</b> из группы ?
      </template>
      <template #actions>
        <it-button @click="isWindowDeleteUser = false">Отмена</it-button>
        <it-button :loading="isWaitingUserActionLoading" @click="deleteUser" type="danger"
          >Удалить</it-button>
      </template>
    </it-modal>
    <!-- /Окно Удаления Польователя -->
    <it-button class="btn-update" icon="update" :class="{'opacity-none':isLoading}" @click="updateList" text>Обновить</it-button>
    <div v-for="(item, index) of items" :key="index">
      <UserItem
        :data="items[index]"
        @update_item="updateItem"
        @edit="editUserName"
        @delete="deleteUserDialog"
        :index="index"
      />
      <it-divider class="user-divider" />
    </div>
    <it-button @click="Load" :loading="isLoading" v-if="isButtonLoad" block
      >Еще</it-button
    >
  </div>
</template>

<script>
import UserItem from "../Items/GroupUser.vue";
export default {
  components: {
    UserItem,
  },
  props: ["data", "gr_id", "accepted"],
  data() {
    return {
      curUserName: '',
      isWindowEditUserName: false,
      isWindowDeleteUser: false,
      curUserIndex: null,
      isLoading: false,
      isButtonLoad: true,
      isWaitingUserActionLoading: false,
      items: [],
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
    updateList(){
      this.items = [];
      this.Load();
    },
    deleteUserDialog(index){
      this.isWindowDeleteUser = true;
      this.curUserIndex = index;
      this.curUserName = this.items[index].name; 
    },
    deleteUser(){
      if(this.isWaitingUserActionLoading) return;
      this.isWaitingUserActionLoading = true;
      let obj = {
        q: 'join_request_action',
        me: this.$store.state.ME.data,
        action: 'delete',
        req_id: this.items[this.curUserIndex].req_id,
      }
      console.log(obj);
      this.axios.post(window.c.API, obj).then(itm=>{
        console.log(itm.data);
        if(itm.data?.data && itm.data?.deleted){
          this.items.splice(this.curUserIndex, 1);
          this.isWaitingUserActionLoading = false;
          this.isWindowDeleteUser = false;
          this.$emit("cash", this.items);
        }
      });
    },
    editUserName(index) {
      this.isWindowEditUserName = true;
      this.curUserName = this.items[index].name; 
      this.curUserIndex = index;
    },
    saveUserName(){
      if(this.isWaitingUserActionLoading) return;
      this.isWaitingUserActionLoading = true;
      let obj = {
        q: 'join_request_action',
        me: this.$store.state.ME.data,
        action: 'set_name',
        name: this.curUserName,
        req_id: this.items[this.curUserIndex].req_id,
      }
      console.log(obj);
      this.axios.post(window.c.API, obj).then(itm=>{
        console.log(itm.data);
        if(itm.data?.data){
          this.items[this.curUserIndex] = itm.data.data
          this.isWaitingUserActionLoading = false;
          this.isWindowEditUserName = false;
          this.$emit("cash", this.items);
        }
      });
    },
    updateItem(data, index) {
      console.log(data, index);
      this.items[index] = data;
      this.$emit("cash", this.items);
    },
    async Load() {
      if (this.isLoading) return false;
      this.isLoading = true;
      this.isButtonLoad = true;
      let obj = {
        q: "get_group_users",
        me: this.$store.state.ME.data,
        accepted: this.accepted,
        gr_id: this.gr_id,
        desc: true,
        count: 30,
      };
      if (this.items.length > 0)
        obj.point = this.items[this.items.length - 1].req_id;

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
        }
        this.isLoading = false;
      });
    },
  },
};
</script>

<style>
.opacity-none{
  opacity: 0 !important;
}
.btn-update{
  color: gray !important;
  padding-left: 3px;
  padding-right: 3px;
}
.user-divider.it-divider {
  margin: 4px 0px;
}
</style>