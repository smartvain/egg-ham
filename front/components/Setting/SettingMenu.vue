<template>
  <v-card outlined>
    <v-row>
      <v-col cols="4" class="pa-5">
        <v-list flat>
          <v-card-title class="text-h5 font-weight-bold">設定</v-card-title>

          <v-list-item-group v-model="selectedItem" color="primary">
            <v-list-item v-for="(list, index) in settingList" :key="index">
              <v-list-item-content>
                <v-list-item-title v-text="list.text" />
              </v-list-item-content>

              <v-list-item-icon>
                <v-icon>mdi-chevron-right</v-icon>
              </v-list-item-icon>
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </v-col>

      <v-divider vertical />

      <v-col cols="8" class="pl-0">
        <v-card-text>
          <UserInfo
            v-if="selectedItem === 0"
            :loading="loading.changeName"
            @change-user-info="changeName"
          />
          <ValidationObserver ref="changeEmailValidation" @submit.prevent>
            <MailAddress
              v-if="selectedItem === 1"
              :loading="loading.changeEmail"
              @change-email="changeEmail"
            />
          </ValidationObserver>
          <Password
            v-if="selectedItem === 2"
            ref="password"
            :loading="loading.changePass"
            @change-pass="changePass"
          />
        </v-card-text>
      </v-col>
    </v-row>
  </v-card>
</template>

<script>
import UserInfo from '~/components/Setting/UserInfo.vue'
import MailAddress from '~/components/Setting/MailAddress.vue'
import Password from '~/components/Setting/Password.vue'
import Mixin from '~/mixins/mixin.js'

export default {
  components: { UserInfo, MailAddress, Password },
  mixins: [ Mixin ],
  data: () => ({
    settingList: [
      { text: 'アカウント情報' },
      { text: 'メールアドレス変更' },
      { text: 'パスワード変更' },
    ],
    loading: {
      changeName: false,
      changeEmail: false,
      changePass: false
    },
    selectedItem: null
  }),
  methods: {
    async getUser() {
      const res = await this.$axios.$get('user')
      return res.user
    },
    async changeName(e) {
      this.loading.changeName = true
      
      try {
        const res = await this.$axios.$put('user/name', { name: e.name })
        
        this.$auth.setUser(await this.getUser())

        this.showMessage(res.status, res.message)
      } catch (e) {
        console.log(e.message)
      }

      this.loading.changeName = false
    },
    async changeEmail(e) {
      const isValid = await this.$refs.changeEmailValidation.validate()
      if (!isValid) { return }
      
      this.loading.changeEmail = true

      try {
        const res = await this.$axios.$put('user/email', {
          currentPass: e.currentPass,
          newEmail   : e.newEmail,
        })

        this.showMessage(res.status, res.message)
      } catch (e) {
        console.log(e.message)
      }

      this.loading.changeEmail = false
    },
    async changePass(e) {
      this.loading.changePass = true
      
      try {
        const res = await this.$axios.$put('user/password', {
          currentPass: e.currentPass,
          newPass    : e.newPass,
          confirmPass: e.confirmPass,
        })
        
        if (res.status === 'success') { this.$refs.password.initPasswords() }

        this.showMessage(res.status, res.message)
      } catch (e) {
        console.log(e.message)
      }

      this.loading.changePass = false
    }
  }
}
</script>

<style>

</style>