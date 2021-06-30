<template>
  <div>
      <!-- Создание группы -->
      <it-modal v-model="isWindowCreateGroup">
        <template #header
          ><h3 class="modal-header">Создание группы</h3></template
        >
        <template #body>
          <it-input
            v-model="newGroup.name"
            placeholder="Название группы"
          /><br />
          <it-textarea
            v-model="newGroup.description"
            placeholder="Описание группы"
          />
        </template>
        <template #actions>
          <it-button @click="isWindowCreateGroup = false">Отмена</it-button>
          <it-button @click="CreateGroup" type="primary">Создать</it-button>
        </template>
      </it-modal>
      <!-- /Создание группы -->
      <!-- Вступление в группу -->
      <it-modal v-model="isWindowJoinGroup">
        <template #header
          ><h3 class="modal-header">Вступить в группу</h3></template
        >
        <template #body>
          <it-input v-model="joinCode" placeholder="Код вступления" /><br />
        </template>
        <template #actions>
          <it-button @click="isWindowJoinGroup = false">Отмена</it-button>
          <it-button :loading="isJoinLoading" @click="JoinGroup" type="primary">Вступить</it-button>
        </template>
      </it-modal>
      <!-- /Вступление в группу -->
    <block>
      <h3>Группы:</h3>
      <it-button-group>
      <it-button icon="add" @click="isWindowCreateGroup = true">Создать группу</it-button>
      <it-button icon="login" @click="isWindowJoinGroup = true">Вступить в группу</it-button>
      </it-button-group>
      <br>
      <it-toggle v-model="tabGroup" :options="['В составе', 'Управляемые']" />
        <br>
        <GroupsList :key="tabGroup" :tab="tabGroup" />
        <!-- <GroupCard :data="item" />
        <Divider /> -->
    </block>
  </div>
</template>

<script>
import GroupsList from "../../components/Lists/GroupsList.vue";


export default {
  components: {
    GroupsList,
  },
  data() {
    return {
      items: [],
      tabGroup: 'В составе',
      isWindowCreateGroup: false,
      isWindowJoinGroup: false,
      isJoinLoading: false,
      joinCode: "",
      newGroup: {
        name: "",
        description: "",
      },
    };
  },
  methods: {
    CreateGroup() {
      if (this.newGroup.name.trim().length > 1) {
        let obj = {
          q: "create_group",
          me: this.$store.state.ME.data,
          name: this.newGroup.name,
          description: this.newGroup.description,
        };
        this.axios.post(window.c.API, obj).then((itm) => {
          console.log(itm.data);
          if (itm.data?.data) {
            this.$router.push(`/group/${itm.data.data}`);
          }
        });
      }
    },

    JoinGroup() {
      if(this.isJoinLoading) return;
      this.isJoinLoading = true;
      let obj = {
        q: "join_group",
        me: this.$store.state.ME.data,
        join_code: this.joinCode,
      };
      this.axios.post(window.c.API, obj).then((itm) => {
        this.isJoinLoading = false;
        this.joinCode = "";
        console.log(itm.data);
        if (itm.data?.data) {
          this.isWindowJoinGroup = false;
          switch (itm.data.data) {
            case 'ok':
              this.$Notification.success({
            title: itm.data?.message,
            placement: 'bottom-right',
            text: "Ожидайте, пока куратор примит вашу заявку",
          });
              break;
            case 'ok2':
              this.$Notification.warning({
            title: itm.data?.message,
            placement: 'bottom-right',
           
          });
              break;
          }
        }
      });
    },
  },
};
</script>

<style>

.modal-header {
  margin: 3px;
}
</style>