<template>
  <v-app dark>
    <v-app-bar app elevation="2" height="60">
      <a href="/">
        <v-img
          :src="require('~/assets/img/logo.png')"
          max-width="160"
          max-height="100%"
          contain
        />
      </a>

      <v-spacer />
      <UrlInput v-if="$route.path === '/'" />
      <v-spacer />

      <div v-if="$route.path !== '/'">
        <v-btn
          class="mr-3"
          outlined plain
          :to="{ path: '/' }"
        >
          TOP
        </v-btn>
      </div>

      <v-menu offset-y>
        <template v-slot:activator="{ on, attrs }">
          <v-btn icon>
            <v-img
              :src="require('~/assets/img/default_icon.png')"
              class="rounded-circle"
              max-width="43"
              v-bind="attrs"
              v-on="on"
            />
          </v-btn>
        </template>

        <v-list>
          <v-list-item
            v-for="(page, index) in pages"
            :key="index"
            :to="page.path"
          >
            <v-list-item-title>{{ page.title }}</v-list-item-title>
          </v-list-item>

          <v-list-item
            @click="logout()"
            v-if="$auth.loggedIn"
          >
            <v-list-item-title>ログアウト</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
    </v-app-bar>

    <v-main>
      <Nuxt />
    </v-main>
  </v-app>
</template>

<script>
import UrlInput from '~/components/UrlInput.vue'

export default {
  name: 'DefaultLayout',
  components: {
    UrlInput
  },
  data: () => ({
    pages: [
      { title: 'ログイン', path: '/login' },
      { title: 'マイページ', path: '/mypage' },
      { title: '設定', path: '/mypage/setting' },
    ]
  }),
  methods: {
    logout() {
      try {
        this.$auth.logout()
        this.$toast.show('ログアウトしました')
      } catch (e) {
        this.$toast.show('ログアウトに失敗しました')
      }
      
    }
  }
}
</script>
