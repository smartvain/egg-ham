<template>
  <v-container fluid>
    <v-row>
      <v-col cols="2">

      </v-col>

      <v-divider vertical />

      <v-col cols="5">
        <v-card outlined>
          <v-data-table
            :headers="headers"
            :items="filteredWords"
            :items-per-page="15"
          >
          </v-data-table>
        </v-card>
      </v-col>

      <v-col cols="5">
        <v-card outlined>
          <v-data-table
            :headers="headers"
            :items="filteredIdiom"
            :items-per-page="15"
          >
            <template #[`header.text`]>慣用句</template>
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  async asyncData({ $axios }) {
    return {
      words: await $axios.$get('words')
    }
  },
  data: () => ({
    headers: [
      { text: '単語', value: 'text' },
      { text: '意味', value: 'mean' }
    ]
  }),
  computed: {
    filteredWords() {
      return this.words.filter(word => {
        return word.word_type === 1
      })
    },
    filteredIdiom() {
      return this.words.filter(word => {
        return word.word_type === 2
      })
    }
  }
}
</script>

<style>

</style>