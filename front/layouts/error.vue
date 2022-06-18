<template>
  <v-container fill-height>
    <v-row justify="center" align-content="center">
      <v-col cols="12" align="center">
        <h1 v-if="error.statusCode === 404" class="grey--text">
          {{ error.statusCode }}<br>{{ pageNotFound }}
        </h1>
        <h1 v-else class="grey--text">
          {{ otherError }}
        </h1>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  name: 'EmptyLayout',
  layout: 'empty',
  props: {
    error: {
      type: Object,
      default: null,
    },
  },
  data() {
    return {
      pageNotFound: 'ページが存在しません。',
      otherError: '予期しないエラーが発生しました。',
    }
  },
  head() {
    const title =
      this.error.statusCode === 404 ? this.pageNotFound : this.otherError
    return {
      title,
    }
  },
}
</script>

<style scoped>
h1 {
  font-size: 20px;
}
</style>
