<template>
  <v-navigation-drawer
    v-model="_show"
    v-click-outside="hideDrawer"
    :width="screenWidth <= 767 ? '100%' : '27%'"
    right temporary fixed
  >
    <v-card class="px-2" height="100%" flat>
      <v-btn icon right fixed @click="$emit('update:show', false)">
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
          style="border-color: #979797"
          height="48px"
          :loading="loading.googleLogin"
          rounded outlined block
          @click="beforeSnsLogin(googleLogin)"
        >
          <v-img
            class="mr-4"
            src="https://madeby.google.com/static/images/google_g_logo.svg"
            max-width="24"
          />
          Googleでログイン
        </v-btn>

        <p class="text-center mt-5 text-subtitle-1">メールアドレスでログイン</p>
        
        <ValidationObserver ref="loginValidation" @submit.prevent>
          <form>
            <ValidationProvider
              v-slot="{ errors }"
              rules="required|_email|max:50"
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
              rules="required|min:8|max:20"
              name="パスワード"
              mode="passive"
            >
              <v-text-field
                v-model="password"
                placeholder="パスワード"
                outlined dense
                :append-icon="passwordIcon"
                :type="passwordType"
                :error-messages="errors"
                @click:append="togglePasswordVisualization"
              />
            </ValidationProvider>

            <div class="text-center">
              <v-btn
                class="primary"
                type="submit"
                rounded block
                :loading="loading.login"
                @click="beforeLogin(login)"
              >
                <span class="text-subtitle-1 font-weight-bold">ログイン</span>
              </v-btn>
            </div>
          </form>
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
          @click="resetLinkDialog = !resetLinkDialog"
        >
          パスワードをお忘れの方
          <v-icon>mdi-chevron-right</v-icon>
        </v-btn>

        <v-btn
          v-if="$auth.loggedIn"
          class="pa-0"
          absolute bottom right plain small
          @click="logout"
        >
          ログアウト
          <v-icon>mdi-logout</v-icon>
        </v-btn>
      </v-card-text>
    </v-card>

    <SendResetLinkDialog :show.sync="resetLinkDialog" />
  </v-navigation-drawer>
</template>

<script>
import AuthMixin from '~/mixins/auth.js'
import Mixin from '~/mixins/mixin.js'
import SendResetLinkDialog from '~/components/SendResetLinkDialog.vue'

export default {
  mixins: [ AuthMixin, Mixin ],
  components: { SendResetLinkDialog },
  props: {
    show: { type: Boolean, default: false }
  },
  data: () => ({
    email: null,
    password: null,
    loading: {
      login: false,
      googleLogin: false,
    },
    resetLinkDialog: false,
    screenWidth: window.innerWidth,
    passwordIcon: 'mdi-eye-off',
    passwordType: 'password'
  }),
  computed: {
    _show: {
      get() { return this.show },
      set() {}
    }
  },
  mounted() {
    window.addEventListener('resize', () => this.screenWidth = window.innerWidth)
  },
  methods: {
    async login() {
      this.loading.login = true
      
      try {
        const res = await this.$auth.loginWith('local', { data: {
          email: this.email,
          password: this.password,
        }})

        this.showMessage(res.data.status, res.data.message)

        if (res.data.status === 'success') {
          this.$emit('update:show', false)
        }
      } catch (e) {
        console.log(e.message)
      }

      this.loading.login = false
    },
    async beforeLogin(callback) {
      const isValid = await this.$refs.loginValidation.validate()
      if (!isValid) { return }

      if (this.$auth.loggedIn) {
        this.$toast.error('すでにログインしています。')
        return
      }
      callback()
    },
    hideDrawer(e) {
      if(e.target.className === "v-overlay__scrim") {
        this.$emit('update:show', false);
      }
    }
  }
}
</script>

<style>

</style>