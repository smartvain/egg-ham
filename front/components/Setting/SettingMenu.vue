<template>
  <v-card outlined>
    <v-row>
      <v-col cols="4" class="pa-5">
        <v-list flat>
          <v-card-title class="text-h5 font-weight-bold">設定</v-card-title>

          <v-list-item-group
            v-model="selectedItem"
            color="primary"
          >
            <v-list-item
              v-for="(list, index) in settingList"
              :key="index"
            >
              <v-list-item-content>
                <v-list-item-title v-text="list.text"></v-list-item-title>
              </v-list-item-content>
              <v-list-item-icon>
                <v-icon>mdi-chevron-right</v-icon>
              </v-list-item-icon>
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </v-col>

      <v-divider class="" vertical/>

      <v-col cols="8" class="pl-0">
        <v-card-text>
          <UserInfo
            v-if="selectedItem === 0"
            :loading="loading.changeUserInfo"
            @change-user-info="changeUserInfo"
          >
          </UserInfo>
          <MailAddress
            v-if="selectedItem === 1"
            :loading="loading.changeEmail"
            @change-email="changeEmail"
          />
          <Password
            ref="password"
            v-if="selectedItem === 2"
            :loading="loading.changePass"
            @change-pass="changePass"
          />
          <SiteLanguage v-if="selectedItem === 3" />
          <DarkMode v-if="selectedItem === 4" />
        </v-card-text>
      </v-col>
    </v-row>
  </v-card>
</template>

<script>
import UserInfo from '~/components/Setting/UserInfo.vue'
import MailAddress from '~/components/Setting/MailAddress.vue'
import Password from '~/components/Setting/Password.vue'
import SiteLanguage from '~/components/Setting/SiteLanguage.vue'
import DarkMode from '~/components/Setting/DarkMode.vue'

export default {
  components: {
    UserInfo, MailAddress, Password, SiteLanguage, DarkMode
  },
  data: () => ({
    settingList: [
      { text: 'アカウント情報' },
      { text: 'メールアドレス変更' },
      { text: 'パスワード変更' },
      { text: 'サイト内言語' },
      { text: 'ダークモード' },
    ],
    loading: {
      changeUserInfo: false,
      changeEmail: false,
      changePass: false
    },
    selectedItem: null
  }),
  methods: {
    async changeUserInfo(e) {
      this.loading.changeUserInfo = true
      
      try {
        const res = await this.$axios.$put('user/name', { name: e.name, id: this.$auth.user.id })
        this.$toast.show(res.message)
      } catch (e) {
        this.$toast.error(e.message)
      }

      this.loading.changeUserInfo = false
    },
    async changeEmail(e) {
      this.loading.changeEmail = true

      const currentPass = e.currentPass
      const newEmail    = e.newEmail

      try {
        const res = await this.$axios.$put('user/email', { currentPass, newEmail, oldEmail: this.$auth.user.email })
        this.$toast.show(res.message)
      } catch (e) {
        this.$toast.error(e.message)
      }

      this.loading.changeEmail = false
    },
    async changePass(e) {
      this.loading.changePass = true
      
      const currentPass = e.currentPass
      const newPass     = e.newPass
      const confirmPass = e.confirmPass

      try {
        const res = await this.$axios.$put('user/password', {
          currentPass,
          newPass,
          confirmPass,
          email: this.$auth.user.email
        })
        if (res.isSuccess) {
          this.$refs.password.initPasswords()
        }
        this.$toast.show(res.message)
      } catch (e) {
        this.$toast.error(e.message)
      }

      this.loading.changePass = false
    }
  }
}
</script>

<style>

</style>