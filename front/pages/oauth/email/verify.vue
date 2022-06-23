<template>
<v-container fill-height>
  <v-row justify="center" align-content="center">
    <v-col cols="12" align="center">
      <v-card-text class="text-h5" style="color: #979797">
        メールアドレス認証中…
      </v-card-text>

      <VueLoading
        type="balls"
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
    this.$toast.show(this.$route.query.message)

    if (this.$route.query.token) {
      this.$auth.setUserToken(this.$route.query.token)
      
      try {
        const user = await this.getUser()
        this.$auth.setUser(user)
      } catch (e) {
        this.$toast.error(e.message)
      }
    }

    this.$refs.redirect.$el.click()
  },
  methods: {
    async getUser() {
      const res = await this.$axios.$get('user')
      return res.user
    }
  }
}
</script>