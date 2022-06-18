<template>
<v-container fill-height>
  <v-row justify="center" align-content="center">
    <v-col cols="12" align="center">
      <v-card-text class="text-h5" style="color: #979797">
        Twitter認証中…
      </v-card-text>

      <VueLoading
        type="barsCylon"
        color="#1095FE"
        :size="{ width: '50px', height: '50px' }"
      />
    </v-col>
  </v-row>
</v-container>
</template>

<script>
import { VueLoading } from 'vue-loading-template'

export default {
  components: { VueLoading },
  layout: 'oauth',
  head() {
    return { title: '認証中' }
  },
  async mounted() {
    try {
      const token = await this.getToken()
      this.$auth.setUserToken(token)

      const user = await this.getUser()
      this.$auth.setUser(user)
      
      this.$router.replace('/')
    } catch (e) {
      this.$toast.error(e.message)
    }
  },
  methods: {
    async getToken() {
      const res = await this.$axios.$get('oauth/twitter/callback', { params: this.$route.query })
      this.showMessage(res.message)
      return res.token
    },
    async getUser() {
      const res = await this.$axios.$get('user')
      return res.user
    },
    showMessage(message) {
      this.$toast.show(message)
    }
  }
}
</script>