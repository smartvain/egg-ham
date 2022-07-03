<template>
  <v-dialog v-model="_show" width="670">
    <v-col cols="12" align="center" class="pa-0">
      <v-card class="px-8" elevation="5" height="300">
        <v-card-text>
          <v-card-title class="text-h5 font-weight-bold">パスワード再設定メールを送信</v-card-title>

          <p class="text-left">パスワードを再設定したいユーザーのメールアドレスをご入力下さい。</p>

          <ValidationObserver v-slot="{ passes, validate }" @submit.prevent>
            <form>
              <ValidationProvider
                v-slot="{ errors }"
                rules="required|_email|max:50"
                name="メールアドレス"
                mode="passive"
              >
                <v-text-field
                  v-model="email"
                  :class="inputMt"
                  append-icon="mdi-email"
                  label="メールアドレス"
                  type="text"
                  outlined dense
                  :error-messages="errors"
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
                パスワード再設定メールを送る
              </v-btn>
            </form>
          </ValidationObserver>
        </v-card-text>

        <v-card-actions>
          <v-spacer />
          
          <v-btn color="primary" text @click="$emit('update:show', false)">
            閉じる
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-col>
  </v-dialog>
</template>

<script>
import Mixin from '~/mixins/mixin.js'

export default {
  mixins: [ Mixin ],
  props: {
    show: { type: Boolean, default: false }
  },
  data: () => ({
    loading: {
      sendResetLinkEmail: false
    },
    email: null,
    inputMt: 'mt-3',
    passwordIcon: 'mdi-eye-off',
    passwordType: 'password',
    confirmPasswordIcon: 'mdi-eye-off',
    confirmPasswordType: 'password'
  }),
  computed: {
    _show: {
      get() { return this.show },
      set() {}
    }
  },
  head() {
    return { title: 'パスワード再設定' }
  },
  methods: {
    async sendResetLinkEmail() {
      this.loading.sendResetLinkEmail = true
      
      try {
        const res = await this.$axios.$post('password/forgot', { email: this.email })
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