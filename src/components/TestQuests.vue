<template>
<div>

<draggable
  v-model="items"
  item-key="id"
  tag="transition-group"
  animation="150"
  handle=".handle-card-center"
  ghost-class="blue-background-class"
>
  <template #item="{ index }">
    <block>
    <div v-if="mode == 'editor'" class="handle-card">
      <div>
        <it-icon @click="deleteCard(index)" name="delete" size="32px" />
      </div>
      <div class="handle-card-center">{{ index+1 }}</div>
      <div class="handle-part-right">
        <it-icon @click="DublicateCard(index)" name="content_copy" />
        <it-icon name="more_horiz" @click="toggle" />
        <div>
          <Menu ref="menu" :model="itemsMenu" :popup="true" />
        </div>
      </div>
    </div>
    <div class="inner-card">
    <component :is="items[index].type.toLowerCase()+'Card_'+mode" :data="items[index]" />
    </div>
    </block>
  </template>
</draggable>
<it-divider/>
<Toolbar v-if="mode == 'editor'"> 
  <template #left>
    <span class="p-buttonset">
    <Button @click="addCard('Simple')" label="Текст" class="p-button-outlined" />
    <Button @click="addCard('Choice')" label="Выбор" class="p-button-outlined" />
    <Button @click="addCard('Orthoepy')" label="Ударение" class="p-button-outlined" />
    </span>
  </template>
</Toolbar>

</div>
</template>

<script>
import draggable from "vuedraggable";
import Menu from 'primevue/menu';
import Button from 'primevue/button';
import Toolbar from 'primevue/toolbar';

//Simple
import SimpleCard_editor from "./TestCards/Editor/Simple";
import SimpleCard_basic from "./TestCards/Basic/Simple";
import SimpleCard_result from "./TestCards/Result/Simple";
//Choice
import ChoiceCard_editor from "./TestCards/Editor/Choice";
import ChoiceCard_basic from "./TestCards/Basic/Choice";
import ChoiceCard_result from "./TestCards/Result/Choice";
//Orthoepy
import OrthoepyCard_editor from "./TestCards/Editor/Orthoepy";
import OrthoepyCard_basic from "./TestCards/Basic/Orthoepy";
import OrthoepyCard_result from "./TestCards/Result/Orthoepy";

export default {
  components: {
    draggable, Menu, Button, Toolbar,
    SimpleCard_editor, SimpleCard_basic, SimpleCard_result,
    ChoiceCard_editor, ChoiceCard_basic, ChoiceCard_result,
    OrthoepyCard_editor, OrthoepyCard_basic, OrthoepyCard_result
  },
  props: ['data', 'mode'],
  data() {
    return {
      items: this.data,

      itemsMenu: [
        {
					label: 'Шото сделать',
					icon: 'pi pi-caret-right',
					command: () => {
					}
				},
      ],
    };
  },
  methods: {
    toggle(event) {
      this.$refs.menu.toggle(event);
    },
    showinfo(){
      console.log(this.items);
    },
    addCard(cardType) {
      switch (cardType) {
        case 'Simple':
        case 'Choice': 
        case 'Orthoepy':
            this.items.push({id: Math.random(), type: cardType });
            break;
        default:
          break;
      }
      
    },
    deleteCard(index){
      this.items.splice(index, 1);
    },
    DublicateCard(index){
      let card =  JSON.parse(JSON.stringify(this.items[index]));
      card.id = Math.random();
      this.items.splice(index+1, 0, card);
    }
  },
};
</script>

<style scoped>
.handle-card-center{
  line-height: 28px;
  color: rgb(206, 206, 206);
}
.handle-part-right .it-icon {
  padding-right: 8px;
}
.btn-add-card {
  line-height: 18px;
  font-size: 18px;
}
.btn-add-card .it-icon {
  line-height: 23px;
  font-size: 26px;
}
.it-icon {
  padding-left: 3px;
  padding-right: 3px;
  font-size: 24px;
  line-height: 30px;
  opacity: 0.5;
}
.inner-card {
  padding: 10px;
}
.block {
  padding: 0px;
  overflow: hidden;
}
.handle-card .it-icon{
  cursor: pointer;
}
.handle-card {
  display: grid;
  grid-template-columns: auto 1fr auto;
  background-color: rgb(236, 236, 236);
  height: 30px;
}
.handle-card:hover {
  background-color: #b6cbf1;
  height: 30px;
  cursor: move;
}
.blue-background-class .handle-card {
  opacity: 0.4;
}
.blue-background-class {
  background-color: #5589ec;
}
</style>