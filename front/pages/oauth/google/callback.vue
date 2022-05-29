<template>
  <v-row justify="center" align-content="center">
    <v-col cols="1" align="center">
      認証中…
    </v-col>
  </v-row>
</template>

<script>
  export default {
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
        const res = await this.$axios.$get('oauth/google/callback', { params: this.$route.query })
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