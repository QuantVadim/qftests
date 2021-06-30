<template>
  <div>
    <div v-if="dt != undefined">
      <div></div>
      <pre>{{ dt.text }}</pre>
      <div class="line-word">
        <span v-for="(item, index) in dt.word" 
          :key="index"
          :class="{'letter-glas':isGlas(item), 
            'letter-percussive':isPercussive(item),
            'letter-space': item == ' ',
            'letter-correct': dt.correct[index].toUpperCase() == dt.correct[index] || dt.correct[index].toLowerCase() == 'ё'
            }" 
        >
        {{item.toLowerCase()}}
        </span>
      </div>
      <div class="display-none">
        <it-input disabled v-model="dt.word" placeholder="Слово"></it-input>
      </div>
      <div class="card-more-footer">
        <div></div>
        <div class="mini-gray-text">{{ dt.score }}</div>
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
      glas: ['а', 'о', 'э', 'е', 'и', 'ы', 'у', 'ё', 'ю', 'я'],
    };
  },
  mounted() {
    this.dt.text = this.data?.text ? this.data.text : '';
    this.dt.word = this.data?.word ? this.data.word : '';
    this.dt.score = this.data.score ? this.data.score : 0;

  },
  methods: {
    isGlas(char){
      return this.glas.indexOf(char.toLowerCase()) >= 0 ? true : false;
    },
    isPercussive(char){
      return this.isGlas(char) ? char == char.toUpperCase() || char == 'ё' : false;
    },

  },
};
</script>

<style scoped>

.letter-percussive{
  background-color: rgb(241, 119, 70);
  border: 1px solid #dd6b3d;
  color: white;
}

.letter-glas.letter-correct{
background-color: #5bb8ff21;
  border: 1px solid #64b2f3;
  color: black;
}
.letter-percussive.letter-correct{

  background-color: #5bb8ff;
  color: white;
}

.display-none{
  display: none;
}

.letter-space{
  padding-right: 10px;
}
.letter-glas{


  padding-left: 4px;
  padding-right: 4px;
  border-radius: 3px;
  cursor: pointer;
  box-shadow: 0px 0px 0px 0px #64b3f300;
}



.line-word{
  min-height: 40px;
  overflow-x: auto;
  text-align: center;
  line-height: 40px;
  font-size: 28px;
  margin-top: 2px;
  margin-bottom: 2px;
}

</style>