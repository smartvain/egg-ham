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
          <MailAddress
            v-if="selectedItem === 0"
            :loading="loading.changeEmail"
            @change-email="changeEmail"
          />
          <Password v-if="selectedItem === 1" />
          <SiteLanguage v-if="selectedItem === 2" />
          <DarkMode v-if="selectedItem === 3" />
        </v-card-text>
      </v-col>
    </v-row>
  </v-card>
</template>

<script>
import MailAddress from '~/components/Setting/MailAddress.vue'
import Password from '~/components/Setting/Password.vue'
import SiteLanguage from '~/components/Setting/SiteLanguage.vue'
import DarkMode from '~/components/Setting/DarkMode.vue'

export default {
  components: {
    Password, MailAddress, SiteLanguage, DarkMode
  },
  data: () => ({
    settingList: [
      { text: 'メールアドレス' },
      { text: 'パスワード' },
      { text: 'サイト内言語' },
      { text: 'ダークモード' },
    ],
    loading: {
      changeEmail: false
    },
    selectedItem: null
  }),
  methods: {
    changeEmail(email) {
      this.loading.changeEmail = true

      try {
        alert(email)
        // const res = await this.$axios.$put('user/email', { email })
        // console.log(res)
        // this.$toast.show()
      } catch (e) {
        this.$toast.error(e.message)
      }

      this.loading.changeEmail = false
    }
  }
}
</script>

<style>

</style>