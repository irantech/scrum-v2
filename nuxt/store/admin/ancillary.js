export const state = () => ({
    ancillary :  {}
})

export const mutations = {
  SET_ADMIN_ANCILLARY(state , ancillary) {
    state.ancillary = ancillary
  } ,
  SET_NEW_ANCILLARY_TITLE (state , params) {
    state.ancillary.title = params.title
  } ,
  SET_NEW_BASE_PROGRESS_STATUS (state , params) {
    let base_progress = state.ancillary.base_progress.find(x => x.id == params.base_progress_id)
    base_progress.pivot.status = params.status
  },
  SET_NEW_SUB_PROGRESS_STATUS(state , params){
    let sub_progress = state.ancillary.sub_progress.find(x => x.id == params.sub_progress_id)
    sub_progress.pivot.status = params.status
  }
}


export const actions = {
  async getAncillaryById(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`ancillary/${payload.id}`)
        .then(response => {
          state.commit('SET_ADMIN_ANCILLARY', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async updateAncillaryTitle(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.put(`ancillary/update-title/${payload.id}`, payload.params)
        .then(response => {
          state.commit('SET_NEW_ANCILLARY_TITLE', payload.params)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async removeAncillary(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.delete(`ancillary/${payload.id}`)
        .then(response => {
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async changeAncillaryBaseStatus(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.put('ancillary-change-base-progress', payload.params)
        .then(response => {
          state.commit('SET_NEW_BASE_PROGRESS_STATUS' , payload.params)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async changeAncillarySubStatus(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.put('ancillary-change-sub-progress', payload.params)
        .then(response => {
          state.commit('SET_NEW_SUB_PROGRESS_STATUS' , payload.params)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async addAncillaryBaseProgress(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.put(`ancillary/${payload.id}` , payload.params)
        .then(response => {
          //state.commit('ADD_BASE_TO_ANCILLARY' , payload.params)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
}
