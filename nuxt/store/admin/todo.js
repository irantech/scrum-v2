export const state = () => ({
  toDos : [] ,
  allUserTodos : [],
  todo_section_list : [] ,
  todo_user_list : []  ,
  status_count : []
})


export const getters = {
    filterTodoList : (state) => (section , taskStatus  , username , startDate , endDate , startTodoDate , endTodoDate) => {

        let userTodos  = state.allUserTodos



        if(section) {
            userTodos = userTodos.filter( user => {
                if(user.role.section) {
                  return user.role.section.id == section
                }
            })
        }

        if(username) {
          userTodos = userTodos.filter( user => {
            return user.id == username
          })
        }

        if(taskStatus) {

          userTodos = userTodos.filter( user => {
            const test = user.todo_lists.filter(todo =>  todo.status == taskStatus )
            return test.length != 0

          })
        }

        if (startDate) {
          userTodos = userTodos.filter( user => {
            const new_todo_list = [];
            user.todo_lists.forEach(todo=> {
              if(startDate <= todo.task.contract.sign_date){
                new_todo_list.push(todo)
              }
            })
            if(new_todo_list.length != 0 ) {
              user.todo_lists = new_todo_list
              return user
            }
          })
        }

        if (endDate) {
          userTodos = userTodos.filter( user => {
            const new_todo_list = [];
            user.todo_lists.forEach(todo=> {
              if(endDate >= todo.task.contract.sign_date){
                new_todo_list.push(todo)
              }
            })
            if(new_todo_list.length != 0 ) {
              user.todo_lists = new_todo_list
              return user
            }
          })
        }

        if (startTodoDate) {
          userTodos = userTodos.filter( user => {
            const new_todo_list = [];
            user.todo_lists.forEach(todo=> {

              if(todo.created_at >= startTodoDate ){
                new_todo_list.push(todo)
              }
            })

            if(new_todo_list.length != 0 ) {
              user.todo_lists = new_todo_list
              return user
            }
          })

        }

        if (endTodoDate) {
          userTodos = userTodos.filter( user => {
            const new_todo_list = [];
            user.todo_lists.forEach(todo=> {
              if(endTodoDate >= todo.created_at){
                new_todo_list.push(todo)
              }
            })
            if(new_todo_list.length != 0 ) {
              user.todo_lists = new_todo_list
              return user
            }
          })
        }

        return userTodos;
  } ,
    filterTodo : (state) => ( taskStatus , startTodoDate , endTodoDate , doneTaskStatus ) => {

    let todos  = state.toDos
    if(taskStatus) {

      todos = todos.filter( todo => {

        return  todo.status == taskStatus
      })
    }
    if (startTodoDate) {
      todos = todos.filter( todo => {
        return  todo.created_at >= startTodoDate
      })

    }

    if (endTodoDate) {
      todos = todos.filter( todo => {
        return endTodoDate >= todo.created_at
      })
    }
    if(doneTaskStatus) {
      console.log(doneTaskStatus)
      todos = todos.filter( todo => {
        console.log(todo)
        return  todo.todo_status == doneTaskStatus
      })
    }
    return todos;
  } ,

}


export const mutations = {
  SET_USER_TO_DOS(state , toDos){
    state.toDos = toDos
  },
  SET_USER_TO_DOS_STATUS_LIST(state , count) {
    state.status_count = count
  },
  SET_ALL_USER_TO_DOS(state , toDos){
    state.allUserTodos = toDos
  },
  SET_TO_DO_USER_LIST(state , toDos){
    state.todo_user_list = toDos
  },
  SET_TO_DOS_SECTION(state , toDos){
    const uniqueIds = [];
    state.todo_section_list = toDos.filter(todo => {
      if(todo.role.section) {
        const isDuplicate = uniqueIds.includes(todo.role.section.id);
        if (!isDuplicate) {
          uniqueIds.push(todo.role.section.id);

          return true;
        }
      }
      return false;
    });
    state.todo_section_list = state.todo_section_list.sort((a, b) => a.role.section.order - b.role.section.order);
  },
  UPDATE_TODO(state , todo_id) {
    let todo = state.toDos.find(a => a.id == todo_id)
    if(todo.status != 'in_progress') {

    }else{
      todo.status = 'done'
    }

  } ,
  CHANGE_TODO_TIME(state , payload){

    state.todo_user_list.forEach(user => {
        let todo = user.todo_lists.find( x => x.id == payload.id)
        if(todo) {
          todo.starting_time = payload.result.starting_time
          todo.ending_time = payload.result.ending_time
          todo.change_time_reason = payload.result.change_time_reason
          todo.difference_time = payload.result.difference_time
          todo.todo_status = payload.result.todo_status
        }
    })
  } ,
  CHANGE_TODO_STATUS(state , payload) {
    let todo = state.toDos.filter(x=> x.type == 'Task' && x.task_id == payload.task_id)
    if(todo) {
      let index = todo.length - 1
      todo[index].status = 'done'
      if(payload.todo) {
        todo[index].difference_time = payload.todo.difference_time
        todo[index].todo_status = payload.todo.todo_status
      }else{
        todo[index].difference_time = '0'
        todo[index].todo_status = 'best'
  }
    }
  } ,
  ADD_TASK_SUBTASK : (state , payload) => {
    console.log(payload)
    let todo = state.toDos.find(x => x.type == 'Task' && x.task_id == payload.taskId)
    console.log(todo)
    if(todo) {
      if(payload.newSubTask.parent)
      {
        let data =  findSubTask(task.sub_task , payload.newSubTask.parent)
        data.replies.push(payload.newSubTask)
      }
      else
        todo.task.not_assigned_sub_task.push(payload.newSubTask)
    }
    console.log(todo)
  },
  CHANGE_TODO_TASK : (state , payload) =>{
    let todo = state.toDos.find(x => x.id === payload.todo_id)
    if(todo) {
      todo.task = payload.task
    }
  }
}

export const actions = {
  async getToDoList(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.post('userTodoList'  ,payload)
        .then(response => {
          state.commit('SET_USER_TO_DOS', response.data.data)
          state.commit('SET_USER_TO_DOS_STATUS_LIST', response.data.status_count)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  },

  async updateToDoList(state , payload) {
    return new Promise((resolve, reject)  => {
      this.$axios.put(`todoList/${payload.id}` , payload.form)
        .then(response => {
          state.commit('UPDATE_TODO', payload.id)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,

  async changeToDoListTime(state , payload) {
    return new Promise((resolve, reject)  => {
      this.$axios.put(`change/todoList/${payload.id}` , payload.form)
        .then(response => {
          state.commit('CHANGE_TODO_TIME', { 'id' :  payload.id , 'result' :  response.data.data } )
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,

  async getUserToDoList(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.post('all/todoList' , payload)
        .then(response => {
          state.commit('SET_ALL_USER_TO_DOS', response.data.data)
          state.commit('SET_TO_DOS_SECTION', response.data.data)
          state.commit('SET_TO_DO_USER_LIST', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  }
}
