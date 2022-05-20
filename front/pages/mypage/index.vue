<template>
  <v-container fluid>
    <v-tabs v-model="tab">
      <v-tab v-for="item in tabItems" :key="item">
        {{ item }}
      </v-tab>
    </v-tabs>

    <v-tabs-items v-model="tab">
      <v-tab-item>
        <v-card outlined>
          <WordList
            :headers="headers"
            :filteredItems="filteredWords"
          >
            単語
          </WordList>
        </v-card>
      </v-tab-item>

      <v-tab-item>
        <v-card outlined>
          <WordList
            :headers="headers"
            :filteredItems="filteredIdioms"
          >
            慣用句
          </WordList>
        </v-card>
      </v-tab-item>
    </v-tabs-items>
  </v-container>
</template>

<script>
import WordList from '~/components/WordList.vue'

export default {
  middleware: 'auth',
  components: { WordList },
  async asyncData({ $axios }) {
    return { words: await $axios.$get('words') }
  },
  data: () => ({
    headers: [
      { text: '単語', value: 'text' },
      { text: '意味', value: 'mean' },
      { text: '動画タイトル', value: 'video_title' },
      { text: 'URL', value: 'url' },
      { text: '時間', value: 'time', width: 80 },
      { text: '種別', value: 'word_type', sortable: false },
      { text: '操作', value: 'operation', sortable: false }
    ],
    tabItems: [ '単語', '慣用句' ],
    tab: null
  }),
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