export const strict = false
export const state = () => ({
  user :  {}
})

export const mutations = {
  SET_LOGGED_IN_USER(state , user) {
    state.user = user
  } ,
}

export const actions = {
  async nuxtServerInit({dispatch}) {
    const token = this.$cookies.get('token')
    if(token)
    await dispatch('auth/getLoggedInUser').catch(error => console.log(error))
  }
}
