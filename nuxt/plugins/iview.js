import Vue from 'vue'
import iView from 'view-design'
import locale from 'view-design/dist/locale/pt-PT' // Change locale, check node_modules/iview/dist/locale

Vue.use(iView, {locale})


export default ({ app }) => {
  app.router.beforeResolve((to, from, next) => {
    iView.LoadingBar.start();
    next()
  });

  app.router.beforeEach((to, from, next) => {
    iView.LoadingBar.start();
    next();
  });

  app.router.afterEach(route => {
    iView.LoadingBar.finish();
  });


  app.$axios.interceptors.request.use(function(config) {
    iView.LoadingBar.start();
    return config
  });

  app.$axios.interceptors.response.use(function(response) {
    iView.LoadingBar.finish();
    return response
  }, error => {
    iView.LoadingBar.error();
    return Promise.reject(error);
  });
}

