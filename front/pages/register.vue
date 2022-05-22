<template>
<v-container fill-height>
  <v-row justify="center">
    <v-col cols="6">
      <v-card class="pa-3" elevation="3">
        <v-card-text>
          <h1 class="mb-5">
            <!-- <v-img :src="require('~/assets/img/logo.png')" /> -->
            EggHamへようこそ！
          </h1>

          <p>アカウント登録すると以下の機能を使用できます。</p>
          <p>・翻訳機能</p>
          <p>・単語を保存/編集</p>
          <p>・保存した単語をダウンロード</p>

          <ValidationObserver v-slot="{ passes, validate }">
            <ValidationProvider
              v-slot="{ errors }"
              rules="required|max:20"
              name="ユーザー名"
              mode="passive"
            >
              <v-text-field
                class="mt-8"
                v-model="form.name"
                append-icon="mdi-account"
                label="ユーザー名"
                type="text"
                counter="20"
                outlined dense
                :error-messages="errors"
              />
            </ValidationProvider>

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
                append-icon="mdi-lock"
                label="パスワード"
                type="password"
                counter="20"
                outlined dense
                :error-messages="errors"
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
                append-icon="mdi-lock"
                label="パスワード確認用"
                type="password"
                counter="20"
                outlined dense
                :error-messages="errors"
              />
            </ValidationProvider>

            <v-btn
              :class="inputMt"
              color="primary"
              :loading="loading.register"
              block
              @click="validate().then(passes(register))"
            >
              新規登録
            </v-btn>

            <v-btn
              :class="inputMt"
              color="primary"
              :loading="loading.resend"
              block
              @click="validate().then(passes(resend))"
            >
              再送信
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
      resend: false
    },
    inputMt: 'mt-3'
  }),
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
      
      try {
        const res = await this.$axios.$post('register', this.form)
        this.$toast.show(res.message)
      } catch (e) {
        this.$toast.error('アカウント登録に失敗しました。もう一度お試しください。')
      }

      this.loading.register = false
    },
    async resend() {
      this.loading.resend = true
      
      try {
        const res = await this.$axios.$get('email/resend', { params: this.form })
        console.log(res)
        this.$toast.show(res.message)
      } catch (e) {
        this.$toast.error('リクエスト失敗')
      }

      this.loading.resend = false
    }
  }
}
</script>
