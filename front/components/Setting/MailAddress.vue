<template>
  <div>
    <v-card-title>メールアドレス変更</v-card-title>

    <form>
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

      <v-btn plain small @click="passwordReset">
        パスワードを忘れましたか？<v-icon>mdi-chevron-right</v-icon>
      </v-btn>
      
      <ValidationProvider
        v-slot="{ errors }"
        rules="required|_email|max:20"
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
            変更する
          </v-btn>
        </v-col>
      </v-row>
    </form>
  </div>
</template>

<script>
import Mixin from '~/mixins/mixin.js'

export default {
  mixins: [ Mixin ],
  props: {
    loading: { type: Boolean, default: false }
  },
  data: () => ({
    currentPass: null,
    newEmail: null,
    currentPasswordIcon: 'mdi-eye-off',
    currentPasswordType: 'password',
  })
}
</script>

<style>

</style>