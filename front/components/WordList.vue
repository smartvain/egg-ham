<template>
  <v-card outlined>
    <v-data-table
      :headers="headers"
      :items="filteredItems"
      :items-per-page="10"
      :search="searchWord"
      :footer-props="{
        showFirstLastPage: true,
        firstIcon: 'mdi-chevron-double-left',
        lastIcon: 'mdi-chevron-double-right',
        itemsPerPageText: '表示件数：',
        itemsPerPageAllText: '全て',
        itemsPerPageOptions: [
          10, 20, 30, -1
        ]
      }"
      fixed-header
    >
      <template #top>
        <v-row justify="center" class="mt-0">
          <v-col cols="8">
            <v-text-field
              v-model="searchWord"
              placeholder="単語を検索"
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
          @change="addEditedWord(index)"
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
          @change="addEditedWord(index)"
        />

        <div v-else>
          <span v-if="item.mean">{{ item.mean }}</span>
          <span v-else class="grey--text">未入力</span>
        </div>
      </template>

      <template #[`item.video_title`]="{ item, index }">
        <v-text-field
          v-if="editMode"
          v-model="item.video_title"
          dense
          @change="addEditedWord(index)"
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
          @change="addEditedWord(index)"
        />

        <a
          v-else
          :href="item.url"
          target="subwindow"
        >
          {{ item.url }}
        </a>
      </template>

      <template #[`item.weblio`]="{ item }">
        <v-btn
          icon
          @click="moveWeblio(item.text)"
        >
          <v-icon>mdi-search-web</v-icon>
        </v-btn>
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

      <template #no-data>
        <v-card-text class="grey--text lighten-4 text-center text-subtitle-1 my-12">
          保存された単語がありません。
        </v-card-text>
      </template>

      <template #no-results>
        <v-card-text class="grey--text lighten-4 text-center text-subtitle-1 my-12">
          単語が見つかりません。
        </v-card-text>
      </template>

      <template #[`footer.page-text`]="{ pageStart, pageStop, itemsLength }">
        <div v-if="itemsLength !== 0">
          {{ itemsLength }}件中： {{ pageStart }} 〜 {{ pageStop }}
        </div>
        <span v-else>ー</span>
      </template>
    </v-data-table>
  </v-card>
</template>

<script>
import { mapGetters } from 'vuex'
import Mixin from '~/mixins/mixin.js'

export default {
  mixins: [ Mixin ],
  props: {
    filteredItems: { type: Array, default: () => [] },
  },
  data: () => ({
    headers: [
      { text: '単語', value: 'text' },
      { text: '意味', value: 'mean' },
      { text: '動画タイトル', value: 'video_title' },
      { text: 'URL', value: 'url' },
      { text: 'Weblio検索', value: 'weblio', sortable: false },
      { text: '削除', value: 'operation', sortable: false }
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
        this.showMessage('success', '単語を削除しました。')
      } catch (e) {
        console.log(e.message)
      }

      this.loading.deleteWord = false
    },
    async saveWords() {
      if (this.editedWords.length === 0) { return }

      this.loading.saveWords = true
      
      try {
        await this.$axios.$put('words', { words: this.editedWords })
        this.editedWords = []
        this.showMessage('success', '編集内容を保存しました。')
      } catch (e) {
        console.log(e.message)
      }
      
      this.loading.saveWords = false
    },
    addEditedWord(index) {
      const word = this.filteredItems[index]
      for (const item of this.editedWords) {
        if (item.id === word.id) { return }
      }
      this.editedWords.push(word)
    },
    moveWeblio(word) {
      window.open(`https://ejje.weblio.jp/content/${word}`, '_blank')
    },
  }
}
</script>

<style>

</style>