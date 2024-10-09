export const state = () => ({
  sectionList: []
})

export const mutations = {
  SET_ADMIN_SECTION_LIST : (state, sectionList)  => {
    state.sectionList = sectionList;
  } ,
  UPDATE_ADMIN_SECTION_LIST : (state , newSection) =>{
      let sect = state.sectionList.find(x => x.id === newSection.id)
      sect.title = newSection.title
      sect.description = newSection.description
      sect.color = newSection.color
  }
}

export const getters = {
  getSectionTitle : (state) => (sectionId) =>{
    let section = state.sectionList.find(section => section.id === sectionId)
    if(section)
      return section.title
  } ,
  getSection : (state) => (sectionId) =>{
    return state.sectionList.find(section => section.id === sectionId)
  } ,
  // sectionWithoutDesigner : (state) => {
  //   return state.sectionList.splice(1, 1)
  // }
  getSpecialSections : (state) => (sections) => {
    if(sections){
      sections = sections.map(x => x.id)
      return state.sectionList.filter(item => {
        return sections.includes(item.id)
      })
    }
  },
}

export const actions = {
  async LoadAdminSections(state) {
    this.$axios.get('section')
      .then(response => {
        state.commit('SET_ADMIN_SECTION_LIST', response.data)
      })
      .catch(error => console.log(error))
  } ,

  async UpdateAdminSections(state , payload) {
    return new Promise((resolve, reject) => {
      this.$axios.put(`section/${payload.id}`, payload.form)
        .then(response => {
          state.commit('UPDATE_ADMIN_SECTION_LIST', response.data.data)
          resolve(response.data)
        })
        .catch(error => reject(error))
    });
  } ,

}
