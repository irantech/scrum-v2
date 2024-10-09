export const state = () => ({
  customers :  [] ,
  customersCount : ''
})

export const mutations = {
  SET_CUSTOMERS(state , customers) {
    state.customers = customers
  } ,
  SET_TOTAL_CUSTOMERS_COUNT(state , customersCount) {
    state.customersCount = customersCount
  }
}


export const actions = {
  async getCustomers(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.post(`https://api.ladyscarf.ir/api/curl/filtered-data`, {
        page: `${payload.page}`,
        perPage: `${payload.perPage}`,
        type: `${payload.type}`,
        lost: `${payload.lost}`,
        statusType: `${payload.statusType}`,
        status: `${payload.status}`,
        startDate: `${payload.startDate}`,
        endDate: `${payload.endDate}`,
        mob_manager: `${payload.mob_manager}`,
        sites: `${payload.sites}`,
        name: `${payload.name}`
      })
        .then(response => {
          const data = response.data.info;
          // console.log(data)
          // console.log(mergedArray);
          state.commit('SET_TOTAL_CUSTOMERS_COUNT', response.data.count )
          state.commit('SET_CUSTOMERS', data)
          resolve(data)
        })
        .catch(error => reject(error))
    });
  }  ,
}
