<template>
<v-container fill-height>
  <v-row justify="center">
    <v-col cols="6">
      <v-card class="pa-3" elevation="3">
        <v-card-text>
          <h1>
            <v-img :src="require('~/assets/img/logo.png')" />
          </h1>

          <ValidationObserver v-slot="{ passes, validate }">
            <ValidationProvider
              v-slot="{ errors }"
              rules="required|max:20"
              name="メールアドレス"
              mode="passive"
            >
              <v-text-field
                class="mt-8"
                v-model="email"
                append-icon="mdi-account"
                placeholder="メールアドレス"
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
                v-model="password"
                append-icon="mdi-lock"
                placeholder="パスワード"
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
                v-model="confirm"
                append-icon="mdi-lock"
                placeholder="パスワード確認用"
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
    email: null,
    password: null,
    confirm: null,
    loading: {
      register: false
    },
    inputMt: 'mt-5'
  }),
  created() {
    console.log(this.$auth.loggedIn)
    if (!this.$auth.loggedIn) {
      this.email = 'example@eggham.com'
      this.password = 'hogehoge'
    }
  },
  methods: {
    async register() {
      this.loading.register = true
      
      try {
        const res = await this.$axios.$post('register', {
          email: this.email,
          password: this.password,
          confirm: this.confirm
        })
        this.$toast.show(res.data.message)
      } catch (e) {
        this.$toast.error('アカウント登録に失敗しました。もう一度お試しください。')
      }

      this.loading.register = false
    }
  }
}
</script>
