<template>
  <div class="navigator">
    <it-button-group vertical>
          <it-button block @click="goRoute('/')">Главная</it-button>
          <it-button block @click="goRoute('/mytests')">Мои тесты</it-button>
          <it-button block @click="goRoute('/myresults')">Мои решения</it-button>
          <it-button block @click="goRoute('/groups')">Группы</it-button>
          <it-button block @click="goRoute('/test/new/editor')">Создать тест</it-button>
        </it-button-group>
  </div>
</template>

<script>
export default {
  methods:{
    goRoute(route) {
      this.$router.push(route);
      this.$emit('on-clicked');
    },
  }
}
</script>

<style>
.navigator
.it-btn-text {
    height: 20px !important;
    font-size: 14px !important;
    line-height: 24px !important;
}
</style>