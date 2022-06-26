export default {
  methods: {
    showMessage(status, message) {
      status === 'success' ? this.$toast.show(message) : this.$toast.error(message)
    },
    togglePasswordVisualization() {
      const isPasswordType = this.passwordType === 'password'
      this.passwordType = isPasswordType ? 'text' : 'password'
      this.passwordIcon = isPasswordType ? 'mdi-eye' : 'mdi-eye-off'
    },
    toggleCurrentPasswordVisualization() {
      const isCurrentPasswordType = this.currentPasswordType === 'password'
      this.currentPasswordType    = isCurrentPasswordType ? 'text' : 'password'
      this.currentPasswordIcon    = isCurrentPasswordType ? 'mdi-eye' : 'mdi-eye-off'
    },
    toggleNewPasswordVisualization() {
      const isNewPasswordType = this.newPasswordType === 'password'
      this.newPasswordType    = isNewPasswordType ? 'text' : 'password'
      this.newPasswordIcon    = isNewPasswordType ? 'mdi-eye' : 'mdi-eye-off'
    },
    toggleConfirmPasswordVisualization() {
      const isConfirmPasswordType = this.confirmPasswordType === 'password'
      this.confirmPasswordType = isConfirmPasswordType ? 'text' : 'password'
      this.confirmPasswordIcon = isConfirmPasswordType ? 'mdi-eye' : 'mdi-eye-off'
    },
  }
}