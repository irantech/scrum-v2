export const state = () => ({
  roleList :  [] ,
  role : ''
})

export const mutations = {
  SET_ADMIN_USER_ROLE_LIST(state , roles) {
    state.roleList = roles
  } ,
  CREATE_ADMIN_USER_ROLE(state , role) {
      state.roleList.unshift(role)
  },
  REMOVE_USER_ROLE(state , roleId){
    let role = state.roleList.find(role => role.id == roleId)
    role.trashed = true
  },
  RESTORE_USER_ROLE(state , roleId){
    let role = state.roleList.find(role => role.id == roleId)
    role.trashed = false
  },
  UPDATE_USER_ROLE(state , {roleId , newRole}){
    let role = state.roleList.find(role => role.id == roleId)
    role.title = newRole.title
    role.type = newRole.type
    role.permissions = newRole.permissions
    role.section = newRole.section
  },
  SET_ADMIN_USER_ROLE(state , newRole){
      state.role = newRole
  }
}


export const actions = {
  async getUserRoleList(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.get('role')
        .then(response => {
          state.commit('SET_ADMIN_USER_ROLE_LIST', response.data.data)
          resolve(response.data.data)
        })
        .catch(error => reject(error))
    });
  } ,
  async getUserRole(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`role/${payload.id}`)
        .then(response => {
          state.commit('SET_ADMIN_USER_ROLE', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async createNewUserRole(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.post('role' , payload.form)
        .then(response => {
          state.commit('CREATE_ADMIN_USER_ROLE', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async removeUserRole(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.delete(`role/${payload.id}`)
        .then(response => {
          state.commit('REMOVE_USER_ROLE', payload.id)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async restoreUserRole(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.put(`role/${payload.id}/restore`)
        .then(response => {
          state.commit('RESTORE_USER_ROLE', payload.id)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async updateUserRole(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.put(`role/${payload.id}` , payload.form)
        .then(response => {
          state.commit('UPDATE_USER_ROLE', {roleId : payload.id , newRole : response.data.data} )
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
}
