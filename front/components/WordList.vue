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
        <v-col cols="8">
          <v-text-field
            v-model="searchWord"
            :placeholder="`${wordType.text}を検索`"
            dense
          />
        </v-col>

        <v-col cols="1">
          <v-btn
            color="primary"
            dense solo block
            :outlined="editMode"
            @click="editMode = !editMode"
          >
            編集モード
          </v-btn>
        </v-col>

        <v-col cols="1" align="right">
          <v-btn
            color="primary"
            dense solo block
            :loading="loading.saveWords"
            :disabled="!editMode"
            @click="saveWords()"
          >
            保存する
          </v-btn>
        </v-col>
      </v-row>
    </template>

    <template #[`header.text`]>
      <slot />
    </template>

    <template #[`item.text`]="{ item }">
      <v-text-field
        v-model="item.text"
        :disabled="!editMode"
      ></v-text-field>
    </template>

    <template #[`item.mean`]="{ item }">
      <v-text-field
        v-model="item.mean"
        :disabled="!editMode"
      ></v-text-field>
    </template>

    <template #[`item.video_title`]="{ item }">
      <v-text-field
        v-model="item.video_title"
        :disabled="!editMode"
      ></v-text-field>
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
        :items="wordTypesArray"
        item-text="text"
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
import Mixin from '~/mixins/mixin.js'

export default {
  mixins: [ Mixin ],
  props: {
    filteredItems: { type: Array, default: () => [] },
    wordType: { type: Object, default: () => {} }
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
    loading: {
      deleteWord: false,
      saveWords: false
    },
    searchWord: null,
    editMode: false
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
    },
    async saveWords() {
      this.loading.saveWords = true

      try {
        await this.$axios.$put('words', { words: this.filteredItems })
        this.$toast.show('編集内容を保存しました。')
      } catch (e) {
        this.$toast.error('編集内容の保存に失敗しました。')
      }
      
      this.loading.saveWords = false
    }
  }
}
</script>

<style>

</style>