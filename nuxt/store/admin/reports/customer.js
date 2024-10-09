export const state = () => ({
  customerReports :  [] ,
  customerReportsCount : '',
  customerReport : {}
})

export const mutations = {
  SET_CUSTOMER_REPORTS(state , reports) {
    state.customerReports = reports
  } ,
  SET_TOTAL_CUSTOMER_REPORTS_COUNT(state , customerReportsCount) {
    state.customerReportsCount = customerReportsCount
  },
  SET_CUSTOMER_REPORT ( state , report) {
    state.customerReport = report
  }
}


export const actions = {
  async getCustomerReports(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.post(`report-customers?page=${payload.page}` , payload.form)
        .then(response => {
          state.commit('SET_TOTAL_CUSTOMER_REPORTS_COUNT', response.data.data.meta.total )
          state.commit('SET_CUSTOMER_REPORTS', response.data.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async getCustomerReport(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`report-customers/${payload.id}`)
        .then(response => {
          state.commit('SET_CUSTOMER_REPORT', response.data.data )
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
}
