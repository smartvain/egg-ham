<template>
  <div>
    <v-card-title>メールアドレス変更</v-card-title>

    <v-card-text v-if="$auth.user.provider_id" class="grey--text">
      SNSログインしている場合は、メールアドレスの変更はできません。
    </v-card-text>

    <form v-else>
      <ValidationProvider
        v-slot="{ errors }"
        rules="required|min:8|max:20"
        name="パスワード"
        mode="passive"
      >
        <v-text-field
          v-model="currentPass"
          label="パスワード"
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
        rules="required|_email|max:50"
        name="メールアドレス"
        mode="passive"
      >
        <v-text-field
          v-model="newEmail"
          class="mt-7"
          label="新しいメールアドレス"
          append-icon="mdi-email"
          outlined
          :error-messages="errors"
        />
      </ValidationProvider>

      <v-row>
        <v-col cols="12" align="right">
          <v-btn
            color="primary"
            type="submit"
            :loading="loading"
            @click="$emit('change-email', {currentPass, newEmail})"
          >
            メールアドレス確認メールを送る
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
    newEmail: null,
    currentPasswordIcon: 'mdi-eye-off',
    currentPasswordType: 'password',
    resetLinkDialog: false,
  })
}
</script>

<style>

</style>