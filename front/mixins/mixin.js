export default {
  methods: {
    showMessage(status, message) {
      status === 'success' ? this.$toast.show(message) : this.$toast.error(message)
    }
  }
}