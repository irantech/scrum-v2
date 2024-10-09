export const state = () => ({
  baseProgressList :  [] ,
  baseProgress : {}
})

export const mutations = {
  SET_ADMIN_BASE_PROGRESS_LIST(state , baseProgressList) {
    state.baseProgressList = baseProgressList
  } ,
  SET_ADMIN_BASE_PROGRESS(state , baseProgress) {
    state.baseProgress = baseProgress
  },
  RESTORE_BASE_PROGRESS_BY_ID(state , baseProgressId) {
    let baseProgress = state.baseProgressList.find(base => base.id == baseProgressId)
    baseProgress.trashed = false
  },
  REMOVE_BASE_PROGRESS_BY_ID( state , baseProgressId){
    let baseProgress = state.baseProgressList.find(base => base.id == baseProgressId)
    baseProgress.trashed = true
  },
  ADD_NEW_BASE_PROGRESS( state , baseProgress) {

  } ,
  UPDATE_BASE_PROGRESS(state , payload) {
    let baseProgress = state.baseProgressList.find(base => base.id == payload.id)
    baseProgress.software_id = payload.form.software_id
    baseProgress.software.id = payload.form.software_id
    baseProgress.software.title = payload.form.softwareTitle
    baseProgress.user_role = payload.form.user_role
    baseProgress.title = payload.form.title
    baseProgress.description = payload.form.description
    baseProgress.private_description = payload.form.private_description
    baseProgress.section_id = payload.form.section_id
    baseProgress.percentage = payload.form.percentage
  },
  ADD_NEW_SUB_BASE_PROGRESS(state , payload) {
    payload.trashed = false
    state.baseProgress.progress.push(payload)
  } ,
  REMOVE_SUB_BASE_PROGRESS (state  , payload) {
    let progress = state.baseProgress.progress.find(p => p.id == payload)
    progress.trashed = true
  },
  RESTORE_SUB_BASE_PROGRESS( state , payload ) {
    let progress = state.baseProgress.progress.find(p => p.id == payload)
    progress.trashed = false
  },
  UPDATE_SUB_BASE_PROGRESS( state , payload ){
    let  progress = state.baseProgress.progress.find(p => p.id == payload.id)
    progress.section_id = payload.form.section_id
    progress.title = payload.form.title
    progress.description = payload.form.description
  }
}


export const actions = {
  async getBaseProgressList(state) {
    return new Promise((resolve, reject) => {
      this.$axios.get('base-progress/all')
        .then(response => {
          state.commit('SET_ADMIN_BASE_PROGRESS_LIST', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async getBaseProgress(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`base-progress/${payload.id}`)
        .then(response => {
          state.commit('SET_ADMIN_BASE_PROGRESS', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async restoreBaseProgress(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.put(`base-progress/${payload.id}/restore`)
        .then(response => {
          state.commit('RESTORE_BASE_PROGRESS_BY_ID', payload.id)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
   async removeBaseProgress(state , payload) {
      return new Promise((resolve, reject) => {
        this.$axios.delete(`base-progress/${payload.id}`)
          .then(response => {
            state.commit('REMOVE_BASE_PROGRESS_BY_ID', payload.id)
            resolve(response)
          })
          .catch(error => reject(error))
      });
    } ,
  async createNewBaseProgress(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.post(`base-progress` , payload.form)
        .then(response => {
          state.commit('ADD_NEW_BASE_PROGRESS', payload.form)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async updateBaseProgress(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.put(`base-progress/${payload.id}`, payload.form)
        .then(response => {
          state.commit('UPDATE_BASE_PROGRESS', payload)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async addNewSubBaseProgress(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.post(`progress` , payload.form)
        .then(response => {
          state.commit('ADD_NEW_SUB_BASE_PROGRESS', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async removeSubBaseProgress(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.delete(`progress/${payload.id}`)
        .then(response => {
          state.commit('REMOVE_SUB_BASE_PROGRESS', payload.id)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async restoreSubBaseProgress( state , payload){
    return new Promise((resolve, reject) => {
      this.$axios.put(`progress/${payload.id}/restore`)
        .then(response => {
          state.commit('RESTORE_SUB_BASE_PROGRESS', payload.id)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  },
  async updateSubBaseProgress( state , payload){
    return new Promise((resolve, reject) => {
      this.$axios.put(`progress/${payload.id}`, payload.form)
        .then(response => {
          state.commit('UPDATE_SUB_BASE_PROGRESS', payload)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  }

}
