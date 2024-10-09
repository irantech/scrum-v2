export default {
    state: () => ({
        allSections: []
    }),
    mutations: {
        SET_ALL_SECTIONS : (state , allSections) => {
            state.allSections = allSections
        }
    },
    actions: {
        getAllSections( { commit } ) {
            return new Promise((resolve, reject) => {
                return  axios.get('section')
                    .then(response => {
                        commit('SET_ALL_SECTIONS' , response.data)
                    })
                    .catch(error => {
                        reject(error);

                    })
            });
        }
    },
    getters: {
    }
}
