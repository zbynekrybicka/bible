import { createStore } from 'vuex'
import axios from 'axios'

export default createStore({
  state: {
    authToken: null,
    success: false,
    preloader: false,
    data: null,
  },
  getters: {
    knihy(state) {
      return state.data.knihy
    },
    slovnikAvailableByVersId: (state) => (id) => {
      const pouziteVyrazy = state.data.lekce_verse_slovnik.filter(lvs => lvs.lekce_vers_id === id)
      const slovnik = state.data.slovnik.filter(s => !pouziteVyrazy.some(v => v.slovnik_id === s.id)).map(s => s.vyraz)
      return slovnik
    },
    slovnikByVersId: (state) => (id) => {
      const slovnikIds = state.data.lekce_verse_slovnik.filter(lvs => lvs.lekce_vers_id === id).map(lvs => lvs.slovnik_id)
      return state.data.slovnik.filter(s => slovnikIds.some(sid => s.id === sid))
    },
    verseByLekceId: (state) => (id) => {
      return state.data.lekce_verse.filter(lv => lv.lekce_id === id)
    },
    lekce(state) {
      return state.data.lekce
    },
    lekceById: (state) => (id) => {
      return state.data.lekce.find(l => l.id === parseInt(id))
    },
    isLoggedIn(state) {
      return !!state.authToken && !!state.data
    }
  },
  mutations: {
    putLekceNovyVers(state, novyVers) {
      const index = state.data.lekce_verse.findIndex(lv => lv.id === novyVers.id)
      state.data.lekce_verse[index].kniha = novyVers.kniha
      state.data.lekce_verse[index].nazev = novyVers.nazev
      state.data.lekce_verse[index].kapitola = novyVers.kapitola
      state.data.lekce_verse[index].from = novyVers.from
      state.data.lekce_verse[index].to = novyVers.to
      state.data.lekce_verse[index].obsah = novyVers.obsah
    },
    postLekce(state, lekce) {
      state.data.lekce.push(lekce)
    },
    postSlovnik(state, data) {
      if (!state.data.slovnik.find(s => s.id === data.slovnik.id)) {
        state.data.slovnik.push(data.slovnik)
      }
      state.data.lekce_verse_slovnik.push(data.lekce_verse_slovnik)
    },
    deleteVers(state, id) {
      const index = state.data.lekce_verse.findIndex(lv => lv.id === id)
      state.data.lekce_verse.splice(index, 1)
      state.data.lekce_verse_slovnik = state.data.lekce_verse_slovnik.filter(lvs => lvs.lekce_vers_id !== id)
    },
    deleteLekceVerseSlovnik(state, lekceVersSlovnik) {
      const index = state.data.lekce_verse_slovnik.findIndex(lvs => 
        lvs.lekce_vers_id === lekceVersSlovnik.lekce_verse_id && 
        lvs.slovnik_id === lekceVersSlovnik.slovnik_id)
      state.data.lekce_verse_slovnik.splice(index, 1)
    },
    putLekceVerse(state, vers) {
      const index = state.data.lekce_verse.findIndex(v => v.id === vers.id)
      state.data.lekce_verse[index].vysvetleni = vers.vysvetleni
    },
    postLekceVerse(state, vers) {
      state.data.lekce_verse.push(vers)
    },
    putLekce(state, lekce) {
      const index = state.data.lekce.findIndex(l => l.id === lekce.id)
      state.data.lekce[index].nazev = lekce.nazev
      state.data.lekce[index].popis = lekce.popis
    },
    getAll(state, data) {
      state.data = data
    },
    success(state) {
      setTimeout(() => state.success = false, 5000)
      state.success = true
    },
    setLoading(state, isLoading) {
      state.preloader = isLoading
    },
    logout(state) {
      localStorage.removeItem('bozi-slovo-authToken')
      state.authToken = null
    },    
    authenticate(state, authToken) {
      localStorage.setItem('bozi-slovo-authToken', authToken)
      state.authToken = authToken
    }
  },
  actions: {
    loadAuthToken({ commit, dispatch }) {
      let authToken = localStorage.getItem('bozi-slovo-authToken')
      if (authToken) {
        commit("authenticate", authToken)
        dispatch("getAll")
      }
    },
    authenticate({ commit, dispatch }, code) {
      commit('setLoading', true)
      return axios
        .post(window.API_URL + '/authenticate', { code })
        .then((response) => {
          commit("authenticate", response.data)
          commit("success")
          dispatch("getAll")
        })
        .catch((error) => {
          console.error(error)
          commit("setError", error.response.data)
        })
        .finally(() => {
          commit('setLoading', false)
        })
    },

    getAll({ commit, state }) {
      commit('setLoading', true)
      return axios
        .get(window.API_URL + '/all', { headers: { Authorization: `Bearer ${state.authToken}` }})
        .then((response) => {
          commit("getAll", response.data)
          commit("success")
        })
        .catch((error) => {
          console.error(error)
          commit("setError", error.response.data)
        })
        .finally(() => {
          commit('setLoading', false)
        })
    },

    postLekce({ commit, state }) {
      commit('setLoading', true)
      return axios
        .post(window.API_URL + '/lekce', { nazev: '' }, { headers: { Authorization: `Bearer ${state.authToken}` }})
        .then(response => {
          commit("postLekce", response.data)
          commit("success")
        })
        .catch((error) => {
          console.error(error)
          commit("setError", error.response.data)
        })
        .finally(() => {
          commit('setLoading', false)
        })
    },

    putLekce({ commit, state }, lekce) {
      commit('setLoading', true)
      return axios
        .put(window.API_URL + '/lekce', lekce, { headers: { Authorization: `Bearer ${state.authToken}` }})
        .then(() => {
          commit("putLekce", lekce)
          commit("success")
        })
        .catch((error) => {
          console.error(error)
          commit("setError", error.response.data)
        })
        .finally(() => {
          commit('setLoading', false)
        })
    },

    postLekceVerse({ commit, state }, vers) {
      commit('setLoading', true)
      return axios
        .post(window.API_URL + '/lekce-verse', vers, { headers: { Authorization: `Bearer ${state.authToken}` }})
        .then((response) => {
          commit("postLekceVerse", response.data)
          commit("success")
        })
        .catch((error) => {
          console.error(error)
          commit("setError", error.response.data)
        })
        .finally(() => {
          commit('setLoading', false)
        })
    },

    putLekceVerse({ commit, state }, vers) {
      commit('setLoading', true)
      return axios
        .put(window.API_URL + '/lekce-verse', vers, { headers: { Authorization: `Bearer ${state.authToken}` }})
        .then(() => {
          commit("putLekceVerse", vers)
          commit("success")
        })
        .catch((error) => {
          console.error(error)
          commit("setError", error.response.data)
        })
        .finally(() => {
          commit('setLoading', false)
        })
    },

    postSlovnik({ commit, state }, slovnik) {
      commit('setLoading', true)
      return axios
        .post(window.API_URL + '/slovnik', slovnik, { headers: { Authorization: `Bearer ${state.authToken}` }})
        .then((response) => {
          commit("postSlovnik", response.data)
          commit("success")
        })
        .catch((error) => {
          console.error(error)
          commit("setError", error.response.data)
        })
        .finally(() => {
          commit('setLoading', false)
        })
    },

    putSlovnik({ commit, state }, slovnik) {
      commit('setLoading', true)
      return axios
        .put(window.API_URL + '/slovnik', slovnik, { headers: { Authorization: `Bearer ${state.authToken}` }})
        .then(() => {
          commit("putSlovnik", slovnik)
          commit("success")
        })
        .catch((error) => {
          console.error(error)
          commit("setError", error.response.data)
        })
        .finally(() => {
          commit('setLoading', false)
        })
    },

    deleteLekceVerseSlovnik({ commit, state }, lekceVersSlovnik) {
      commit('setLoading', true)
      return axios
        .delete(window.API_URL + `/lekce-verse-slovnik/${lekceVersSlovnik.lekce_vers_id}-${lekceVersSlovnik.slovnik_id}`,
          { headers: { Authorization: `Bearer ${state.authToken}` }})
        .then(() => {
          commit("deleteLekceVerseSlovnik", lekceVersSlovnik)
          commit("success")
        })
        .catch((error) => {
          console.error(error)
          commit("setError", error.response.data)
        })
        .finally(() => {
          commit('setLoading', false)
        })
    },

    deleteVers({ commit, state }, id) {
      commit('setLoading', true)
      return axios
        .delete(window.API_URL + `/lekce-verse/${id}`,
          { headers: { Authorization: `Bearer ${state.authToken}` }})
        .then(() => {
          commit("deleteVers", id)
          commit("success")
        })
        .catch((error) => {
          console.error(error)
          commit("setError", error.response.data)
        })
        .finally(() => {
          commit('setLoading', false)
        })
    },

    putLekceNovyVers({ commit, state }, novyVers) {
      commit('setLoading', true)
      return axios
        .put(window.API_URL + '/lekce-verse-novy-vers', novyVers, { headers: { Authorization: `Bearer ${state.authToken}` }})
        .then(response => {
          commit("putLekceNovyVers", response.data)
          commit("success")
        })
        .catch((error) => {
          console.error(error)
          commit("setError", error.response.data)
        })
        .finally(() => {
          commit('setLoading', false)
        })
    }

  },
  modules: {
  }
})
