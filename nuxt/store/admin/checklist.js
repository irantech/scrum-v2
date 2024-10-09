export const state = () => ({
  checklists :  [] ,
  checklist : {}
})

export const mutations = {
  SET_ADMIN_CHECK_LISTS(state , checklists) {
    state.checklists = checklists
  } ,
  SET_ADMIN_CHECK_LIST(state , checklist) {
    state.checklist = checklist
  },
  RESTORE_CHECK_LIST_BY_ID(state , checklistId) {
    let checklist = state.checklists.find(check => check.id == checklistId)
    checklist.trashed = false
  },
  REMOVE_CHECK_LIST_BY_ID(state , checklistIds){
    let checklist = state.checklists.find(check => check.id == checklistIds)
    checklist.trashed = true
  },
  UPDATE_CHECK_LIST(state ,   { newChecklist , checklistId }) {
    let checklist = state.checklists.find(check => check.id == checklistId)
    checklist.title = newChecklist.title
    checklist.description = newChecklist.description
    checklist.language = newChecklist.language
    checklist.sections = newChecklist.sections
  },
  ADD_NEW_CHECK_LIST(state , payload) {
    state.checklists.push(payload)
  } ,
  ADD_NEW_TITLE_CHECK_LIST(state , payload) {
    state.checklist.title_checklists.push(payload)
  } ,
  UPDATE_TITLE_CHECK_LIST(state , {newTitleCheckList , id}) {
    let titleChecklist = state.checklist.title_checklists.find(check => check.id == id)
    titleChecklist.title = newTitleCheckList.title
    titleChecklist.description = newTitleCheckList.description
    titleChecklist.section = newTitleCheckList.section
  },
  REMOVE_TITLE_CHECK_LIST_BY_ID(state , titleChecklistIds){
    let titleChecklist = state.checklist.title_checklists.find(check => check.id == titleChecklistIds)
    let index = state.checklist.title_checklists.indexOf(titleChecklist);
    state.checklist.title_checklists.splice(index , 1)
  },
}

export const getters = {
  getBySection : (state) => (section) => {
    if(section)
      return state.checklist.title_checklists.filter(title => {
      let newTitle = title.section.map(sect => sect.id)
      return  newTitle.includes(section)
    })
    else
      return state.checklist.title_checklists
  } ,
}
export const actions = {
  async getChecklists(state) {
    return new Promise((resolve, reject) => {
      this.$axios.get('checklist')
        .then(response => {
          state.commit('SET_ADMIN_CHECK_LISTS', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async getChecklist(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`checklist/${payload.id}`)
        .then(response => {
          state.commit('SET_ADMIN_CHECK_LIST', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async restoreChecklist(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.put(`checklist/${payload.id}/restore`)
        .then(response => {
          state.commit('RESTORE_CHECK_LIST_BY_ID', payload.id)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async removeChecklist(state , payload) {
      return new Promise((resolve, reject) => {
        this.$axios.delete(`checklist/${payload.id}`)
          .then(response => {
            state.commit('REMOVE_CHECK_LIST_BY_ID', payload.id)
            resolve(response)
          })
          .catch(error => reject(error))
      });
    } ,
  async createNewChecklist(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.post(`checklist` , payload.form)
        .then(response => {
          state.commit('ADD_NEW_CHECK_LIST', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async updateChecklist(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.put(`checklist/${payload.id}`, payload.form)
        .then(response => {
          state.commit('UPDATE_CHECK_LIST', {newChecklist : response.data.data , checklistId : payload.id})
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async createNewTitleChecklist(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.post(`titleChecklist` , payload.form)
        .then(response => {
          state.commit('ADD_NEW_TITLE_CHECK_LIST', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async updateTitleChecklist(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.put(`titleChecklist/${payload.id}`, payload.form)
        .then(response => {
          state.commit('UPDATE_TITLE_CHECK_LIST', {newTitleCheckList : response.data.data , id : payload.id})
          resolve(response)
        })
        .catch(error => reject(error))
    });
  },
  async removeTitleChecklist(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.delete(`titleChecklist/${payload.id}`)
        .then(response => {
          state.commit('REMOVE_TITLE_CHECK_LIST_BY_ID', payload.id)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
}
