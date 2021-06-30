<template>
<div>
  <div v-if="dt != undefined">
  <div></div>
  <pre>{{ dt.text }}</pre>
  <pre class="it-input-wrapper" :class="{'it-input-wrapper--success': dt.score > 0}" placeholder="Ответ">{{ dt.answer }}</pre>
  </div>
  <div v-else>Пусто</div>
</div>
</template>

<script>

export default {
  props: ['data'],
  data(){
    return{
      dt: this.data,
    }
  },
  mounted(){
    console.log(this.dt);
  },
  methods:{

  }
}
</script>

<style scoped>
.it-input-wrapper{
  padding: 10px;
  padding-top: 7px;
  border-color: #f93155!important;
  box-shadow: 0 2px 4px rgb(249 49 85 / 40%), 0 0 0 3px rgb(249 49 85 / 20%);
}
.it-input-wrapper--success{
      border-color: #07d85b!important;
    box-shadow: 0 2px 4px rgb(7 216 91 / 40%), 0 0 0 3px rgb(7 216 91 / 20%);
}
</style>