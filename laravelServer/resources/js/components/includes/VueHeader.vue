<template>
    <header class="sticky-top">
        <nav class="navbar navbar-expand-md navbar-dark bg-dark">
            <router-link to="/" class="navbar-brand"><img class="img-fluid" src="/images/logo.svg"></router-link>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault"
                    aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarsExampleDefault">
                <ul v-if="this.getCurrentRole() != null" class="nav navbar-nav ml-auto">
                    <li class="nav-item"><span class="nav-link text-light"><i class="fas fa-user-alt"></i></span></li>
                    <li class="nav-item">
                        <router-link to="/logout" class="btn btn-block btn-outline-warning">   خروج
                            <i class="fa-flip-both pb-1 fas fa-sign-out-alt"></i></router-link>
                    </li>
                </ul>
                <ul class="navbar-nav mr-auto" v-else >
                    <li class="nav-item">
                        <router-link class="btn btn-outline-info" to="/login">لطفا برای دسترسی وارد شوید</router-link>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
</template>

<script>
    export default{
        name: "VueHeader",
        data() {
            return {
                Laravel: Laravel
            }
        }
    }
</script>
