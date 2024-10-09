export const state = () => ({
  request_list : [] ,
  staff_request_list : [] ,
  admin_request_list : [] ,
})

export const mutations = {
  SET_REQUEST_LIST(state , requests){
    state.request_list = requests
  },
  SET_STAFF_REQUEST_LIST(state , requests){
    state.staff_request_list = requests
  },
  SET_ADMIN_REQUEST_LIST(state , requests){
    state.admin_request_list = requests
  },
  CHANGE_REQUEST_STATUS(state , payload){
    let request = state.request_list.find( x => x.id == payload.id)
    request.has_confirmed = payload.form.has_confirmed
  }
}

export const actions = {
  async getManagerRequestList(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.post('manager/requests'  ,payload)
        .then(response => {
          state.commit('SET_REQUEST_LIST', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  },
  async getStaffRequestList(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.get('staff/requests'  ,payload)
        .then(response => {
          state.commit('SET_STAFF_REQUEST_LIST', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  },
  async getAdminRequestList(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.post('admin/requests'  ,payload)
        .then(response => {
          state.commit('SET_ADMIN_REQUEST_LIST', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  },
  async updateManagerRequestList(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.put(`staffRequest/${payload.id}`  ,payload.form)
        .then(response => {
          state.commit('CHANGE_REQUEST_STATUS', payload)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  },
}
