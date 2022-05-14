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
          ></v-data-table>
        </v-card>
      </v-col>

      <v-divider vertical />

      <v-col cols="5">
        <v-card outlined>
          <v-data-table
            :headers="headers"
            :items="words"
            :items-per-page="15"
          ></v-data-table>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  data: () => ({
    headers: [
      { text: '単語', value: 'text' },
      { text: '意味', value: 'mean' }
    ]
  }),
  async asyncData({ $axios }) {
    return {
      words: await $axios.$get('words')
    }
  }
}
</script>

<style>

</style>