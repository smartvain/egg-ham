<template>
  <v-navigation-drawer
    v-model="_drawer"
    :width="width <= 767 ? '100%' : '27%'"
    right temporary fixed
  >
    <v-card class="px-2" height="100%" flat>
      <v-btn
        icon right fixed
        @click="_drawer = false"
      >
        <v-icon>mdi-close</v-icon>
      </v-btn>

      <v-img
        :src="require('~/assets/img/logo.png')"
        max-width="70%"
        min-height="100"
        class="mx-auto"
        contain
      />

      <v-card-text class="py-0">
        <v-btn
          class="text-capitalize caption"
          color="#00ACEE"
          height="48px"
          rounded dark depressed block
          @click="twitterLogin()"
        >
          <v-img
            class="mr-4"
            :src="require('~/assets/img/twitter_logo_white.png')"
            max-width="24"
          />
          twitterでログイン
        </v-btn>

        <v-btn
          class="text-capitalize caption mt-5"
          style="border-color: #979797"
          height="48px"
          :loading="loading.googleLogin"
          rounded outlined block
          @click="googleLogin()"
        >
          <v-img
            class="mr-4"
            src="https://madeby.google.com/static/images/google_g_logo.svg"
            max-width="24"
          />
          Googleでログイン
        </v-btn>

        <p class="text-center mt-5 text-subtitle-1">
          メールアドレスでログイン
        </p>
        
        <ValidationObserver v-slot="{ passes, validate }">
          <ValidationProvider
            v-slot="{ errors }"
            rules="required|max:20"
            name="メールアドレス"
            mode="passive"
          >
            <v-text-field
              v-model="email"
              append-icon="mdi-email"
              placeholder="メールアドレス"
              type="text"
              outlined dense
              :error-messages="errors"
            />
          </ValidationProvider>

          <ValidationProvider
            v-slot="{ errors }"
            rules="required|max:20"
            name="パスワード"
            mode="passive"
          >
            <v-text-field
              v-model="password"
              append-icon="mdi-lock"
              placeholder="パスワード"
              type="password"
              outlined dense
              :error-messages="errors"
            />
          </ValidationProvider>

          <v-checkbox v-model="remember" class="mt-n2">
            <template #label>
              <span class="text-caption">ログインしたままにする</span>
            </template>
          </v-checkbox>

          <div class="text-center">
            <v-btn
              class="primary"
              rounded block
              :loading="loading.login"
              @click="validate().then(passes(login))"
            >
              <span class="text-subtitle-1 font-weight-bold">ログイン</span>
            </v-btn>
          </div>
        </ValidationObserver>
        
        <v-divider class="my-4" />

        <v-row>
          <v-col cols="12" align="center">
            <span>アカウント登録はお済みですか？</span>
            <router-link class="font-weight-bold" :to="{ path: '/register' }">
              新規登録
            </router-link>
          </v-col>
        </v-row>

        <v-btn
          class="pa-0 mb-8"
          absolute bottom right plain small
        >
          パスワードをお忘れの方
          <v-icon>mdi-chevron-right</v-icon>
        </v-btn>

        <v-btn
          v-if="$auth.loggedIn"
          class="pa-0"
          absolute bottom right plain small
          @click="logout()"
        >
          ログアウト
          <v-icon>mdi-logout</v-icon>
        </v-btn>
      </v-card-text>
    </v-card>
  </v-navigation-drawer>
</template>

<script>
export default {
  props: {
    drawer: { type: Boolean, default: false }
  },
  data: () => ({
    email: null,
    password: null,
    remember: false,
    loading: {
      login: false,
      googleLogin: false,
      twitterLogin: false
    },
    width: window.innerWidth,
  }),
  computed: {
    _drawer: {
      get() {
        return this.drawer
      },
      set(value) {
        this.$emit('close', value)
      }
    }
  },
  mounted() {
    window.addEventListener('resize', () => this.width = window.innerWidth)
    console.log(this.$auth.user)
    if (!this.$auth.loggedIn) {
      this.email = 'example@eggham.com'
      this.password = 'hogehoge'
    }
  },
  methods: {
    async login() {
      if (this.$auth.loggedIn) {
        this.$toast.show('すでにログインしています。')
        return
      }
      
      this.loading.login = true
      
      try {
        const res = await this.$auth.loginWith('local', { data: {
          email: this.email,
          password: this.password
        }})
        this._drawer = false
        this.$toast.show(res.data.message)
      } catch (e) {
        this.$toast.error('ログインに失敗しました。もう一度お試しください。')
      }

      this.loading.login = false
    },
    async googleLogin() {
      if (this.$auth.loggedIn) {
        this.$toast.show('すでにログインしています。')
        return
      }
      
      this.loading.googleLogin = true

      try {
        await this.$auth.loginWith('google')
      } catch (e) {
        this.$toast.error(e.message)
      }

      this.loading.googleLogin = false
    },
    async twitterLogin() {
      if (this.$auth.loggedIn) {
        this.$toast.show('すでにログインしています。')
        return
      }
      
      this.loading.twitterLogin = true

      try {
        // await this.$auth.loginWith('twitter')
        const redirectUrl = await this.$axios.$get('oauth/twitter/redirect')
        window.location.href = redirectUrl
      } catch (e) {
        this.$toast.error(e.message)
      }

      this.loading.twitterLogin = false
    },
    logout() {
      try {
        this.$auth.logout()
        this.$toast.show('ログアウトしました')
      } catch (e) {
        this.$toast.show('ログアウトに失敗しました')
      }
    },
  }
}
</script>

<style>

</style>