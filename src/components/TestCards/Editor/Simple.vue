<template>
  <div>
    <div v-if="dt != undefined">
      <div></div>
      <it-textarea
        v-model="dt.text"
        placeholder="Вопрос"
        :resize-on-write="true"
        :rows="1"
      ></it-textarea>
      <it-input v-model="dt.answer" placeholder="Ответ"></it-input>
      <div class="card-more-footer">
        <div>
          <it-icon
            @click="isMore = !isMore"
            name="expand_more"
            :class="{ 'more-rotate': isMore }"
            class="btn-expand-more"
          />
        </div>
        <div class="mini-gray-text">{{ dt.score }}</div>
      </div>
      <div v-if="isMore" class="more-props-wrapper">
        <div class="prop-line">
          <div>Кол-во баллов</div>
          <it-number-input
            v-model="dt.score"
            :min="1"
            :resize-on-write="true"
            :max="100"
          />
        </div>
      </div>
    </div>
    <div v-else>Пусто</div>
  </div>
</template>

<script>
//  props:{ data: {type: Object, default: {id: Math.random(), type="simple" , text: "", answer: ""} },

export default {
  props: ["data"],
  data() {
    return {
      dt: this.data,
      isMore: false,
    };
  },
  mounted() {
    this.dt.score = this.data.score ? this.data.score : 1;

  },
  methods: {},
};
</script>

<style>
.card-more-footer {
  display: grid;
  grid-template-columns: 1fr auto;
  align-items: center;
}
.mini-gray-text {
  font-size: 10px;
  color: gray;
}
.btn-expand-more {
  font-size: 24px;
  opacity: 0.5;
}
.btn-expand-more:hover {
  border-radius: 100%;
  background-color: rgba(0, 0, 0, 0.2);
  cursor: pointer;
  opacity: 1;
}
.more-rotate {
  transform: rotate(180deg);
}
.prop-line > div:first-child {
  padding-top: 5px;
}
.prop-line {
  font-size: 15px;
  display: grid;
  padding-left: 6px;
  padding-right: 6px;
  grid-template-columns: 1fr auto;
  align-items: center;
  border-top: 1px solid rgb(219, 219, 219);
  border-bottom: 1px solid rgb(219, 219, 219);
  border-radius: 8px;
  padding-bottom: 6px;
}

.more-props-wrapper {
  background-color: rgba(0, 0, 0, 0.05);
  padding: 8px;
  border-radius: 8px;
  box-shadow: 2px 2px 3px rgba(0, 0, 0, 0.15) inset;
  animation: show 0.2s;
}

@keyframes show {
  from{
    opacity: 0;
    padding: 0;
    transform: scale(0.9);
  }
  to{
    opacity: 1;
    transform: scale(1);
  }
}

@media screen and (min-width: 600px) {
  .more-props-wrapper {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
  }
}
</style>