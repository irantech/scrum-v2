export const state = () => ({
  titleChecklists :  [] ,
  titleChecklist : {}
})

export const mutations = {
  SET_ADMIN_TITLE_CHECK_LISTS(state , titleChecklists) {
    state.titleChecklists = titleChecklists
  } ,
  SET_ADMIN_TITLE_CHECK_LIST(state , titleChecklist) {
    state.titleChecklist = titleChecklist
  },
  RESTORE_TITLE_CHECK_LIST_BY_ID(state , titleChecklistId) {
    let titleChecklist = state.titleChecklists.find(check => check.id == titleChecklistId)
    titleChecklist.trashed = false
  },
}


export const actions = {
  async getTitleChecklists(state) {
    return new Promise((resolve, reject) => {
      this.$axios.get('titleChecklist')
        .then(response => {
          state.commit('SET_ADMIN_TITLE_CHECK_LISTS', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async getTitleChecklist(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`titleChecklist/${payload.id}`)
        .then(response => {
          state.commit('SET_ADMIN_TITLE_CHECK_LIST', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async restoreTitleChecklist(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.put(`titleChecklist/${payload.id}/restore`)
        .then(response => {
          state.commit('RESTORE_TITLE_CHECK_LIST_BY_ID', payload.id)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,

}
