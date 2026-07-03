import { createStore } from 'vuex'

export default createStore({
  state() {
    return {
      // Tempat menyimpan data profil secara global
      profile: null,
      categories: null,
      newevents: null,
    }
  },
  mutations: {
    // Mutation untuk mengubah nilai state profile
    SET_PROFILE(state, payload) {
      state.profile = payload
    },
    SET_CATEGORIES(state, payload) {
      state.categories = payload
    },
    SET_NEWEVENTS(state, payload) {
      state.newevents = payload
    }
  },
  actions: {
    // Action untuk memicu perubahan data jika diperlukan
    updateProfile({ commit }, data) {
      commit('SET_PROFILE', data)
    },
    updateCategories({ commit }, data) {
      commit('SET_CATEGORIES', data)
    },
    updateNewEvents({ commit }, data) {
      commit('SET_NEWEVENTS', data)
    }
  },
  getters: {
    // Mengambil data profile dari state
    getProfile: (state) => state.profile,
    getCategories: (state) => state.categories,
    getNewEvents: (state) => state.newevents
  }
})


