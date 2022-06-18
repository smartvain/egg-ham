<template>
<v-container fill-height>
  <span class="bg"></span>
  <v-row>
    <v-col cols="6">
      <h1 class="register-title text-h3 font-weight-bold">
        EggHamへようこそ。
      </h1>

      <p class="register-text text-subtitle-1">
        EggHamはYouTube動画に含まれているTOEFL単語を探し出し、<br>
        すぐに単語の意味を調べられるサイトです。
      </p>

      <v-btn
        :to="{ path: '/' }"
        class="register-action-text text-subtitle-1"
        plain nuxt
      >
        <v-icon>mdi-chevron-left</v-icon>
        アカウント登録せずに利用する
      </v-btn>
    </v-col>
    
    <v-col cols="6" align="center">
      <v-card class="px-8" elevation="5" height="600">
        <v-card-text>
          <v-card-title class="text-h5 font-weight-bold">
            アカウント作成
          </v-card-title>

          <ValidationObserver v-slot="{ passes, validate }">
            <ValidationProvider
              v-slot="{ errors }"
              rules="required|max:20"
              name="メールアドレス"
              mode="passive"
            >
              <v-text-field
                :class="inputMt"
                v-model="form.email"
                append-icon="mdi-email"
                label="メールアドレス"
                type="text"
                counter="20"
                outlined dense
                :error-messages="errors"
              />
            </ValidationProvider>

            <ValidationProvider
              v-slot="{ errors }"
              rules="required|max:20|confirmed:confirmPass"
              name="パスワード"
              mode="passive"
            >
              <v-text-field
                :class="inputMt"
                v-model="form.password"
                label="パスワード"
                counter="20"
                outlined dense
                :append-icon="passwordIcon"
                :type="passwordType"
                :error-messages="errors"
                @click:append="togglePasswordVisualization"
              />
            </ValidationProvider>

            <ValidationProvider
              vid="confirmPass"
              v-slot="{ errors }"
              rules="required|max:20"
              name="パスワード確認用"
              mode="passive"
            >
              <v-text-field
                :class="inputMt"
                v-model="form.confirm"
                label="パスワード確認用"
                counter="20"
                outlined dense
                :append-icon="confirmPasswordIcon"
                :type="confirmPasswordType"
                :error-messages="errors"
                @click:append="toggleConfirmPasswordVisualization"
              />
            </ValidationProvider>

            <v-btn
              class="font-weight-bold"
              color="primary"
              :class="inputMt"
              :loading="loading.register"
              rounded
              @click="!isRequested ? validate().then(passes(register)) : validate().then(passes(resend))"
            >
              <span>{{ !isRequested ? 'アカウントを作成' : 'メールをもう一度送る' }}</span>
            </v-btn>
            
            <v-row align-content="center" justify="center" class="my-3">
              <v-col cols="5" class="mt-3"><v-divider /></v-col>

              <v-col cols="2">
                <span class="text-subtitle-1">または</span>
              </v-col>

              <v-col cols="5" class="mt-3"><v-divider /></v-col>
            </v-row>

            <v-btn
              class="text-capitalize caption"
              color="#00ACEE"
              height="48px"
              rounded dark depressed block
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
          </ValidationObserver>
        </v-card-text>
      </v-card>
    </v-col>
  </v-row>
</v-container>
</template>

<script>

export default {
  data: () => ({
    form: {
      name: null,
      email: null,
      password: null,
      confirm: null,
    },
    loading: {
      register: false,
      googleLogin: false
    },
    inputMt: 'mt-3',
    isRequested: false,
    passwordIcon: 'mdi-eye-off',
    passwordType: 'password',
    confirmPasswordIcon: 'mdi-eye-off',
    confirmPasswordType: 'password'
  }),
  head() {
    return { title: 'アカウント作成' }
  },
  // created() {
  //   console.log(this.$auth.loggedIn)
  //   if (!this.$auth.loggedIn) {
  //     this.email = 'example@eggham.com'
  //     this.password = 'hogehoge'
  //   }
  // },
  methods: {
    async register() {
      this.loading.register = true
      
      this.form.name = this.form.email
      try {
        const res = await this.$axios.$post('register', this.form)
        this.isRequested = true
        this.$toast.show(res.message)
      } catch (e) {
        this.$toast.error('アカウント登録に失敗しました。もう一度お試しください。')
      }

      this.loading.register = false
    },
    async resend() {
      this.loading.register = true
      
      try {
        const res = await this.$axios.$get('email/resend', { params: this.form })
        console.log(res)
        this.$toast.show(res.message)
      } catch (e) {
        this.$toast.error('リクエスト失敗')
      }

      this.loading.register = false
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
    togglePasswordVisualization() {
      this.passwordType = this.passwordType === 'password' ? 'text' : 'password'
      this.passwordIcon = this.passwordType === 'password' ? 'mdi-eye-off' : 'mdi-eye'
    },
    toggleConfirmPasswordVisualization() {
      this.confirmPasswordType = this.confirmPasswordType === 'password' ? 'text' : 'password'
      this.confirmPasswordIcon = this.confirmPasswordType === 'password' ? 'mdi-eye-off' : 'mdi-eye'
    }
  }
}
</script>

<style scoped>
.bg {
  width: 100%;
  height: 100%;
  position: absolute;
  top: 0;
  left: 0;
  background: url( '~/assets/img/pexels-frans-van-heerden-624015.jpg') no-repeat center center;
  background-size: cover;
  background-color: black;
}

.bg::before {
  content: '';
  background-color: rgba(0, 0, 0, 0.4);
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: block;
}

.register-title {
  color: white;
  position: absolute;
  top: 25%;
  left: 10%;
}

.register-text {
  color: white;
  position: absolute;
  top: 35%;
  left: 10%;
}

.register-action-text {
  color: white;
  position: absolute;
  top: 50%;
  left: 10%;
}
</style>