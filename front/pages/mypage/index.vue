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
            :items="words"
            :items-per-page="15"
            :custom-filter="WordType"
            search="1"
          >
            <template #[`item.word_type`] />
          </v-data-table>
        </v-card>
      </v-col>

      <v-divider vertical />

      <v-col cols="5">
        <v-card outlined>
          <v-data-table
            :headers="headers"
            :items="words"
            :items-per-page="15"
            :custom-filter="WordType"
            search="2"
          >
            <template #[`item.word_type`] />
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
      { text: '意味', value: 'mean' },
      { value: 'word_type', sortable: false},
    ]
  }),
  methods: {
    WordType(value, search) {
      return value.word_type === search
    }
  }
}
</script>

<style>

</style>