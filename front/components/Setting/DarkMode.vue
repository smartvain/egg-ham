<template>
  <div>
    <v-card-title>ダークモード設定</v-card-title>

    <v-card-text>
      <v-switch
        v-model="localTheme"
        :prepend-icon="themeIcon"
      />
    </v-card-text>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  data: () => ({
    loading: {
      changeTheme: false
    },
    localTheme: false
  }),
  computed: {
    ...mapGetters(['theme']),
    themeIcon() {
      return this.localTheme ? 'mdi-weather-night' : 'mdi-weather-sunny'
    }
  },
  created() {
    this.localTheme = this.theme
  },
  watch: {
    async localTheme(value) {
      this.loading.changeTheme = true
      
      this.$vuetify.theme.dark = value
      this.$store.commit('setTheme', value)
      try {
        await this.$axios.$put('user/theme', { theme: value, id: this.$auth.user.id })
      } catch (e) {
        this.$toast.error(e.message)
      }

      this.loading.changeTheme = false
    }
  },
}
</script>

<style>

</style>