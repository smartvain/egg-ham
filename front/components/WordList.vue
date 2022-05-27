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

    <template #[`item.text`]="{ item, index }">
      <v-text-field
        v-if="editMode"
        v-model="item.text"
        dense
        @change="addWord(index)"
      />

      <div v-else>
        {{ item.text }}
      </div>
    </template>

    <template #[`item.mean`]="{ item, index }">
      <v-text-field
        v-if="editMode"
        v-model="item.mean"
        dense
        @change="addWord(index)"
      />

      <div v-else>
        {{ item.mean }}
      </div>
    </template>

    <template #[`item.video_title`]="{ item, index }">
      <v-text-field
        v-if="editMode"
        v-model="item.video_title"
        dense
        @change="addWord(index)"
      />

      <div v-else>
        {{ item.video_title }}
      </div>
    </template>

    <template #[`item.url`]="{ item, index }">
      <v-text-field
        v-if="editMode"
        v-model="item.url"
        dense
        @change="addWord(index)"
      />

      <a
        v-else
        :href="item.url"
        target="subwindow"
      >
        {{ item.url }}
      </a>
    </template>

    <template #[`item.calcTime`]="{ item, index }">
      <v-text-field
        v-if="editMode"
        v-model="item.calcTime"
        dense
        @change="addWord(index)"
      />

      <a
        v-else
        :href="`${url}&t=${item.start_second}s`"
        target="subwindow"
      >
        {{ item.calcTime }}
      </a>
    </template>

    <template #[`item.word_type`]="{ item, index }">
      <v-select
        v-model="item.word_type"
        :items="wordTypesArray"
        :disabled="!editMode"
        item-text="text"
        item-value="value"
        dense
        @change="addWord(index)"
      />
    </template>

    <template #[`item.operation`]="{ item }">
      <v-btn
        icon
        :disabled="!editMode"
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
    editMode: false,
    editedWords: []
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
      
      for (const word of this.editedWords) {
        word.start_second = this.calcSeconds(word.calcTime)
      }

      try {
        await this.$axios.$put('words', { words: this.editedWords })
        this.editedWords = []
        this.$toast.show('編集内容を保存しました。')
      } catch (e) {
        this.$toast.error('編集内容の保存に失敗しました。')
      }
      
      this.loading.saveWords = false
    },
    addWord(index) {
      const word = this.filteredItems[index]
      for (const item of this.editedWords) {
        if (item.id === word.id) { return }
      }
      this.editedWords.push(word)
    }
  }
}
</script>

<style>

</style>