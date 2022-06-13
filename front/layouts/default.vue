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
          <v-list-item @click="about = true">
            <v-list-item-title>サイト概要</v-list-item-title>
          </v-list-item>

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

    <v-dialog
      v-model="about"
      width="670"
    >
      <v-card>
        <v-card-title class="text-h6 font-weight-bold">
          サイト概要
        </v-card-title>

        <v-divider></v-divider>
        
        <v-card-title :class="aboutTitleClass">
          当サイトについて
        </v-card-title>
        <v-card-text>
          EggHamはWelio様の「TOEFL®に出る単語リスト」を参考にYouTube動画に含まれているTOEFL単語を探し出し、すぐに単語の意味を調べられるサイトです。
          アカウントを作成すると、単語を保存することができます。
        </v-card-text>

        <v-card-title :class="aboutTitleClass">
          アクセス解析ツールについて
        </v-card-title>
        <v-card-text>
          当サイトでは、Googleによるアクセス解析ツール「Googleアナリティクス」を利用しています。
          このGoogleアナリティクスはトラフィックデータの収集のためにクッキー（Cookie）を使用しております。
          トラフィックデータは匿名で収集されており、個人を特定するものではありません。
        </v-card-text>

        <v-card-title :class="aboutTitleClass">
          お問い合わせ
        </v-card-title>
        <v-card-text>
          こちらのGoogleFormsからお問い合わせお願いします。
        </v-card-text>

        <v-card-title :class="aboutTitleClass">
          開発者について
        </v-card-title>
        <v-card-text>
          Twitter: _________
        </v-card-text>
        <v-card-text>
          GitHub: _________
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="primary"
            text
            @click="about = false"
          >
            閉じる
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-footer v-if="$route.path !== '/register'" padless>
      <v-col
        class="text-center"
        cols="12"
      >
        <span>©</span> {{ new Date().getFullYear() }} — <span>EggHam</span>
      </v-col>
    </v-footer>
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
    about: false,
    drawer: false,
    aboutTitleClass: 'text-subtitle-1 font-weight-bold'
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
