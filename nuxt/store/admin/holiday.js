import {indexOf} from "core-js/internals/array-includes";

export const state = () => ({
  holidayList: [] ,
})

export const mutations = {
  SET_ADMIN_HOLIDAY_LIST: (state, holidayList) => {
    state.holidayList = holidayList;
  },
  UPDATE_ADMIN_HOLIDAY_LIST: (state, newHoliday) => {
    let holiday = state.holidayList.find(x => x.id === newHoliday.id)
    holiday.title = newHoliday.title
    holiday.date = newHoliday.date
  } ,
  ADD_NEW_HOLIDAY(state , payload) {
    state.holidayList.push({
      'title' : payload.title ,
      'date' : payload.date ,
    })
  } ,
  REMOVE_HOLIDAY_BY_ID(state , holidayId) {
    let holiday = state.holidayList.find(holiday => holiday.id == holidayId)
    console.log(holiday)
    let index = state.holidayList.indexOf(holiday);
    console.log(index)
    state.holidayList.splice(index, 1);
  }
}

export const actions = {
  async LoadAdminHoliday(state) {
    this.$axios.get('holiday')
      .then(response => {
        state.commit('SET_ADMIN_HOLIDAY_LIST', response.data)
      })
      .catch(error => console.log(error))
  } ,

  async UpdateAdminHoliday(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.put(`holiday/${payload.id}`, payload.form)
        .then(response => {
          state.commit('UPDATE_ADMIN_HOLIDAY_LIST', response.data.data)
          resolve(response.data)
        })
        .catch(error => reject(error))
    });
  } ,

  async createNewHoliday(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.post(`holiday`, payload.form)
        .then(response => {
          state.commit('ADD_NEW_HOLIDAY', payload.form)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  },

  async removeHoliday(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.delete(`holiday/${payload.id}`)
        .then(response => {
          state.commit('REMOVE_HOLIDAY_BY_ID', payload.id)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
}
