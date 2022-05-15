<template>
  <v-app dark>
    <v-app-bar app elevation="2" height="60">
      <a href="/">
        <v-img
          :src="require('~/assets/img/logo.png')"
          max-width="160"
          max-height="100%"
          contain
        />
      </a>

      <v-spacer />
      <UrlInput />
      <v-spacer />

      <v-btn
        icon
        :to="{ path: 'mypage' }"
      >
        <v-icon>mdi-cog-outline</v-icon>
      </v-btn>
    </v-app-bar>

    <v-main>
      <Nuxt />
    </v-main>
  </v-app>
</template>

<script>
import UrlInput from '~/components/UrlInput.vue'

export default {
  name: 'DefaultLayout',
  components: {
    UrlInput
  }
}
</script>
