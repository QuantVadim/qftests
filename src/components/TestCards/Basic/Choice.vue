<template>
  <div>
    <div v-if="dt != undefined">
      <div></div>
      <pre>{{dt.text}}</pre>
      <div class="choices-body">
        <div v-for="(item, index) in dt.choices" :key="index" class="choice-line">
          <it-checkbox v-if="dt.isMultiple" v-model="dt.choices[index].selected">{{ dt.choices[index].label }}</it-checkbox>
          <it-radio v-else v-model="dt.answer" :value="dt.choices[index].id" :label="dt.choices[index].label" />
        </div>
      </div>

      <div class="card-more-footer">
        <div>
        </div>
        <div class="mini-gray-text">{{ dt.score }}</div>
      </div>
      
    </div>
    <div v-else>Пусто</div>
  </div>
</template>

<script>

export default {
  props: ["data"],
  data() {
    return {
      dt: this.data,
    };
  },
  mounted() {
    this.dt.text = this.data?.text ? this.data.text : '';
    this.dt.score = this.data?.score ? this.data.score : 1;
    this.dt.choices = this.data?.choices ? this.data.choices : [];
    this.dt.isMultiple = this.data?.isMultiple ? this.data.isMultiple : false;
    this.dt.answer = this.data?.answer ? this.data.answer : undefined;

    console.log(this.dt);
  },
  methods: {
    
  },
};
</script>

<style>
.choice-options {
  padding-bottom: 6px !important;
  opacity: 0.23;
  transition: opacity 0.2s;
}
.choice-options:hover{
  opacity: 1;
}
.choice-input-line {
  display: grid;
  grid-template-columns: auto 1fr;
}
.btn-choice-delete{
  margin-left: 8px;
}
.choice-input-line .it-btn {
  height: 32px !important;
  width: 32px !important;
}
.choice-line {
  display: flex;
  margin-top: 6px;
  margin-bottom: 6px;
}
.choice-line>*{
  cursor: pointer;
}
.prop-line{
  margin-top: 10px !important;
}

</style>