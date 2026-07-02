import { createStore } from 'vuex'

export default createStore({
  state() {
    return {
      // Tempat menyimpan data profil secara global
      profile: null
    }
  },
  mutations: {
    // Mutation untuk mengubah nilai state profile
    SET_PROFILE(state, payload) {
      state.profile = payload
    }
  },
  actions: {
    // Action untuk memicu perubahan data jika diperlukan
    updateProfile({ commit }, data) {
      commit('SET_PROFILE', data)
    }
  },
  getters: {
    // Mengambil data profile dari state
    getProfile: (state) => state.profile
  }
})


