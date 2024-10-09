export const state = () => ({
  checklistContract : {} ,
  contractTitleChecklist : [] ,
  contractTitleChecklistSection : [] ,
  reverseChecklistProcess : [] ,
  checklistProcessList : [] ,
  signedSections : [],
  activeReverse : '',
  result : '' ,
  checklist_process_duration : []

})
let result = null ;
function findReverse(list , checklist_id){
  for(let i = 0 ; i < list.length ; i ++ ) {
    if(list[i].id === checklist_id) {
      result = list[i]
      break;
    }
    findReverse(list[i].replies  , checklist_id);
  }
  return  result ? result : null;
}
export const mutations = {
  SET_CHECKLIST_PROCESS_DURATION(state , data)
  {
    state.checklist_process_duration = data
  },
  SET_ACTIVE_REVERSE(state , data) {
    state.activeReverse  = data
  },
  SET_CHECK_LIST_CONTRACT(state , checklistContract) {
    state.checklistContract = checklistContract
  } ,
  SET_CONTRACT_TITLE_CHECKLIST : (state , titleChecklists) =>{
    state.contractTitleChecklist = titleChecklists
    let userList = [] ;
    for(let key in state.contractTitleChecklist) {
      let titleChecklists = state.contractTitleChecklist[key]
      for (let i = 0 ; i <  titleChecklists.length ; i++){
        if(userList.find(user => user.id === titleChecklists[i].user.id))
          break;
        else
          userList.push(titleChecklists[i].user)
      }
    }
    state.contractTitleChecklistUser = userList;
  },
  SET_CONTRACT_TITLE_CHECKLIST_SECTION : (state  , sections) => {
    state.contractTitleChecklistSection = sections
  } ,
  SET_TITlE_CHECKLIST_STATUS : (state , payload ) => {
    let titlechecklist  = state.contractTitleChecklist[payload.titleChecklist]
    let title = titlechecklist.find(title => title.section.id === payload.form.section)
    if (title.status === 0 ) title.status = 1
    else title.status = 0
  } ,
  SET_CHECKLIST_PROCESS_REVERSE : (state , reverseProcess) =>{
    state.reverseChecklistProcess = reverseProcess
  },
  ADD_CHECKLIST_PROCESS_REVERSE : (state , reverseItem) => {
    if(reverseItem.length > 1 ){
      reverseItem.forEach(x => {
        state.reverseChecklistProcess.push(x)
      })
    }else{
     state.reverseChecklistProcess.push(reverseItem)
    }
  },
  ADD_CHECKLIST_PROCESS_REVERSE_DATA : (state , payload) => {
    let reverse = state.reverseChecklistProcess.find(x => x.id === payload.processId)
    if(payload.newReverse.parent)
    {
      let data =  findReverse(reverse.reverse_data , payload.newReverse.parent)
      data.replies.push(payload.newReverse)

    }
    else
      reverse.reverse_data.push(payload.newReverse)
  },
  EDIT_CHECKLIST_PROCESS_REVERSE_DATA: (state , payload) =>{
    let reverse = state.reverseChecklistProcess.find(x => x.id === payload.processId)
    let data =  findReverse(reverse.reverse_data , payload.newReverse.id)
    data.status = payload.newReverse.status
    data.body = payload.newReverse.body
    data.section = payload.newReverse.section
  },
  SEEN_CHECKLIST_PROCESS_REVERSE_DATA :  (state , payload) =>{
      let reverse = state.reverseChecklistProcess.find(x => x.id === payload.processId)
      let data =  reverse.reverse_data.find(x => x.id === payload.reverseId)
      if(data.seen == 0) {
        data.seen = 1
      }else {
        data.seen = 0
      }
  },
  DELETE_CHECKLIST_PROCESS_REVERSE_ATTACH : (state , payload) => {
    let reverse = state.reverseChecklistProcess.find(x => x.id === payload.processId)
    let data =  findReverse(reverse.reverse_data , payload.newReverse.id)
    data.file_list = payload.newReverse.file_list
  },
  SET_TITLE_CHECKLIST_SECTION : (state  , payload ) =>{
    let section = state.contractTitleChecklistSection.find(titleChecklist => titleChecklist.section.id === payload.form.section)
    if(section) {
      section.user.id =  payload.form.user
    }
    else
      state.contractTitleChecklistSection.push({
        section : {
          id : payload.form.section
        } ,
        user : {
          id  : payload.form.user
        }
      })
  } ,
  SET_CHECKLIST_PROCESS_LIST : (state , processList) =>{
    state.checklistProcessList = processList
  },
  SET_CHECKLIST_SIGNED_SECTION:(state , signedList) =>{
      state.signedSections = signedList
  } ,
  SET_SECTION_SIGNED : (state , data ) => {
    console.log(data.status)
    let status ;
    switch (data.status){
      case 'manager' :
       status = 4;
        break;
      case 'staff' :
        status = 5 ;
        break;
      case 'support' :
        status = 6;
        break;
    }
    state.signedSections.push({
      section : data.section ,
      user : data.user,
      status : status
    })
  }
}

export const getters = {
  getSectionDuration : (state) => (section) => {
    if(state.checklist_process_duration){
      let time = state.checklist_process_duration.find( x => x.section.id === section)
      if(time)
        return time.sumTime
    }
    return 0
  },
  getBySection : (state) => (section) => {
    if(section)
      return state.checklistContract.checklist.title_checklists.filter(title => {
        let newTitle = title.section.map(sect => sect.id)
        return  newTitle.includes(section)
      })
    else
      return Object.keys(state.checklistContract).length !== 0 ? state.checklistContract.checklist.title_checklists : ''
  } ,
  getSectionUser : (state) => (sectionId) =>{
    return state.contractTitleChecklistSection.find(titleChecklist =>titleChecklist.section.id === sectionId)
  },
  canUser : (state , getters , rootState) => (sectionId) => {
    let titleChecklist = getters.getSectionUser(sectionId)
    if(titleChecklist)
      return  titleChecklist.user.id === rootState.auth.user.id;
  } ,
  allTitleChecked : (state) => (sectionId) => {
    let titleChecklistSection = []

    for(let key in state.contractTitleChecklist) {
      let titleChecklist = state.contractTitleChecklist[key]
      titleChecklist.filter(title => title.section.id === sectionId ? titleChecklistSection.push(title) : '')
    }
    for (let title  in titleChecklistSection ) {
      if(titleChecklistSection[title].status === 0)
        return false
    }
    return true
  } ,
  hasSection : (state) => (sectionId , titleChecklist) =>{
    let isExist = titleChecklist.filter(title => title.section.id === sectionId)
    return isExist.length !== 0;

  },
  isInSection : (state) => (sectionId) =>{
    let titleChecklistSection = state.contractTitleChecklistSection.filter(titleChecklist => titleChecklist.section.id === sectionId)
    return titleChecklistSection !== '';

  } ,
  getSignedBySection : (state) => (sectionId , status) =>{
    let sign =  state.signedSections.find(sign => sign.section.id === sectionId && sign.status === status)
    if(sign) {
      sign.user.sign_time = sign.created_at
    }
    return sign ? sign.user : ''
  }
}

export const actions = {
  async getContractChecklist(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`checklist-contract/${payload.checklistContract}`)
        .then(response => {
          state.commit('SET_CHECK_LIST_CONTRACT', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async getContractTitleChecklists(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`all/checklist-contract/${payload.checklistContract}`)
        .then(response => {
          state.commit('SET_CONTRACT_TITLE_CHECKLIST', response.data.titleChecklists)
          state.commit('SET_CONTRACT_TITLE_CHECKLIST_SECTION' , response.data.section)
          state.commit('SET_CHECKLIST_SIGNED_SECTION' , response.data.signed)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async sumChecklistProcessDuration(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`sum/checklist-contract/${payload.checklistContract}`)
        .then(response => {
          state.commit('SET_CHECKLIST_PROCESS_DURATION', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async changeContractTitleChecklistStatus(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.post(`checklist-contract/${payload.checklistContract}/titleChecklist/${payload.titleChecklist}` , payload.form)
        .then(response => {
          state.commit('SET_TITlE_CHECKLIST_STATUS', payload)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async getReverseChecklistProcesses(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`reverse/checklist-contract/${payload.checklistContract}`)
        .then(response => {
          state.commit('SET_CHECKLIST_PROCESS_REVERSE', response.data.data)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
  async assignUserToTitleChecklist(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.post(`assignUser/${payload.contract_checklist_id}` , payload.form)
        .then(response => {
          state.commit('SET_TITLE_CHECKLIST_SECTION', payload)
          resolve(response)
        })
        .catch(error => reject(error))
    });
  } ,
}
