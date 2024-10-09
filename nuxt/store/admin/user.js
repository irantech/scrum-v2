export const state = () => ({
  userList :  [] ,
  managerList : []
})

export const mutations = {
  SET_ADMIN_USER_LIST(state , users) {
    state.userList = users
  } ,
  ADD_ADMIN_USER(state , user) {
    state.userList.unshift(user)
  },
  REMOVE_ADMIN_USER(state , payload) {
    let user = state.userList.find(user => user.id == payload.id)
    user.trashed = true
  },
  RESTORE_ADMIN_USER(state , payload) {
    let user = state.userList.find(user => user.id == payload.id)
    user.trashed = false
  },
  UPDATE_ADMIN_USER(state , newUser) {
    let user = state.userList.find(user => user.id == newUser.id)
    user.name = newUser.name
    user.userName = newUser.userName
    user.email = newUser.email
    user.role = newUser.role
  } ,
  SET_MANAGER_LIST( state  , managers) {
    state.managerList = managers
  },
}

export const getters = {
  getUserBySection : (state) => (section) => {
    if(section)
     return  state.userList.filter(user => {
       if(user.role.section)
          return user.role.section.id === section
     })
    else
      return state.userList
  } ,
  getUserName : (state) => (userId) =>{
    let user = state.userList.find(user => user.id === userId)
    if(user)
      return user.name
  },
  getManagerSignature : (state) => (sectionId) =>{
    console.log(sectionId)
    if(sectionId === 3) sectionId = 2
    let manager = state.managerList.find(manager => manager.role.section.id === sectionId)
    if(manager)
      return manager.name
  }
}

export const actions = {
  async getUserList(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.get('user/list')
        .then(response => {
          state.commit('SET_ADMIN_USER_LIST', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async createNewUser(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.post('user', payload.form)
        .then(response => {
          state.commit('ADD_ADMIN_USER', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  },
  async removeUser(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.delete(`user/${payload.id}`)
        .then(response => {
          state.commit('REMOVE_ADMIN_USER', payload)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async restoreUser(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.put(`user/${payload.id}/restore`)
        .then(response => {
          state.commit('RESTORE_ADMIN_USER', payload)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async updateUser(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.put(`user/${payload.id}` , payload.form)
        .then(response => {
          state.commit('UPDATE_ADMIN_USER', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,

  async getManager(state) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`user/managers/get`)
        .then(response => {
          state.commit('SET_MANAGER_LIST', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,

}
