<template>
  <v-navigation-drawer
    v-model="_drawer"
    width="27%"
    right temporary fixed
  >
    <v-card class="px-2" flat>
      <v-btn
        icon right fixed
        @click="_drawer = false"
      >
        <v-icon>mdi-close</v-icon>
      </v-btn>

      <v-img
        :src="require('~/assets/img/logo.png')"
        max-width="60%"
        class="mx-auto mt-5"
      />

      <v-card-text>
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
          rounded outlined block
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

          <v-row>
            <v-col cols="6" align="left">
              <span>新規登録</span>
            </v-col>

            <v-col cols="6" align="left" class="px-0">
              <span>パスワードを忘れた方</span>
            </v-col>
          </v-row>

          <div class="text-center">
            <v-btn
              class="primary mt-5"
              @click="validate().then(passes(login))"
            >
              ログイン
            </v-btn>
          </div>
        </ValidationObserver>
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
    loading: {
      login: false
    }
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
  created() {
    console.log(this.$auth.loggedIn)
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
        this.$toast.show(res.data.message)
      } catch (e) {
        this.$toast.error('ログインに失敗しました。もう一度お試しください。')
      }

      this.loading.login = false
    }
  }
}
</script>

<style>

</style>