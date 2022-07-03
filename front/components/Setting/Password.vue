<template>
  <div>
    <v-card-title>パスワード変更</v-card-title>

    <form>
      <ValidationProvider
        v-slot="{ errors }"
        rules="required|min:8|max:20"
        name="現在のパスワード"
        mode="passive"
      >
        <v-text-field
          v-model="currentPass"
          label="現在のパスワード"
          :append-icon="currentPasswordIcon"
          :type="currentPasswordType"
          :error-messages="errors"
          outlined
          @click:append="toggleCurrentPasswordVisualization"
        />
      </ValidationProvider>

      <v-btn plain small @click="resetLinkDialog = !resetLinkDialog">
        パスワードを忘れましたか？<v-icon>mdi-chevron-right</v-icon>
      </v-btn>

      <ValidationProvider
        v-slot="{ errors }"
        rules="required|min:8|max:20|confirmed:confirmPass"
        name="新しいパスワード"
        mode="passive"
      >
        <v-text-field
          v-model="newPass"
          class="mt-7"
          label="新しいパスワード"
          :append-icon="newPasswordIcon"
          :type="newPasswordType"
          :error-messages="errors"
          outlined
          @click:append="toggleNewPasswordVisualization"
        />
      </ValidationProvider>

      <ValidationProvider
        v-slot="{ errors }"
        vid="confirmPass"
        rules="required|min:8|max:20"
        name="新しいパスワード確認用"
        mode="passive"
      >
        <v-text-field
          v-model="confirmPass"
          label="新しいパスワード確認用"
          :append-icon="confirmPasswordIcon"
          :type="confirmPasswordType"
          :error-messages="errors"
          outlined
          @click:append="toggleConfirmPasswordVisualization"
        />
      </ValidationProvider>

      <v-row>
        <v-col cols="12" align="right">
          <v-btn
            color="primary"
            type="submit"
            :loading="loading"
            @click="$emit('change-pass', {currentPass, newPass, confirmPass})"
          >
            変更する
          </v-btn>
        </v-col>
      </v-row>
    </form>

    <SendResetLinkDialog :show.sync="resetLinkDialog" />
  </div>
</template>

<script>
import Mixin from '~/mixins/mixin.js'
import SendResetLinkDialog from '~/components/SendResetLinkDialog.vue'

export default {
  mixins: [ Mixin ],
  components: { SendResetLinkDialog },
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
    confirmPasswordType: 'password',
    resetLinkDialog: false,
  }),
  methods: {
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