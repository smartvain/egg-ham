<template>
<v-container fill-height>
  <v-row justify="center">
    <v-col cols="6">
      <v-card class="pa-3" elevation="1">
        <v-card-text>
          <h1>
            <v-img :src="require('~/assets/img/logo.png')" />
          </h1>

          <ValidationObserver v-slot="{ passes, validate }">
            <v-form>
              <ValidationProvider
                v-slot="{ errors }"
                rules="required|max:20"
                name="ログインID"
                mode="passive"
              >
                <v-text-field
                  v-model="email"
                  append-icon="mdi-account"
                  label="メールアドレス"
                  type="text"
                  counter="20"
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
                  label="パスワード"
                  type="password"
                  counter="20"
                  :error-messages="errors"
                />
              </ValidationProvider>

              <div class="mt-5">
                <v-btn
                  color="primary"
                  :loading="loading.login"
                  block
                  @click="validate().then(passes(login))"
                >
                  ログイン
                </v-btn>
              </div>
            </v-form>
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
    loading: {
      login: false
    }
  }),
  created() {
    console.log(this.$auth.loggedIn)
    if (!this.$auth.loggedIn) {
      this.email = 'example@eggham.com'
      this.password = 'hogehoge'
    }
  },
  methods: {
    async login() {
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
