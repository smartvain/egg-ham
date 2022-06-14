<template>
  <div>
    <v-card-title>パスワード変更</v-card-title>

    <v-text-field
      v-model="currentPass"
      label="現在のパスワード"
      outlined
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
      outlined
    />

    <v-text-field
      v-model="confirmPass"
      label="新しいパスワード確認用"
      outlined
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
    confirmPass: null
  })
}
</script>

<style>

</style>