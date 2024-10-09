export const state = () => ({
  taskLabelList: [] ,
  singleTask : '' ,
  taskList: [] ,
  activeSubTask : '' ,
  selectedSubTask : [] ,
  showTask : false
})

let result = null ;
function findSubTask(list , subtask_id){
    for(let i = 0 ; i < list.length ; i ++ ) {

      if(list[i].id === subtask_id) {
        result = list[i]
        break;
      }

      findSubTask(list[i].replies  , subtask_id);
    }
    return  result ? result : null;

}
export const mutations = {
  SET_TASK_LABEL_LIST : (state, taskLabelList)  => {
    state.taskLabelList = taskLabelList;
  } ,
  SHOW_TASK : (state , value)  => {
    state.showTask = value;
  } ,
  SET_TASK_LIST : (state, taskList)  => {
    state.taskList = taskList;
  } ,
  SET_SINGLE_TASK : (state, task)  => {
    state.singleTask = task
  } ,
  FREE_SINGLE_TASK : (state)  => {
    console.log('FREE_SINGLE_TASK')
    state.singleTask = ''
  } ,
  UPDATE_TASK_LABEL : (state , newLabel) =>{
    let label = state.taskLabelList.find(x => x.id === newLabel.id)
    label.title = newLabel.title
    label.color = newLabel.color
  },

  UPDATE_TASK : (state , newTask) =>{
    let task = state.taskList.find(x => x.id === newTask.id)
    task.title = newTask.title
    task.description = newTask.description
    task.theme_link = newTask.theme_link
    task.site_link = newTask.site_link
    task.contract = newTask.contract
    task.label_list = newTask.label_list
  },

  CRAETE_TASK_LABEL : (state , newLabel) =>{
    state.taskLabelList.unshift(newLabel)
  },
  CREATE_TASK : (state , newTask) =>{
    state.taskList.unshift(newTask)
  },
  REMOVE_TASK_LABEL : (state , payload) => {
    let label = state.taskLabelList.find(label => label.id == payload.id)
    label.trashed = true
  },
  REMOVE_TASK : (state , payload) => {
   
    let task = state.taskList.find(task => task.id == payload.id.toString())

    task.status = 'complete'
  },
  RESTORE_TASK_LABEL : (state , payload) => {
    let label = state.taskLabelList.find(label => label.id == payload.id)
    label.trashed = false
  } ,
  ADD_TASK_SUBTASK : (state , payload) => {
    if(payload.newSubTask.parent)
    {
      let sub_task = []
      if(payload.user_id) {
        sub_task = state.singleTask.sub_task.find(user => user.id == payload.user_id)
        sub_task = sub_task.subTask
      }
      else{
        sub_task = state.singleTask.not_assigned_sub_task
      }
      let data =  findSubTask( sub_task , payload.newSubTask.parent)

      data.replies.push(payload.newSubTask)
    }
    else{
      state.singleTask.not_assigned_sub_task.push(payload.newSubTask)
    }
  },
  EDIT_TASK_SUBTASK: (state , payload) =>{

    let sub_task = []

    if(payload.user_id) {
      sub_task = state.singleTask.sub_task.find(user => user.id == payload.user_id)
      sub_task = sub_task.subTask
    }
    else{
      sub_task = state.singleTask.not_assigned_sub_task
    }

    let data =  findSubTask(sub_task , payload.newSubTask.id)
    data.status = payload.newSubTask.status
    data.body = payload.newSubTask.body
    data.section = payload.newSubTask.section
  },
  SET_ACTIVE_SUBTASK(state , data) {
    state.activeSubTask  = data
  },
  SET_TASK_DELIVERY_TIME : (state , data) => {
    let task = state.taskList.find(x => x.id === data.id)
    if(task) {
      task.delivery_time = data.delivery_time
      task.delivery_time_base = data.delivery_time_base
    }
    state.singleTask.delivery_time = data.delivery_time
    state.singleTask.delivery_time_base = data.delivery_time_base
  } ,
  SET_SELECTED_SUB_TASK_LIST : (state, selectedSubTask)  => {
    state.selectedSubTask = selectedSubTask;
  } ,
  RESET_SELECTED_SUBTASK_LIST : (state, selectedSubTask)  => {
    state.selectedSubTask = [];
  } ,
  UPDATE_TASK_ASSIGNED : (state , data) => {
    state.singleTask.sub_task = data.sub_task
    state.singleTask.not_assigned_sub_task = data.not_assigned_sub_task
  },
  CHANGE_TASK_LABEL : (state , data) => {
     state.singleTask.label_list = data.label_list
     let task = state.taskList.find(x => x.id === data.id)
     if(task) {
       task.label_list = data.label_list
     }

  }
}


export const actions = {
  async LoadTaskLabelList(state) {
    this.$axios.get('taskLabel')
      .then(response => {
        state.commit('SET_TASK_LABEL_LIST', response.data.data)
      })
      .catch(error => console.log(error))
  } ,

  async UpdateTaskLabel(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.put(`taskLabel/${payload.id}`, payload.form)
        .then(response => {
          state.commit('UPDATE_TASK_LABEL', response.data.data)
          resolve(response.data)
        })
        .catch(error => reject(error))
    });
  } ,

  async UpdateTask(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.put(`task/${payload.id}`, payload.form)
        .then(response => {
          state.commit('UPDATE_TASK', response.data.data)
          resolve(response.data)
        })
        .catch(error => reject(error))
    });
  } ,

  async createNewTaskLabel(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.post('taskLabel', payload.form)
        .then(response => {
          state.commit('CRAETE_TASK_LABEL', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  },

  async removeTaskLabel(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.delete(`taskLabel/${payload.id}`)
        .then(response => {
          state.commit('REMOVE_TASK_LABEL', payload)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async removeTask(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.delete(`task/${payload.id}`)
        .then(response => {
          state.commit('REMOVE_TASK', payload)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,

  async restoreTaskLabel(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.put(`taskLabel/${payload.id}/restore`)
        .then(response => {
          state.commit('RESTORE_TASK_LABEL', payload)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,

  // async LoadTaskList(state) {
  //   this.$axios.get('task')
  //     .then(response => {
  //       state.commit('SET_TASK_LIST', response.data.data)
  //     })
  //     .catch(error => console.log(error))
  // },

  async LoadTaskList(state , payload) {
    return new Promise((resolve, reject) => {
    this.$axios.post('getTask' ,  payload)
      .then(response => {
        state.commit('SET_TASK_LIST', response.data.data)
        resolve(response)
      })
      .catch(error => console.log(error))
    });
  } ,
  async LoadSingleTask(state , payload) {
    state.commit('FREE_SINGLE_TASK')
    return new Promise((resolve, reject) => {
      this.$axios.get(`task/${payload.id}`)
        .then(response => {
          state.commit('SET_SINGLE_TASK', response.data.data)
          resolve(response)
        })
        .catch(error => console.log(error))
    });
  },
  async createNewTask(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.post('task', payload.form)
        .then(response => {
          state.commit('CREATE_TASK', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  },

  async updateTaskDeliveryTime(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.put(`task/${payload.task_id}/setTime`, payload.form)
        .then(response => {
          state.commit('SET_TASK_DELIVERY_TIME', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  },

  async assignSubTaskToUser(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.put(`assignSubTask/${payload.task_id}`, payload.form)
        .then(response => {
          state.commit('UPDATE_TASK_ASSIGNED', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  },

  async changeTaskLabel(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.put(`changeTaskLabel/${payload.task_id}`, payload.form)
        .then(response => {
          state.commit('CHANGE_TASK_LABEL', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  },
  async changeTaskSection(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.put(`changeTaskSection/${payload.task_id}`, payload.form)
        .then(response => {
          // state.commit('CHANGE_TASK_LABEL', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  },


}
