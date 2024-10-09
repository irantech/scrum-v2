export default {
    state: () => ({
        allSoftwares: []
    }),
    mutations: {
        SET_ALL_SOFTWARES : (state , allSoftwares) => {
            state.allSoftwares = allSoftwares
        }
    },
    actions: {
        getAllSoftwares( { commit } ) {
            return new Promise((resolve, reject) => {
                return axios.get('softwares')
                    .then(response => {
                        commit( 'SET_ALL_SOFTWARES' , response.data )
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
