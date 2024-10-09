<template>
    <div id="contracts" class="contracts ">
        <div class="card">
            <h4 class="card-header">قراردادهای شما</h4>
            <div class="card-body">
                <div class="card-text list-group list-group-flush">
                    <contract-box v-for="(contract,index) in contracts" :key="index"
                                  :contract="contract"></contract-box>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import contractBox from './ContractBox.vue';
    export default{
        name: "Contracts",
        components: {'contractBox': contractBox},
        data(){
            return {
                contracts: null,
                loading: true,
                tokens: null,
                scopes: null,
                headers: {}
            }
        },
        mounted(){
            this.Authorized();
            this.prepareComponent();
        },

        methods: {
            /**
             * Prepare the component.
             */
            prepareComponent() {

//                this.getTokens();
//                this.getScopes();
                this.getComponentData()
            },


            /**
             * Get all of the personal access tokens for the user.
             */

            getTokens() {
                axios.get('/oauth/personal-access-tokens', {baseURL: Laravel.baseUrl})
                    .then(response => {
                        this.tokens = response.data;
                    });
            },

            getComponentData(){
                axios
                    .get('contractss')
                    .then(response => this.contracts = response.data)
                    .catch(error => console.log(error))
                    .finally(() => this.loading = false)
            },

            /**
             * Get all of the available scopes.
             */
            getScopes() {
                axios.get('oauth/scopes')
                    .then(response => {
                        this.scopes = response.data;
                    });
            },

            /**
             * Show the form for creating new tokens.
             */
            showCreateTokenForm() {
                $('#modal-create-token').modal('show');
            },

            /**
             * Create a new personal access token.
             */
            store() {
                this.accessToken = null;

                this.form.errors = [];

                axios.post('/oauth/personal-access-tokens', this.form)
                    .then(response => {
                        this.form.name = '';
                        this.form.scopes = [];
                        this.form.errors = [];

                        this.tokens.push(response.data.token);

                        this.showAccessToken(response.data.accessToken);
                    })
                    .catch(error => {
                        if (typeof error.response.data === 'object') {
                            this.form.errors = _.flatten(_.toArray(error.response.data.errors));
                        } else {
                            this.form.errors = ['Something went wrong. Please try again.'];
                        }
                    });
            },

            /**
             * Toggle the given scope in the list of assigned scopes.
             */
            toggleScope(scope) {
                if (this.scopeIsAssigned(scope)) {
                    this.form.scopes = _.reject(this.form.scopes, s => s == scope);
                } else {
                    this.form.scopes.push(scope);
                }
            },

            /**
             * Determine if the given scope has been assigned to the token.
             */
            scopeIsAssigned(scope) {
                return _.indexOf(this.form.scopes, scope) >= 0;
            },

            /**
             * Show the given access token to the user.
             */
            showAccessToken(accessToken) {
                $('#modal-create-token').modal('hide');

                this.accessToken = accessToken;

                $('#modal-access-token').modal('show');
            },

            /**
             * Revoke the given token.
             */
            revoke(token) {
                axios.delete('/oauth/personal-access-tokens/' + token.id)
                    .then(response => {
                        this.getTokens();
                    });
            }
        }

    }
</script>

<style scopped>

    .contracts {
        text-align: right;
    }

    .contracts .card {
    }


</style>
