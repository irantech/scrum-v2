export const state = () => ({
  softwareList :  []
})

export const mutations = {
  SET_ADMIN_SOFTWARE_LIST(state , baseProgressList) {
    state.softwareList = baseProgressList
  } ,

}


export const actions = {
  async getSoftwareList(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.get('softwares')
        .then(response => {
          state.commit('SET_ADMIN_SOFTWARE_LIST', response.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,

}
