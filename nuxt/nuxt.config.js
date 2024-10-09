export default {
  // Global page headers: https://go.nuxtjs.dev/config-head
  head: {
    title: 'scrum-irantech-ui',
    htmlAttrs: {
      lang: 'en'
    },
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      { hid: 'description', name: 'description', content: '' },
      { name: 'format-detection', content: 'telephone=no' }
    ],
    link: [
      { rel: 'icon', type: 'image/x-icon', href: '/favicon.png' },
      { rel: 'stylesheet', href: 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css' }
    ]
  },

  // Global CSS: https://go.nuxtjs.dev/config-css
  css: [
    'view-design/dist/styles/iview.css',
    '~/assets/sass/app.scss',
    '~/assets/sass/iview/custom.scss',
    '~/assets/sass/font/_iransans.scss',
    '~/assets/css/fontawesome.min.css',
  ],

  // Plugins to run before rendering page: https://go.nuxtjs.dev/config-plugins
  plugins: [
    { src : '~/plugins/iview.js', ssr: false} ,
    { src : '~/plugins/axios.js', ssr: true} ,
    { src : '~/plugins/datetimePicker.js', ssr: false} ,
    { src : '~/plugins/jmoment.js', ssr: true},
    { src : '~/plugins/editor.js', ssr: true},
    { src : '~/plugins/fileUpload.js', ssr: false},
    { src : '~/plugins/vue-json-excel.js', ssr: false}
  ],

  // Auto import components: https://go.nuxtjs.dev/config-components
  components: true,

  // Modules for dev and build (recommended): https://go.nuxtjs.dev/config-modules
  buildModules: [
  ],

  // Modules: https://go.nuxtjs.dev/config-modules
  modules: [
    '@nuxtjs/moment',
    '@nuxtjs/axios' ,
    '@nuxtjs/markdownit',
    ['cookie-universal-nuxt', { alias: 'cookies' } , { parseJSON: false }],
    ['nuxt-env', {
      keys: ['UPLOAD_URL' , 'API_URL']
    }]
  ],
  markdownit: {
    runtime: true // Support `$md()`
  },
  moment: {
    locales: ['fa']
  },
  // axios options
  axios: {
    baseURL: process.env.API_URL, // Used as fallback if no runtime config is provided
  },
  env: {
    BASE_URL: process.env.API_URL,
    UPLOAD_URL: process.env.UPLOAD_URL
  },
  // Build Configuration: https://go.nuxtjs.dev/config-build
  build: {
  }
}
