<template>
  <v-app>
    <v-app-bar app flat height="60">
      <router-link :to="{ path: '/' }">
        <v-img :src="require('~/assets/img/logo.png')" max-width="160" />
      </router-link>

      <v-spacer />
      <UrlInput />
      <v-spacer />

      <v-btn icon @click="darkMode = !darkMode">
        <v-icon>{{ themeIcon }}</v-icon>
      </v-btn>

      <v-menu offset-y>
        <template #activator="{ on, attrs }">
          <v-btn icon v-bind="attrs" v-on="on">
            <v-img
              v-if="$auth.loggedIn"
              :src="$auth.user.avatar ? $auth.user.avatar : require('~/assets/img/default_user.png')"
              class="rounded-circle"
              max-width="43"
            />

            <v-icon v-else>mdi-dots-vertical</v-icon>
          </v-btn>
        </template>

        <v-list>
          <v-list-item @click="about = !about">
            <v-list-item-title>サイト概要</v-list-item-title>
          </v-list-item>

          <v-list-item v-for="(page, index) in pages" :key="index" @click="moveMypage(page.path)">
            <v-list-item-title>{{ page.title }}</v-list-item-title>
          </v-list-item>

          <v-list-item @click="drawer = !drawer">
            <v-list-item-title>ログイン</v-list-item-title>
          </v-list-item>

          <v-list-item v-if="$auth.loggedIn" @click="logout()">
            <v-list-item-title>ログアウト</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
    </v-app-bar>

    <v-main>
      <Nuxt />
    </v-main>

    <LoginDrawer :show.sync="drawer" />

    <AboutDialog :show.sync="about" />

    <v-footer v-if="$route.path !== '/register'" padless>
      <v-col class="text-center" cols="12">©2022 — EggHam</v-col>
    </v-footer>
  </v-app>
</template>

<script>
import AuthMixin from '~/mixins/auth.js'
import UrlInput from '~/components/UrlInput.vue'
import LoginDrawer from '~/components/LoginDrawer.vue'
import AboutDialog from '~/components/AboutDialog.vue'

export default {
  name: 'DefaultLayout',
  components: { UrlInput, LoginDrawer, AboutDialog },
  mixins: [ AuthMixin ],
  data: () => ({
    pages: [
      { title: 'トップ', path: '/' },
      { title: 'マイページ', path: '/mypage' },
      { title: '新規登録', path: '/register' },
    ],
    darkMode: false,
    drawer: false,
    about: false,
  }),
  computed: {
    themeIcon() {
      return this.darkMode ? 'mdi-weather-night' : 'mdi-weather-sunny'
    },
  },
  watch: {
    darkMode(value) {
      this.$vuetify.theme.dark = value
    }
  },
  methods: {
    moveMypage(path) {
      if (path === '/mypage') {
        if (!this.$auth.loggedIn) {
          this.$toast.error('マイページに移動するにはログインが必要です。')
          return
        }
      }

      this.$router.push(path)
    }
  }
}
</script>
