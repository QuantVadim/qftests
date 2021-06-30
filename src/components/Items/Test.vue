<template>
  <div>
    <div class="card_test_top_header">
      <div class="card-autor-name">
        {{ date_created }}
      </div>
      <div>
        <div v-if="data.usr_id == $store.state.ME.data.usr_id">
          <it-icon name="more_horiz" @click="toggle" />
         <Menu ref="menu" :model="itemsMenu" :popup="true" />
        </div>
      </div>
    </div>
    <div class="itm-test-main-info">
      <it-avatar square size="45px" :text="data.name" />
      <h3 class="card-test-title">
        <span @click="goTest">{{ data.name }}</span>
      </h3>
      <span v-if="data?.description?.length" class="card-description">
        <pre>{{ data.description }}</pre>
      </span>
    </div>
  </div>
</template>

<script>
import Menu from 'primevue/menu';
export default {
  components:{
    Menu
  },
  props: ["data", 'index'],
  data() {
    return {
      itemsMenu: [
        {
					label: 'Отправить',
					icon: 'pi pi-reply',
					command: () => {
						this.$emit('share-test', this.data);
					}
				},
        {
					label: 'Редактировать',
					icon: 'pi pi-pencil',
					command: () => {
						this.goEditor();
					}
				},
        {
					label: 'Удалить',
					icon: 'pi pi-times',
					command: () => {
						this.$emit('delete', this.data, this?.index);
					}
				},
      ],
    };
  },
  computed: {
    date_created() {
      if (this?.data?.date_created) {
        return this.data.date_created;
      } else return "";
    },
  },
  methods: {
    toggle(event) {
    this.$refs.menu.toggle(event);
    },
    goTest() {
      this.$router.push(`/test/${this.data.test_id}`);
    },
    goEditor() {
      this.$router.push(`/test/${this.data.test_id}/editor`);
    },
  },
};
</script>
<style scoped>
.card-test-title span:hover {
  cursor: pointer;
  text-decoration: underline;
  text-underline-offset: 2px;
}
.it-avatar {
  float: left;
  margin-right: 6px;
}
.itm-test-main-info{
  min-height: 45px;
}

.it-icon {
  cursor: pointer;
}

.card_test_top_header {
  display: grid;
  grid-template-columns: 1fr auto;
}
pre {
  margin: 0px;
}
h3 {
  margin: 0px;
}
.card-autor-name {
  color: gray;
}
.card-description {
  max-height: 300px;
  overflow-y: auto;
}
</style>