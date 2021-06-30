import axios from 'axios';
import helpers from '../../others/helpers'

export default ({
  //namespaced: true,
  state: {
    data: {}
  },
  mutations: {
    SET_USER(state, user) {
      state.data = user;
    }
  },
  actions: {
    async LOAD_USER(ctx) {
      let obj = {
        q: 'login',
        data:{
          usr_id: helpers.getCookie('usrid'),
          mykey: helpers.getCookie('mykey')
        }
      };
      console.log(obj);
      axios.post(window.c.API, obj).then(itm => {
        console.log(itm.data);
        if(itm.data.data){
          ctx.commit('SET_USER', itm.data.data);
        }
      });
    }
  },
  getters:{
    MYID(state){
      return state.data?.usr_id | false; 
    }
  },
  modules: {
  }
})