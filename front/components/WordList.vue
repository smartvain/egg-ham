<template>
  <v-data-table
    :headers="headers"
    :items="filteredItems"
    :items-per-page="10"
  >
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

    <template #[`item.time`]="{ item }">
      <a
        :href="`${url}&t=${item.time}s`"
        target="subwindow"
      >
        {{ item.time | calcTime }}
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
    headers: { type: Array },
    filteredItems: { type: Array }
  },
  data: () => ({
    wordType: [
      { type: '単語', value: 1 },
      { type: '慣用句', value: 2 },
    ],
    loading: {
      deleteWord: false
    }
  }),
  computed:{
    ...mapGetters([ 'url' ]),
  },
  filters: {
    calcTime(time) {
      const min = Math.floor(time % 3600 / 60);
      let rem = String(Math.floor(time % 60));
      if (rem < 10) { rem = rem.padStart(2, '0') }

      return `${min}:${rem}`
    }
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