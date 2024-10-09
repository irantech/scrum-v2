export default {
    state: () => ({
        allBaseProgress : []
    }),
    mutations: {
        SET_ALL_BASE_PROGRESS : (state , allBaseProgress) => {
            state.allBaseProgress = allBaseProgress
        }
    },
    actions: {
        getAllBaseProgress( { commit } ) {
            return new Promise((resolve, reject) => {
               return  axios.get('base-progress/all')
                    .then(response => {
                        commit('SET_ALL_BASE_PROGRESS', response.data.data)
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        createNewBaseProgress( { commit } , payload ) {
            return new Promise((resolve, reject) => {
               axios.post('base-progress', payload.form)
                   .then(response => {
                        resolve(response)
                   })
                   .catch(error => {
                       reject(error);
                   })
            });
        } ,

    },
    getters: {
    }
}
