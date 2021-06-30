<template>
  <div>
    <div class="card_test_top_header">
      <div></div>
      <div>
        <div>
          <it-icon name="more_horiz" @click="toggle" />
          <Menu ref="menu" :model="itemsMenu" :popup="true" />
        </div>
      </div>
    </div>
    <div class="card-result-body">
      <it-avatar :size="'50px'" :src="data?.avatar" />
      <div class="itm-gresult-center">
        <div class="card-autor-name">{{ date_created }}</div>
        <h3 class="card-test-title"><span @click="goResult">{{ user_name }}</span></h3>
        <div class="result-card-score">{{ data.score+" из "+data.max_score }}</div>
      </div>
      <div>
        <Knob v-model="score" :min="0" :max="100" :size="50" :valueTemplate="score+'%'" readonly />
      </div>
    </div>
  </div>
</template>

<script>
import Knob from 'primevue/knob';
import Menu from 'primevue/menu';

export default {
  components:{
    Knob, Menu
  },
  props: ['data', 'index'],
  data(){
    return{
      score: Math.floor((+this.data.score/+this.data.max_score)*100),
      itemsMenu: [
        {
					label: 'К тесту',
					icon: 'pi pi-external-link',
					command: () => {
						this.goTest();
					}
				},
        {
					label: 'Удалить',
					icon: 'pi pi-times',
					command: () => {
						this.$emit('delete', this.data, this?.index );
					}
				},
      ],
    }
  },
  computed: {
    user_name(){
      if(this.data?.user_name){
        return this.data?.user_name;
      }else
      return this.data?.first_name+' '+this.data?.last_name;
      
    },
    date_created(){
      if(this?.data?.date_created){
      return this.data.date_created;
      }else return "";
    }
  },
  methods:{
    toggle(event) {
      this.$refs.menu.toggle(event);
    },
    goTest(){
      window.open(`/gtest/${this.data.ref_test_id}`, '_blank');
    },
    goResult(){
      this.$router.push({query: {result: this.data.res_id}});
      //this.$emit('open-result', this.data.res_id);
      //window.open(`/result/${this.data.res_id}`, '_blank');
    }
  }

}
</script>
<style>
.p-menu.p-component.p-menu-overlay{
  z-index: 99999 !important; 
}
</style>
<style scoped>

.itm-gresult-center{
  margin-left: 6px;
}
.result-card-score{
  text-align: left;
  color: gray;
}
.card-result-body{
  display: grid;
  grid-template-columns: auto 1fr auto;
}
.card-test-title span:hover{
  cursor: pointer;
  text-decoration: underline;
  text-underline-offset: 2px;
}

.it-icon{
  cursor: pointer;
}

.card_test_top_header{
  display: grid;
  grid-template-columns: 1fr auto;
}
pre{
  margin: 0px
}
h3{
  margin: 0px;
}
.card-autor-name{
  color: gray;
}
.card-description{
  max-height: 300px;
  overflow-y: auto;
}
</style>