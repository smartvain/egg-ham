<template>
  <v-container fluid>
    <v-tabs v-model="tab">
      <v-tab v-for="item in tabItems" :key="item">
        {{ item }}
      </v-tab>
    </v-tabs>

    <v-tabs-items v-model="tab">
      <v-tab-item>
        <WordList :filtered-items="words">
          単語
        </WordList>
      </v-tab-item>

      <v-tab-item>
        <Setting />
      </v-tab-item>
    </v-tabs-items>
  </v-container>
</template>

<script>
import WordList from '~/components/WordList.vue'
import Setting from '~/components/Setting/SettingMenu.vue'
import Mixin from '~/mixins/mixin.js'

export default {
  components: { WordList, Setting },
  mixins: [ Mixin ],
  middleware: 'auth',
  async asyncData({ $axios, $auth }) {
    return { words: await $axios.$get('words', { params: {user_id: $auth.$state.user.id} }) }
  },
  data: () => ({
    tabItems: [ '単語', '設定' ],
    tab: null
  }),
}
</script>

<style>

</style>