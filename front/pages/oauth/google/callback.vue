<template>
  <v-container fill-height>
    <v-row justify="center" align-content="center">
      <v-col cols="12" align="center">
        <v-card-text class="text-h5" style="color: #979797">
          Google認証中…
        </v-card-text>

        <VueLoading
          type="barsCylon"
          color="#F8BBD0"
          :size="{ width: '50px', height: '50px' }"
        />
      </v-col>
    </v-row>
    
    <router-link ref="redirect" :to="$route.query.redirect ? $route.query.redirect : '/'" />
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
      this.$auth.setUserToken(await this.getToken())
      this.$auth.setUser(await this.getUser())
    } catch (e) {
      this.$toast.error(e.message)
    }
    
    this.$refs.redirect.$el.click()
  },
  methods: {
    showMessage(status, message) {
      status === 'success' ? this.$toast.show(message) : this.$toast.error(message)
    },
    async getToken() {
      const res = await this.$axios.$get('oauth/google/callback', { params: this.$route.query })
      this.showMessage('success', res.message)
      return res.token
    },
    async getUser() {
      const res = await this.$axios.$get('user')
      return res.user
    }
  }
}
</script>