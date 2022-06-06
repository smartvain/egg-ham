<template>
  <div>
    <v-card-title>メールアドレス変更</v-card-title>
    
    <v-text-field
      v-model="email"
      label="新しいメールアドレス"
      outlined
    />

    <v-btn
      color="primary"
      rounded absolute right
      :loading="loading"
      @click="$emit('change-email', email)"
    >
      変更する
    </v-btn>
  </div>
</template>

<script>
export default {
  props: {
    loading: { type: Boolean, default: false }
  },
  data: () => ({
    email: null
  })
}
</script>

<style>

</style>