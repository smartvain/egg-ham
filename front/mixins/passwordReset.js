export default {
  methods: {
    async passwordReset() {
      this.loading.passwordReset = true
      
      console.log(this.$auth.user)
      try {
        const res = await this.$axios.$post('password/request', { email: this.$auth.user.email })
        this.showMessage(res.status, res.message)
      } catch (e) {
        console.log(e.message)
      }

      this.loading.passwordReset = false
    }
  }
}