export const state = () => ({
  smsTemplates: []
})

export const mutations = {
  SET_SMS_TEMPLATE_LIST : (state, smsTemplates)  => {
    state.smsTemplates = smsTemplates;
  } ,
  CREATE_SMS_TEMPLATE(state , template) {
    state.smsTemplates.unshift(template)
  },
  REMOVE_SMS_TEMPLATE(state , templateId){
    let template = state.smsTemplates.find(sms => sms.id == templateId)
    let index  = state.smsTemplates.indexOf(template)
    state.smsTemplates.splice(index , 1)
  },
  UPDATE_SMS_TEMPLATE(state , {templateId , newTemplate}){
    let template = state.smsTemplates.find(sms => sms.id == templateId)
    template.title = newTemplate.title
    template.key = newTemplate.key
    template.params = newTemplate.params
    template.template = newTemplate.template
  },
}

export const actions = {
  async LoadSmsTemplates(state) {
    this.$axios.get('smsTemplate')
      .then(response => {
        state.commit('SET_SMS_TEMPLATE_LIST', response.data.data)
      })
      .catch(error => console.log(error))
  } ,
  async createNewSmsTemplate(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.post('smsTemplate' , payload.form)
        .then(response => {
          console.log(response.data.data)
          state.commit('CREATE_SMS_TEMPLATE', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async removeSmsTemplate(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.delete(`smsTemplate/${payload.id}`)
        .then(response => {
          state.commit('REMOVE_SMS_TEMPLATE', payload.id)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async updateSmsTemplate(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.put(`smsTemplate/${payload.id}` , payload.form)
        .then(response => {
          state.commit('UPDATE_SMS_TEMPLATE', {templateId : payload.id , newTemplate : response.data.data} )
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
}
