<template>
  <v-data-table
    :headers="headers"
    :items="filteredItems"
    :items-per-page="10"
    :search="searchWord"
    fixed-header
  >
    <template #top>
      <v-row justify="center" class="mt-0">
        <v-col cols="11">
          <v-text-field
            v-model="searchWord"
            placeholder="字幕を検索"
            dense
          />
        </v-col>
      </v-row>
    </template>

    <template #[`header.text`]>
      <slot />
    </template>

    <template #[`item.url`]="{ item }">
      <a
        :href="item.url"
        target="subwindow"
      >
        {{ item.url }}
      </a>
    </template>

    <template #[`item.calcTime`]="{ item }">
      <a
        :href="`${url}&t=${item.start_second}s`"
        target="subwindow"
      >
        {{ item.calcTime }}
      </a>
    </template>

    <template #[`item.word_type`]="{ item }">
      <v-select
        v-model="item.word_type"
        :items="wordType"
        item-text="type"
        item-value="value"
        dense
      />
    </template>

    <template #[`item.operation`]="{ item }">
      <v-btn
        icon
        :loading="loading.deleteWord === item.id"
        @click="deleteWord(item.id)"
      >
        <v-icon>mdi-delete-empty</v-icon>
      </v-btn>
    </template>
  </v-data-table>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  props: {
    filteredItems: { type: Array, default: () => [] }
  },
  data: () => ({
    headers: [
      { text: '単語', value: 'text' },
      { text: '意味', value: 'mean' },
      { text: '動画タイトル', value: 'video_title' },
      { text: 'URL', value: 'url' },
      { text: '時間', value: 'calcTime', width: 80 },
      { text: '種別', value: 'word_type', sortable: false },
      { text: '操作', value: 'operation', sortable: false }
    ],
    wordType: [
      { type: '単語', value: 1 },
      { type: '慣用句', value: 2 },
    ],
    loading: {
      deleteWord: false
    },
    searchWord: null
  }),
  computed:{
    ...mapGetters([ 'url' ]),
  },
  methods: {
    async deleteWord(wordId) {
      this.loading.deleteWord = wordId

      try {
        await this.$axios.$delete(`word/${wordId}`)
        await this.$nuxt.refresh()
      } catch (e) {
        this.$toast.error('削除に失敗しました。')
      }

      this.loading.deleteWord = false
    }
  }
}
</script>

<style>

</style>