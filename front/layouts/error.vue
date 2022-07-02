<template>
  <v-container fill-height>
    <v-row justify="center" align-content="center">
      <v-col cols="12" align="center">
        <h1 v-if="error.statusCode === 401" class="grey--text">
          {{ error.statusCode }}<br>{{ notLogged }}
        </h1>
        <h1 v-else-if="error.statusCode === 404" class="grey--text">
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
      notLogged: '',
      pageNotFound: 'ページが存在しません。',
      otherError: '予期しないエラーが発生しました。',
    }
  },
  created() {
    let message

    switch (this.$route.name) {
      case 'mypage': message = 'マイページはログインが必要なページです。'; break
      default: message = 'ログインが必要です。'; break
    }
    
    this.notLogged = message
  },
  head() {
    let title
    switch (this.error.statusCode) {
      case 401: title = this.notLogged; break
      case 404: title = this.pageNotFound; break
      default: title = this.otherError; break
    }

    return { title }
  },
}
</script>

<style scoped>
h1 {
  font-size: 20px;
}
</style>
