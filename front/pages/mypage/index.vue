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
          <v-data-table
            :headers="headers"
            :items="filteredWords"
            :items-per-page="15"
          >
            <template #[`item.url`]="{ item }">
              <a
                :href="item.url"
                target="subwindow"
              >
                {{ item.url }}
              </a>
            </template>
          </v-data-table>
        </v-card>
      </v-tab-item>

      <v-tab-item>
        <v-card outlined>
          <v-data-table
            :headers="headers"
            :items="filteredIdiom"
            :items-per-page="15"
          >
            <template #[`header.text`]>慣用句</template>
          </v-data-table>
        </v-card>
      </v-tab-item>
    </v-tabs-items>
  </v-container>
</template>

<script>
export default {
  middleware: 'auth',
  async asyncData({ $axios }) {
    return { words: await $axios.$get('words') }
  },
  data: () => ({
    headers: [
      { text: '単語', value: 'text' },
      { text: '意味', value: 'mean' },
      { text: '動画タイトル', value: 'video_title' },
      { text: 'URL', value: 'url' },
    ],
    tabItems: [ '単語', '慣用句' ],
    tab: null
  }),
  computed: {
    filteredWords() {
      return this.words.filter(word => word.word_type === 1)
    },
    filteredIdiom() {
      return this.words.filter(word => word.word_type === 2)
    }
  }
}
</script>

<style>

</style>