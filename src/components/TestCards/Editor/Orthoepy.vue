<template>
  <div>
    <div v-if="dt != undefined">
      <div></div>
      <it-textarea
        v-model="dt.text"
        placeholder="Контекст"
        :resize-on-write="true"
        :rows="1"
      ></it-textarea>
      <div class="line-word">
        <span v-for="(item, index) in dt.word" 
          :key="index"
          :class="{'letter-glas':isGlas(item), 
            'letter-percussive':isPercussive(item),
            'letter-space': item == ' '}" 
          @click="switchEmphasis(index)"
        >
        {{item.toLowerCase()}}
        </span>
      </div>
      <it-input v-model="dt.word" placeholder="Слово"></it-input>
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
      glas: ['а', 'о', 'э', 'е', 'и', 'ы', 'у', 'ё', 'ю', 'я'],
    };
  },
  mounted() {
    this.dt.text = this.data?.text ? this.data.text : '';
     this.dt.word = this.data?.word ? this.data.word : '';
    this.dt.score = this.data.score ? this.data.score : 1;

  },
  methods: {
    isGlas(char){
      return this.glas.indexOf(char.toLowerCase()) >= 0 ? true : false;
    },
    isPercussive(char){
      return this.isGlas(char) ? char == char.toUpperCase() || char == 'ё' : false;
    },
    switchEmphasis(index){
      let char = this.dt.word[index];
      if(this.isGlas(char)){
        char = char.toUpperCase() == char ? char.toLowerCase() : char.toUpperCase();
        let w = this.dt.word;
        this.dt.word = w.substr(0, index)+char+w.substr(index+1); 
      }
    } 

  },
};
</script>

<style scoped>
.letter-space{
  padding-right: 10px;
}
.letter-glas{

  border: 1px solid #63b7fc5b;
  padding-left: 4px;
  padding-right: 4px;
  border-radius: 3px;
  cursor: pointer;
  box-shadow: 0px 0px 0px 0px #64b3f300;
}
.letter-glas:hover{
  box-shadow: 0px 0px 2px 1px #64b2f3;
}
.letter-percussive{
  background-color: #5bb8ff;
  border: 1px solid #64b2f3;
  color: white;
}

.line-word{
  min-height: 40px;
  overflow-x: auto;
  text-align: center;
  line-height: 40px;
  font-size: 28px;
  margin-top: 6px;
  margin-bottom: 10px;
}

</style>