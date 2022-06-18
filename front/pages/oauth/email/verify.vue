<template>
<v-container fill-height>
  <v-row justify="center" align-content="center">
    <v-col cols="12" align="center">
      <v-card-text class="text-h5" style="color: #979797">
        メールアドレス認証中…
      </v-card-text>

      <VueLoading
        type="balls"
        :size="{ width: '50px', height: '50px' }"
      />
    </v-col>
  </v-row>
  <router-link ref="redirect" :to="$route.query.redirect ? $route.query.redirect : '/'" />
</v-container>
</template>

<script>
import { VueLoading } from 'vue-loading-template'

export default {
  components: { VueLoading },
  layout: 'oauth',
  head() {
    return { title: '認証中' }
  },
  mounted() {
    console.log(this.$route.query)
    this.$toast.show(this.$route.query.message)
    this.$refs.redirect.$el.click()
  }
}
</script>