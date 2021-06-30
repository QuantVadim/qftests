<template>
  <div>
    <div v-if="dt != undefined">
      <div class="choice-options">
        <it-switch class="choice-option" v-model="dt.isMultiple" label="Множественный выбор" />
      </div>
      <it-textarea
        v-model="dt.text"
        placeholder="Вопрос"
        :resize-on-write="true"
        :rows="1"
      ></it-textarea>
      <div class="choices-body">
        <div v-for="(item, index) in dt.choices" :key="index" class="choice-line">
          <it-checkbox v-if="dt.isMultiple" v-model="dt.choices[index].selected"> </it-checkbox>
          <it-radio v-else v-model="dt.answer" :value="dt.choices[index].id"/>
          <span @click="setEditingMode(dt.choices[index].id)">{{
            dt.choices[index].label
          }}</span>
          <div>
            <it-icon @click="deleteChoice(index)" class="btn-choice-delete" name="highlight_off" color="#838996" />
          </div>
        </div>
      </div>
      <div class="choice-input-line">
        <div>
          <it-button @click="setEditingMode(false)" size="small" v-if="curIdEditing != undefined">X</it-button>
        </div>
        <it-input
          v-model="inputChoice"
          @keyup.enter="EnterChice"
          placeholder="Добавить пункт"
        >
        </it-input>
      </div>

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

export default {
  props: ["data"],
  data() {
    return {
      dt: this.data,

      curIdEditing: undefined,
      inputChoice: "",
      isMore: false,
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
    deleteChoice(index){
      this.dt.choices.splice(index, 1);
    },
    setEditingMode(id) {
      let obj = {};
      if (id != false) {
        this.dt.choices.forEach((element) => {
          if (element.id == id) obj = element;
        });
        if (obj != undefined) {
          this.curIdEditing = id;
          this.inputChoice = obj.label;
        }
      }else{
        this.curIdEditing = undefined;
        this.inputChoice = '';
      }
    },
    EnterChice() {
      if(this.inputChoice.trim().length == 0) return false;
      let obj = undefined;
      if (this.curIdEditing == undefined) {
        obj = {
          id: Math.random(),
          label: this.inputChoice.trim(),
          selected: false,
        };
        this.dt.choices.splice(this.dt.choices.length, 0, obj);
      } else {
        let indexInsert = this.dt.choices.length;
        for (let index = 0; index < this.dt.choices.length; index++) {
          const element = this.dt.choices[index];
          if (element.id == this.curIdEditing) {
            indexInsert = index;
            obj = this.dt.choices[index];
          }
        }
        if(obj){
           obj.label = this.inputChoice.trim();
           this.dt.choices.splice(indexInsert, 1, obj);
        }else{
          obj = {
          id: Math.random(),
          label: this.inputChoice.trim(),
          selected: false,
          }
          this.dt.choices.splice(indexInsert, 0, obj);
        }
      }
      this.inputChoice = "";
      this.curIdEditing = undefined;
    },
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