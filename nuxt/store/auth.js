export const state = () => ({
  user :  null ,
  activities : [] ,
  toDos : []
})

export const mutations = {
  SET_LOGGED_IN_USER(state , user) {
    state.user = user
  } ,
  DELETE_LOGGED_OUT_UESR (state , user){
     state.user = {}
  } ,
  SET_AVATAR_USER (state , avatar) {
    state.user.avatar = avatar.value
  } ,
  SET_SIGNATURE_USER (state , signature){
    state.user.signature = signature.value
  },
  SET_USER_NEW_DATA(state , params) {
    console.log(params.dateField)
    if(params.dateField === 'name')
      state.user.name = params.newData
    else if(params.dateField === 'userName')
      state.user.userName = params.newData
    else if(params.dateField === 'email')
      state.user.email = params.newData
    else if(params.dateField === 'mobile')
      state.user.mobile = params.newData
  } ,

  SET_USER_ACTIVITIES(state , activites) {
    state.activities = activites
  } ,
  SET_USER_TO_DOS(state , toDos){
    state.toDos = toDos
  },
  MARK_USER_NOTIFICATION_AS_READ : (state , id) => {
    let today = new Date();
    let date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
    let todo =  state.toDos.find(unread => unread.id === id)
    todo.read_at = date
    if(state.user.unread_notify > 0)
      state.user.unread_notify = state.user.unread_notify-1
  },
  CHANGE_TRAINING_SESSION_COUNT( state  , status) {
    if(status != 'set_time')
      state.user.trainingSessionCount --
    else
      state.user.trainingSessionCount ++
  }
}

export const getters = {
  can : (state) => (permission) => {
     if(state.user) {
       if(state.user.role != undefined)
       {
          if(state.user.role.permissions.some(p => p.title == permission))
            return true
          return false
       }
    }
  },
  canChangeTitleChecklistStatus : (state , getters) => (sectionId) => {
    if(sectionId)
      switch(sectionId) {
        case 1:
          return getters.can('staff-approving-office')
        case 4:
          return getters.can('staff-approving-graphic')
        case 3:
          return getters.can('staff-approving-programmer')
        case 5:
          return getters.can('staff-approving-support')
        case 6:
          return getters.can('staff-approving-sale')
        case 2 :
          return getters.can('staff-approving-design')
        default:
          return false
      }
  },
  canManagerApprove : (state , getters) => (sectionId) =>{
    switch(sectionId) {
      case 1:
        return  getters.can('manager-approving-office')
      case 2 :
        return  getters.can('support-approve-design')
      case 3:
        return  getters.can('manager-approving-programmer')
      case 4:
        return  getters.can('manager-approving-graphic')
      case 5:
        return  getters.can('manager-approving-support')
      case 6:
        return  getters.can('manager-approving-sales')
      default:
        return false
    }
  } ,

  canReverseToSection : (state , getters) => (sectionId) =>{
    switch(sectionId) {
      case 1:
        return  getters.can('reverse-to-office')
      case 3:
        return  getters.can('reverse-to-programmer')
      case 4:
        return  getters.can('reverse-to-graphic')
      case 5:
        return  getters.can('reverse-to-support')
      case 6:
        return  getters.can('reverse-to-sale')
      default:
        return false
    }
  } ,

  canReverseToSections : (state , getters) => (section) => {
    switch(section) {
      case 1:
        return  getters.can('administrator-manager') && getters.can('reverse-to-office')
      case 2:
        return  getters.can('support-reverse-design')
      case 3:
        return  getters.can('reverse-to-programmer') && getters.can('technical-manager')
      case 4:
        return  getters.can('technical-manager') && ( getters.can('reverse-to-graphic') || getters.can('reverse-to-programmer')) ||
                getters.can('graphic') && getters.can('reverse-to-programmer')
      case 5:
        return  getters.can('support-manager') && (getters.can('reverse-to-support') || getters.can('reverse-to-graphic') || getters.can('reverse-to-programmer'))
                || getters.can('support') && ( getters.can('reverse-to-graphic') || getters.can('reverse-to-programmer') )
      case 6:
        return  getters.can('sales-manager') && ( getters.can('reverse-to-sale') || getters.can('reverse-to-support') || getters.can('reverse-to-graphic') || getters.can('reverse-to-programmer') )
                || getters.can('sales') && ( getters.can('reverse-to-support') || getters.can('reverse-to-graphic') || getters.can('reverse-to-programmer') )
      default:
        return false
    }
  } ,

  canSign : (state , getters) => (section) => {
    switch(section) {
      case 1:
        return  getters.can('manager-sign-office-checklist')
      case 2:
        return  getters.can('manager-sign-designer-checklist')
      case 3:
        return  getters.can('manager-sign-programmer-checklist')
      case 4:
        return  getters.can('manager-sign-graphic-checklist')
      case 5:
        return  getters.can('manager-sign-support-checklist')
      case 6:
        return  getters.can('manager-sign-sale-checklist')
      default:
        return false
    }
  },

  canStaffSign : (state , getters) => (section) => {
    switch(section) {
      case 1:
        return  getters.can('staff-sign-office-checklist')
      case 2:
        return  getters.can('staff-sign-designer-checklist')
      case 3:
        return  getters.can('staff-sign-programmer-checklist')
      case 4:
        return  getters.can('staff-sign-graphic-checklist')
      case 5:
        return  getters.can('staff-sign-support-checklist')
      case 6:
        return  getters.can('staff-sign-sale-checklist')
      default:
        return false
    }
  },

  readToDos : (state) => {
    return state.toDos.filter(todo => todo.read_at != null);
  },
  unreadTodos: (state) =>{
    return state.toDos.filter(todo => todo.read_at == null);
  }
}

export const actions = {
  async getLoggedInUser(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.get('details')
        .then(response => {
          state.commit('SET_LOGGED_IN_USER', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,

  async uploadProfileAvatar(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.post('user/avatar/upload' , payload)
        .then(response => {
          state.commit('SET_AVATAR_USER', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,

  async uploadProfileSignature(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.post('user/signature/upload' , payload)
        .then(response => {
          state.commit('SET_SIGNATURE_USER', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,

  async updateUserData(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.put('user/single/update' , payload.params)
        .then(response => {
          state.commit('SET_USER_NEW_DATA', payload.params)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,

  async getActivityList(state) {
    return new Promise((resolve, reject) => {
      this.$axios.get('user/activity/list')
        .then(response => {
          state.commit('SET_USER_ACTIVITIES', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,

  async getToDoList(state) {
    return new Promise((resolve, reject) => {
      this.$axios.get('user/todo/list')
        .then(response => {
          state.commit('SET_USER_TO_DOS', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });

  } ,

  async markAsRead(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.put(`user/todo/${payload.id}/markAsRead`)
        .then(response => {
          state.commit('MARK_USER_NOTIFICATION_AS_READ', payload.id)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,

}
