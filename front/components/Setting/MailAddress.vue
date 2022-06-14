<template>
  <div>
    <v-card-title>メールアドレス変更</v-card-title>

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
      v-model="newEmail"
      class="mt-7"
      label="新しいメールアドレス"
      outlined
    />

    <v-row>
      <v-col cols="12" align="right">
        <v-btn
          color="primary"
          :loading="loading"
          @click="$emit('change-email', {currentPass, newEmail})"
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
    newEmail: null
  })
}
</script>

<style>

</style>