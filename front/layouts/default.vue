<template>
  <v-app dark>
    <v-app-bar app flat height="60">
      <a href="/">
        <v-img
          :src="require('~/assets/img/logo.png')"
          max-width="160"
        />
      </a>

      <v-spacer />
      <UrlInput />
      <v-spacer />

      <v-btn
        v-if="$route.path !== '/'"
        class="mr-3"
        style="border-color: #979797"
        outlined plain
        :to="{ path: '/' }"
      >
        トップ
      </v-btn>

      <div v-if="!$auth.loggedIn">
        <v-btn
          class="mr-3"
          style="border-color: #979797"
          outlined plain
          @click="drawer = true"
        >
          ログイン
        </v-btn>
      </div>

      <div v-if="$route.path !== '/mypage' && $auth.loggedIn">
        <v-btn
          class="mr-3"
          style="border-color: #979797"
          outlined plain
          :to="{ path: '/mypage' }"
        >
          マイページ
        </v-btn>
      </div>

      <v-menu offset-y>
        <template #activator="{ on, attrs }">
          <v-btn icon>
            <v-img
              v-if="$auth.loggedIn"
              :src="$auth.user.avatar ? $auth.user.avatar : require('~/assets/img/default_user.png')"
              class="rounded-circle"
              max-width="43"
              v-bind="attrs"
              v-on="on"
            />

            <v-icon
              v-else
              v-bind="attrs"
              v-on="on"
            >
              mdi-dots-vertical
            </v-icon>
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

          <v-list-item @click="drawer = true">
            <v-list-item-title>ログイン</v-list-item-title>
          </v-list-item>

          <v-list-item
            v-if="$auth.loggedIn"
            @click="logout()"
          >
            <v-list-item-title>ログアウト</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
    </v-app-bar>

    <v-main>
      <Nuxt />
    </v-main>

    <LoginDrawer
      :drawer="drawer"
      @close="close"
    />
  </v-app>
</template>

<script>
import UrlInput from '~/components/UrlInput.vue'
import LoginDrawer from '~/components/LoginDrawer.vue'

export default {
  name: 'DefaultLayout',
  components: {
    UrlInput, LoginDrawer
  },
  data: () => ({
    pages: [
      { title: 'トップ', path: '/' },
      { title: 'マイページ', path: '/mypage' },
      { title: '新規登録', path: '/register' },
    ],
    drawer: false
  }),
  methods: {
    logout() {
      try {
        this.$auth.logout()
        this.$toast.show('ログアウトしました')
      } catch (e) {
        this.$toast.show('ログアウトに失敗しました')
      }
    },
    close(bool) {
      this.drawer = bool
    }
  }
}
</script>
