<template>
  <div>
    <div class="card_test_top_header">
      <div class="card-autor-name">{{ date_created }}</div>
      <div>
        <div v-if="data.usr_id == $store.state.ME.data.usr_id">
        <it-icon name="more_horiz" @click="toggle" />
        <Menu ref="menu" :model="itemsMenu" :popup="true" />
        </div>
      </div>
    </div>
    <div class="card-result-body">
      
      <div class="itm-result-center">
        <it-avatar square  size="50px"  :text="data.name" />
        
        <h3 class="card-test-title"><span @click="goResult">{{ data.name }}</span></h3>
        <span v-if="data?.description?.length" class="card-description">
          <pre>{{ data.description}}</pre>
        </span>
      </div>
      <div>
        <Knob v-model="score" :min="0" :max="100" :size="50" :valueTemplate="score+'%'" readonly />
         <div class="result-card-score">{{ data.score+"/"+data.max_score }}</div>
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
					icon: 'pi pi-caret-right',
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
      if(this.data?.first_name){
        return this.data?.first_name+' '+this.data?.last_name;
      }else
      return this.data?.user_name;
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
      if(this.data.gr_id != null){
        this.$router.push(`/gtest/${this.data.ref_test_id}`);
      }else{
        this.$router.push(`/test/${this.data.ref_test_id}`);
      }
      
    },
    goResult(){
      this.$router.push(`/result/${this.data.res_id}`);
    }
  }

}
</script>
<style scoped>
.p-knob.p-component{
  height: 45px;
  display: flex;
  justify-content: space-around;
}


.result-card-score{
  text-align: center;
  color: gray;
  font-size: 14px;
}
.itm-result-center{
  margin-left: 0px;
}
.card-result-body{
  display: grid;
  grid-template-columns: 1fr auto;
}
.card-test-title span:hover{
  cursor: pointer;
  text-decoration: underline;
  text-underline-offset: 2px;
}

.it-avatar{
  float: left;
  margin-right: 8px;
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
.card-description pre{
  color: gray;
  margin-right: 6px;
  border-radius: 8px;
  padding-top: 3px;

  max-height: 300px;
  overflow-y: auto;
  font-size: 12px !important;
  max-height: 84px;
  overflow: hidden;
}
</style>