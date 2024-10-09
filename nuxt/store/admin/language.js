export const state = () => ({
  languageList: []
})

export const mutations = {
  SET_LANGUAGE_LIST : (state, languageList)  => {
    state.languageList = languageList;
  } ,
  CREATE_LANGUAGE(state , language) {
    state.languageList.unshift(language)
  },
  REMOVE_LANGUAGE(state , languageId){
     let language = state.languageList.find(language => language.id == languageId)
     let index  = state.languageList.indexOf(language)
     state.languageList.splice(index , 1)
  },
  UPDATE_LANGUAGE(state , {languageId , newLanguage}){
    let language = state.languageList.find(language => language.id == languageId)
    language.title = newLanguage.title
  },
}

export const actions = {
  async LoadLanguages(state) {
    this.$axios.get('language')
      .then(response => {
        state.commit('SET_LANGUAGE_LIST', response.data.data)
      })
      .catch(error => console.log(error))
  } ,
  async createNewLanguage(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.post('language' , payload.form)
        .then(response => {
          console.log(response.data.data)
          state.commit('CREATE_LANGUAGE', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async removeLanguage(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.delete(`language/${payload.id}`)
        .then(response => {
          state.commit('REMOVE_LANGUAGE', payload.id)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async updateLanguage(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.put(`language/${payload.id}` , payload.form)
        .then(response => {
          state.commit('UPDATE_LANGUAGE', {languageId : payload.id , newLanguage : response.data.data} )
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
}
