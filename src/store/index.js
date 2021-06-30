import { createStore } from 'vuex'

import ME from './modules/me'

const CONSTANS = {
  api: 'http://t.myqf.ru/backend/api/main.php',
}

export default createStore({
  state: {
  },
  mutations: {
  },
  actions: {
  },
  getters:{
    const(){return CONSTANS}
  },
  modules: {
    ME,
  }
})
