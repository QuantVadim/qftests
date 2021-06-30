<template>
  <div>
    <div class="card_test_top_header">
      <div>
        
        <div v-if="data?.group_name" class="user-item-row" @click="goGroup">
          <it-avatar :src="data?.avatar"/>
          <div>
            <div class="card-autor-name">{{ date_created }}</div>
            <div class="itm-group-name">{{data?.group_name}}</div>
            </div>
        </div>
      </div>
      <div>
        <div v-if="data.usr_id == $store.state.ME.data.usr_id">
          <it-icon name="more_horiz" @click="toggle" />
         <Menu ref="menu" :model="itemsMenu" :popup="true" />
        </div>
      </div>
    </div>
    <pre>{{ data.comment }}</pre>
    <div class="item-gtest-body" @click="goTest">
      <div v-if="data?.name">
        <div class="itm-gtest-header">
         <it-avatar square  size="45px"  :text="data.name" />
         <h3 class="card-test-title">
          <span>{{ data.name }}</span>
          </h3>
          <span v-if="data?.description?.length" class="card-description">
          <pre>{{ data.description }}</pre>
          </span>
        </div>
        
        
      </div>
      <div v-else class="gray">
        Тест был удален
      </div>
    </div>
    <div 
    class="gtest-count-responses"
    @click="$emit('show-results', data)"
    >Решено: {{data.count_responses}}</div>
  </div>
</template>

<script>
import Menu from 'primevue/menu';
export default {
  components:{
    Menu
  },
  props: ["data", "index"],
  data() {
    return {
      itemsMenu: [
        {
					label: 'Редактировать запись',
					icon: 'pi pi-pencil',
					command: () => {
            this.$emit('edit-gtest', this.data, this?.index);
					}
				},
        {
					label: 'Редактировать тест',
					icon: 'pi pi-file-o',
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
      if(this.data?.name){
        this.$router.push(`/gtest/${this.data.gt_id}`);
      }
    },
    goEditor() {
      this.$router.push(`/test/${this.data.ref_test_id}/editor`);
    },
    goGroup(){
      this.$router.push(`/group/${this.data.gr_id}`);
    }
  },
};
</script>
<style scoped>
.itm-group-name{
  font-weight: bold;
}
.user-item-row{
  display: grid;
  grid-template-columns: 45px 1fr;
  align-items: center;
  margin-bottom: 6px;
}
.itm-gtest-header{
  position: relative;
}
.itm-gtest-header .it-avatar{
  float: left;
  margin-right: 6px;
  margin-bottom: 6px;
}

.itm-gtest-header h3{
  margin-left: 4px;
  vertical-align: middle;
  
}
.gtest-count-responses{
  color: gray;
  margin-top: 6px;
  font-size: 12px;
}
.gtest-count-responses:hover{
  cursor: pointer;
  text-decoration: underline;
}
.item-gtest-body:hover{
  transform: translate(2px, -2px);
   box-shadow: -2px 6px 8px rgba(0, 0, 0, 0.37);
}

.item-gtest-body {
  cursor: pointer;
  min-height: 62px;
  margin-top: 6px;
  background-color: rgb(250, 250, 250);
  border-left: 1px solid rgb(218, 218, 218);
  border-top: 1px solid rgb(218, 218, 218);
  box-shadow: -1px 3px 3px rgba(0, 0, 0, 0.37);
  padding: 5px;
  border-radius: 5px;
  transition: all 0.2s;
}

.it-icon {
  cursor: pointer;
}
.user-item-row:hover{
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