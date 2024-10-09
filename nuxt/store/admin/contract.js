export const state = () => ({
  contractList: [] ,
  contract : {} ,
  contractMonthInYear : [],
  contractCountInYear : [],
})

export const mutations = {
  SET_ADMIN_CONTRACT_LIST : (state, contracts)  => {
    state.contractList = contracts;
  } ,
  SET_ADMIN_CONTRACT : (state, contract)  => {
    state.contract = contract;
  } ,
  ADD_NEW_ANCILLARY_TO_CONTRACT : (state , ancillary) => {
    state.contract.ancillary.push(ancillary)
  },
  REMOVE_ANCILLARY_FROM_CONTRACT_LIST : (state , ancillary) =>{
    let ancillaryIndex = state.contract.ancillary.indexOf(ancillary)
    state.contract.ancillary.splice( ancillaryIndex , 1)
  } ,
  UPDATE_CONTRACT_START_DATE : (state , params) => {
    if(params.dateField == 'start')
      state.contract.start_date = params.newDate
    else if(params.dateField == 'End')
      state.contract.end_date = params.newDate
    else if(params.dateField == 'Sign')
      state.contract.sign_date = params.newDate
  },
  UPDATE_CONTRACT_DOMAIN : (state , params) => {
    state.contract.domain_link = params.domain_link
  },
  UPDATE_CONTRACT_THEME : (state , params) => {
    state.contract.theme_link = params.theme_link
  },
  SET_CONTRACT_COUNT_IN_MONTH : (state , params) => {
    state.contractCountInYear = params.contractCount
    state.contractMonthInYear = params.contractYears
  } ,
  ADD_CHECKLIST_TO_CONTRACT : (state , newChecklists) => {
    state.contract.checklists = newChecklists.checklists
    state.contract.checklist_contract = newChecklists.contract_checklist
  },
}

export const getters = {
  filterContracts : (state) => (customerName , contractTitle  , contractCode , startYear , endYear) => {
    let contracts  = state.contractList
    if(customerName)
      contracts = contracts.filter( contract => {
        if(contract.customer)
          return contract.customer.name.toLowerCase().includes(customerName.toLowerCase())
      })
    if(contractTitle)
      contracts = contracts.filter( contract => {
        return contract.title.toLowerCase().includes(contractTitle.toLowerCase())
      })
    if(contractCode)
      contracts = contracts.filter( contract => {
                return contract.contract_code.toLowerCase().includes(contractCode.toLowerCase())
      })
    if (startYear) {
      contracts = contracts.filter( contract => {
        return startYear <= contract.sign_date;
      })
    }
    if (endYear) {
      contracts = contracts.filter( contract => {
        return contract.sign_date <= endYear;
      })
    }
      return contracts
  } ,
  getChecklistContract : (state) => (checklist) =>{
    let contract_checklist = state.contract.checklist_contract.find(x => parseInt(x.checklist_id) === checklist)
    return contract_checklist.id;
  }
}

export const actions = {
  async LoadAdminContracts(state) {
    return new Promise((resolve, reject) => {
      this.$axios.get('contract')
        .then(response => {
          resolve(response)
          state.commit('SET_ADMIN_CONTRACT_LIST', response.data.data)
        })
        .catch(error => reject(error))
    });
  } ,
  async getContractById(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`contract/${payload.id}`)
        .then(response => {
          state.commit('SET_ADMIN_CONTRACT', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async removeAncillaryFromContract(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.delete(`ancillary/${payload.ancillary.id}`)
        .then(response => {
          state.commit('REMOVE_ANCILLARY_FROM_CONTRACT_LIST', payload.ancillary)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  },
  async addAncillaryToContract(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.post(`ancillary` , payload.params)
        .then(response => {
          state.commit('ADD_NEW_ANCILLARY_TO_CONTRACT', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  },
  async updateContractStartdate(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.put(`contracts/update-date/${payload.id}` , payload.params)
        .then(response => {
          state.commit('UPDATE_CONTRACT_START_DATE', payload.params)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  },
  async updateContractDomain(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.put(`contracts/update-domain/${payload.id}` , payload.params)
        .then(response => {
          state.commit('UPDATE_CONTRACT_DOMAIN', payload.params)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  },
  async updateContractThemeLink(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.put(`contracts/update-theme/${payload.id}` , payload.params)
        .then(response => {
          state.commit('UPDATE_CONTRACT_THEME', payload.params)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  },
  async countContractInMonth(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.post('contractCount/month' , payload.form)
        .then(response => {
          state.commit('SET_CONTRACT_COUNT_IN_MONTH', response.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  },
  async addChecklistToContract(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.put(`assign-checklist/${payload.contract_id}` , payload.form)
        .then(response => {
          state.commit('ADD_CHECKLIST_TO_CONTRACT' , response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  },
}
