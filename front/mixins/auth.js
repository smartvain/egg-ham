export default {
  methods: {
    logout() {
      try {
        this.$auth.logout()
        this.$toast.show('ログアウトしました。')
      } catch (e) {
        this.$toast.show('ログアウトに失敗しました。時間をおいて再度お試し下さい。')
      }
    }
  }
}