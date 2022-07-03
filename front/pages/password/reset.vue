<template>
  <v-container fill-height>
    <v-row>
      <v-col cols="12" align="center">
        <v-card class="px-8" elevation="5" height="500" width="500">
        <v-card-text>
          <v-card-title class="text-h5 font-weight-bold">パスワード再設定</v-card-title>

          <p class="text-left">新しいパスワードをご入力下さい。</p>

          <ValidationObserver v-slot="{ passes, validate }" @submit.prevent>
            <form>
              <ValidationProvider
                v-slot="{ errors }"
                rules="required|min:8|max:20|confirmed:confirmPass"
                name="新しいパスワード"
                mode="passive"
              >
                <v-text-field
                  v-model="form.password"
                  :class="inputMt"
                  label="新しいパスワード"
                  counter="20"
                  outlined dense
                  :append-icon="passwordIcon"
                  :type="passwordType"
                  :error-messages="errors"
                  @click:append="togglePasswordVisualization"
                />
              </ValidationProvider>

              <ValidationProvider
                v-slot="{ errors }"
                vid="confirmPass"
                rules="required|min:8|max:20"
                name="パスワード確認用"
                mode="passive"
              >
                <v-text-field
                  v-model="form.password_confirmation"
                  :class="inputMt"
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
                type="submit"
                color="primary"
                class="font-weight-bold"
                :class="inputMt"
                :loading="loading.sendResetLinkEmail"
                rounded
                @click="validate().then(passes(sendResetLinkEmail))"
              >
                パスワードをリセットする
              </v-btn>
            </form>
          </ValidationObserver>
        </v-card-text>
      </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import Mixin from '~/mixins/mixin.js'

export default {
  mixins: [ Mixin ],
  data: () => ({
    form: {
      password: null,
      password_confirmation: null
    },
    loading: {
      sendResetLinkEmail: false
    },
    inputMt: 'mt-3',
    passwordIcon: 'mdi-eye-off',
    passwordType: 'password',
    confirmPasswordIcon: 'mdi-eye-off',
    confirmPasswordType: 'password'
  }),
  head() {
    return { title: 'パスワード再設定' }
  },
  methods: {
    async sendResetLinkEmail() {
      this.loading.sendResetLinkEmail = true
      
      try {
        const res = await this.$axios.$post('password/reset', {
          email: this.$route.query.email,
          token: this.$route.query.token,
          ...this.form
        })
        this.showMessage(res.status, res.message)
      } catch (e) {
        console.log(e.message)
      }

      this.loading.sendResetLinkEmail = false
    }
  }
}
</script>

<style>

</style>