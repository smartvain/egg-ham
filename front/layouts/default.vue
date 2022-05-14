<template>
  <v-app dark>
    <v-app-bar :clipped-left="clipped" fixed app elevation="2" height="50">
      <v-toolbar-title v-text="'yuleapp'" />
      
      <v-spacer />

      <UrlInput
        :url="url"
      />
      
      <v-spacer />

      <v-btn
        icon
        @click.stop="clipped = !clipped"
        :to="{
          path: toggleUrl,
        }"
      >
        <v-icon>mdi-cog-outline</v-icon>
      </v-btn>
    </v-app-bar>

    <v-main>
      <Nuxt />
    </v-main>

    <v-footer :absolute="!fixed" app>
      <span>&copy; {{ new Date().getFullYear() }}</span>
    </v-footer>
  </v-app>
</template>

<script>
import UrlInput from '~/components/UrlInput.vue'

export default {
  name: 'DefaultLayout',
  components: {
    UrlInput
  },
  data: () => ({
    clipped: false,
    drawer: false,
    fixed: false,
    items: [
      {
        icon: 'mdi-apps',
        title: 'Welcome',
        to: '/',
      },
      {
        icon: 'mdi-chart-bubble',
        title: 'Inspire',
        to: '/inspire',
      },
    ],
    miniVariant: false,
    right: true,
    rightDrawer: false,
  }),
  computed: {
    toggleUrl() {
      let path
      switch (this.$route.path) {
        case '/'      : path = '/mypage'; break
        case '/mypage': path = '/'      ; break
      }

      return path
    }
  }
}
</script>
