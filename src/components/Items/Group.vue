<template>
  <div>
    <h3 @click="goGroup" class="group-card-header">{{ data.name }}</h3>
    <div class="group-card-count-users" >Участники: {{ data.count_users }}</div>
  </div>
</template>

<script>

export default {
  props: ['data'],
  data(){
    return{

    }
  },
  methods:{
    goGroup(){
      this.$router.push(`/group/${this.data.gr_id}`);
    }
  }
}
</script>

<style>
.group-card-header{
  margin: 3px;
}
.group-card-count-users{
  color: gray;
  font-size: 13px;
}
h3.group-card-header:hover{
  cursor: pointer;
  text-decoration: underline;
}
</style>