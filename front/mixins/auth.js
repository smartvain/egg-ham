export default {
  methods: {
    async googleLogin() {
      this.loading.googleLogin = true

      try {
        await this.$auth.loginWith('google')
      } catch (e) {
        console.log(e.message)
      }

      this.loading.googleLogin = false
    },
    beforeSnsLogin(callback) {
      if (this.$auth.loggedIn) {
        this.$toast.error('すでにログインしています。')
        return
      }
      callback()
    },
    logout() {
      try {
        this.$auth.logout()
        this.$toast.show('ログアウトしました。')
      } catch (e) {
        this.$toast.error('ログアウトに失敗しました。時間をおいて再度お試し下さい。')
      }
    }
  }
}