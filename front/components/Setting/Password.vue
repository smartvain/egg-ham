<template>
  <div>
    <v-card-title>パスワード変更</v-card-title>

    <v-text-field
      v-model="currentPass"
      label="現在のパスワード"
      :append-icon="currentPasswordIcon"
      :type="currentPasswordType"
      outlined
      @click:append="toggleCurrentPasswordVisualization"
    />

    <v-btn
      plain small
    >
      パスワードを忘れましたか？
      <v-icon>mdi-chevron-right</v-icon>
    </v-btn>

    <v-text-field
      v-model="newPass"
      class="mt-7"
      label="新しいパスワード"
      :append-icon="newPasswordIcon"
      :type="newPasswordType"
      outlined
      @click:append="toggleNewPasswordVisualization"
    />

    <v-text-field
      v-model="confirmPass"
      label="新しいパスワード確認用"
      :append-icon="confirmPasswordIcon"
      :type="confirmPasswordType"
      outlined
      @click:append="toggleConfirmPasswordVisualization"
    />

    <v-row>
      <v-col cols="12" align="right">
        <v-btn
          color="primary"
          :loading="loading"
          @click="$emit('change-pass', {currentPass, newPass, confirmPass})"
        >
          変更する
        </v-btn>
      </v-col>
    </v-row>
  </div>
</template>

<script>
export default {
  props: {
    loading: { type: Boolean, default: false }
  },
  data: () => ({
    currentPass: null,
    newPass: null,
    confirmPass: null,
    currentPasswordIcon: 'mdi-eye-off',
    currentPasswordType: 'password',
    newPasswordIcon: 'mdi-eye-off',
    newPasswordType: 'password',
    confirmPasswordIcon: 'mdi-eye-off',
    confirmPasswordType: 'password'
  }),
  methods: {
    toggleCurrentPasswordVisualization() {
      this.currentPasswordType = this.currentPasswordType === 'password' ? 'text' : 'password'
      this.currentPasswordIcon = this.currentPasswordType === 'password' ? 'mdi-eye-off' : 'mdi-eye'
    },
    toggleNewPasswordVisualization() {
      this.newPasswordType = this.newPasswordType === 'password' ? 'text' : 'password'
      this.newPasswordIcon = this.newPasswordType === 'password' ? 'mdi-eye-off' : 'mdi-eye'
    },
    toggleConfirmPasswordVisualization() {
      this.confirmPasswordType = this.confirmPasswordType === 'password' ? 'text' : 'password'
      this.confirmPasswordIcon = this.confirmPasswordType === 'password' ? 'mdi-eye-off' : 'mdi-eye'
    },
    initPasswords() {
      this.currentPass = null
      this.newPass     = null
      this.confirmPass = null
    }
  }
}
</script>

<style>

</style>