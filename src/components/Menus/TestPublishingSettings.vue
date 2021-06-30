<template>
  <div class="test-settings">
    <it-switch v-model="stg.is_limit_attempts" label="Ограничить попытки" @change="toCash" >
      <template #sublabel>Ограничив попытки, пользоватили смогут пройти тест только то кол-во раз, которое вы укажете</template>
    </it-switch>
    <div class="test-share-sttg-two-columns" v-if="stg.is_limit_attempts">
      <it-number-input @change="toCash" :min="0" :max="100" :resize-on-write="true" v-model="stg.limit_attempts"/>
    </div> 
  </div>
</template>

<script>
export default {
  
  props:['data'],
  beforeMount(){
    if(this?.data){
      this.stg = this?.data; 
    }
  },
  data(){
    return{
      stg:{
        is_limit_attempts: false,
        limit_attempts: 1,
      }
    }
  },
  methods:{
    toCash(){
      this.$emit('cash', this.stg);
    }
  },
  watch:{
    stg(){
      this.toCash(this.stg);
    }
  }
}
</script>

<style>
.test-share-sttg-two-columns{
padding-left: 40px;

}
.test-share-sttg-two-columns div:first-child{
  padding-right: 10px;
}
.test-settings .it-switch-label:first-child{
  font-weight: bold !important;
}
</style>