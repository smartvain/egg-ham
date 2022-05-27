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
  </div>
</template>

<script>
export default {
  data: () => ({
    currentPass: null,
    newPass: null,
    confirmPass: null
  })
}
</script>

<style>

</style>