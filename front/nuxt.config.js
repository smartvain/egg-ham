import colors from 'vuetify/es5/util/colors'

export default {
  // Disable server-side rendering: https://go.nuxtjs.dev/ssr-mode
  ssr: false,

  // Global page headers: https://go.nuxtjs.dev/config-head
  head: {
    titleTemplate: '%s - EggHam',
    title: 'EggHam',
    htmlAttrs: {
      lang: 'ja',
    },
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      { hid: 'description', name: 'description', content: '' },
      { name: 'format-detection', content: 'telephone=no' },
    ],
    link: [{ rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }],
  },

  // Global CSS: https://go.nuxtjs.dev/config-css
  css: [],

  // Plugins to run before rendering page: https://go.nuxtjs.dev/config-plugins
  plugins: [
    '~/plugins/vee-validate',
    '~/plugins/axios'
  ],

  // Auto import components: https://go.nuxtjs.dev/config-components
  components: true,

  // Modules for dev and build (recommended): https://go.nuxtjs.dev/config-modules
  buildModules: [
    '@nuxtjs/eslint-module',
    '@nuxtjs/vuetify',
    '@nuxtjs/moment'
  ],

  // Modules: https://go.nuxtjs.dev/config-modules
  modules: [
    '@nuxtjs/axios',
    "@nuxtjs/auth-next",
    '@nuxtjs/toast'
  ],

  moment: {
    locales: ['ja']
  },

  auth: {
    redirect: {
      login: '/',
    },
    strategies: {
      local: {
        token: {
          property: 'token',
          maxAge: 60 * 60,
          global: true,
          // required: true,
          type: 'Bearer'
        },
        user: {
          property: 'user',
          // autoFetch: true
        },
        endpoints: {
          login: { url: '/login', method: 'post' },
          user: { url: '/user', method: 'get' },
          logout: false
        }
      },
      google: {
        token: {
          property: 'token',
          global: true,
        },
        user: {
          property: 'user',
        },
        clientId: process.env.GOOGLE_CLIENT_ID,
        codeChallengeMethod: '',
        responseType: 'code',
        redirectUri: process.env.GOOGLE_REDIRECT_URL,
        endpoints: {
          token: process.env.GOOGLE_FETCH_TOKEN_URL,
          userInfo: process.env.GOOGLE_FETCH_USER_URL
        }
      }
    },
    localStorage: false,
  },

  toast: {
    duration: 5000,
    position: 'bottom-left',
    singleton: true
  },

  // Axios module configuration: https://go.nuxtjs.dev/config-axios
  axios: {
    // Workaround to avoid enforcing hard-coded localhost:3000: https://github.com/nuxt-community/axios-module/issues/308
    prefix: '/api/',
    proxy: true,
  },

  proxy: {
    '/api/': 'http://nginx'
  },

  // Vuetify module configuration: https://go.nuxtjs.dev/config-vuetify
  vuetify: {
    customVariables: ['~/assets/variables.scss'],
    theme: {
      dark: false,
      themes: {
        dark: {
          primary: colors.blue.darken2,
          accent: colors.grey.darken3,
          secondary: colors.amber.darken3,
          info: colors.teal.lighten1,
          warning: colors.amber.base,
          error: colors.deepOrange.accent4,
          success: colors.green.accent3,
        },
      },
    },
  },

  // Build Configuration: https://go.nuxtjs.dev/config-build
  build: {},
}
