<template>
  <v-container fluid>
    <v-tabs v-model="tab">
      <v-tab v-for="item in tabItems" :key="item">
        {{ item }}
      </v-tab>
    </v-tabs>

    <v-tabs-items v-model="tab">
      <v-tab-item>
        <WordList
          :filtered-items="filteredWords"
          :word-type="wordTypes.word"
        >
          単語
        </WordList>
      </v-tab-item>

      <v-tab-item>
        <WordList
          :filtered-items="filteredIdioms"
          :word-type="wordTypes.idiom"
        >
          慣用句
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
  async asyncData({ $axios }) {
    return { words: await $axios.$get('words') }
  },
  data: () => ({
    tabItems: [ '単語', '慣用句', '設定' ],
    tab: null
  }),
  fetch() {
    for (const word of this.words) {
      word.calcTime = this.calcTime(word.start_second)
    }
  },
  computed: {
    filteredWords() {
      return this.words.filter(word => word.word_type === 1)
    },
    filteredIdioms() {
      return this.words.filter(word => word.word_type === 2)
    }
  }
}
</script>

<style>

</style>