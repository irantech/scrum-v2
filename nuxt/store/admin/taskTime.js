export const state = () => ({
  taskTimeList: [] ,
})

export const mutations = {
  SET_ADMIN_TASK_TIME_LIST: (state, taskTimeList) => {
    state.taskTimeList = taskTimeList;
  },
  UPDATE_ADMIN_TASK_TIME_LIST: (state, newTask) => {
    let task = state.taskTimeList.find(x => x.id === newTask.id)
    task.checklist_id = newTask.checklist_id
    task.section_id = newTask.section_id
    task.task_status = newTask.task_status
    task.task_day_duration = newTask.task_day_duration
    task.task_time_duration = newTask.task_time_duration
  } ,
  ADD_NEW_TASK_TIME(state , payload) {
    state.taskTimeList.push(payload)
  } ,
  REMOVE_TASK_TIME_BY_ID(taskId) {
    let task = state.taskTimeList.find(task => task.id == taskId)
    task.trashed = true
  }
}

export const actions = {
  async LoadAdminTasks(state) {
    this.$axios.get('taskTime')
      .then(response => {
        state.commit('SET_ADMIN_TASK_TIME_LIST', response.data.data)
      })
      .catch(error => console.log(error))
  } ,

  async UpdateAdminTasks(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.put(`taskTime/${payload.id}`, payload.form)
        .then(response => {
          state.commit('UPDATE_ADMIN_TASK_TIME_LIST', response.data.data)
          resolve(response.data)
        })
        .catch(error => reject(error))
    });
  } ,

  async createNewTask(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.post(`taskTime`, payload.form)
        .then(response => {
          state.commit('ADD_NEW_TASK_TIME', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  },

  async removeTask(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.delete(`taskTime/${payload.id}`)
        .then(response => {
          state.commit('REMOVE_TASK_TIME_BY_ID', payload.id)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
}
