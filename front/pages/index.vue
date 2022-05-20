<template>
  <v-container fluid>
    <v-row>
      <v-col cols="7">
        <v-row justify="center">
          <v-col cols="11">
            <div v-if="!videoId" :style="`height: ${videoHeight}px`">
              <v-hover v-slot="{ hover }">
                <v-row align-content="center" style="height: 100%" justify="center">
                  <v-col cols="12" md="1">
                    <v-img
                      style="z-index: 9999"
                      class="logo ml-n2 mt-6"
                      :class="{ 'on-hover': !hover }"
                      transition="scroll-x-transition"
                      min-width="90"
                      :src="require('~/assets/img/icon.png')"
                    ></v-img>
                  </v-col>

                  <v-col cols="12" md="7">
                    <v-img
                      class="title ml-n9"
                      :class="{ 'on-hover': hover }"
                      min-width="502"
                      :src="require('~/assets/img/title.png')"
                    />
                  </v-col>
                </v-row>
              </v-hover>
            </div>

            <div
              v-else
              v-show="toggleDisplay"
              ref="iframe"
              :style="`height: ${videoHeight}px`"
              v-html="iframe"
            />

            <v-card
              class="overflow-y-auto overflow-x-hidden mt-2"
              outlined
            >
              <v-data-table
                :headers="headers"
                :items="captions"
                :items-per-page="-1"
                :search="searchCaption"
                :hide-default-header="captions.length === 0"
                :height="captions.length === 0 ? 250 : 615"
                hide-default-footer
                fixed-header
              >
                <template #top>
                  <v-row justify="center">
                    <v-col cols="11">
                      <div v-show="captions.length !== 0">
                        <v-row class="mt-0">
                          <v-col cols="10">
                            <v-text-field
                              v-model="searchCaption"
                              placeholder="字幕を検索"
                              dense
                            />
                          </v-col>

                          <v-col cols="2">
                            <v-btn
                              :class="borderRadius"
                              color="primary"
                              @click="toggleDisplay = !toggleDisplay"
                            >
                              <span>{{ toggleDisplay ? '非表示' : '表示' }}</span>
                            </v-btn>
                          </v-col>
                        </v-row>
                      </div>
                    </v-col>
                  </v-row>
                </template>

                <template #[`item.calcTime`]="{ item }">
                  <a
                    :href="`${url}&t=${item.start}s`"
                    target="subwindow"
                  >
                    {{ item.calcTime }}
                  </a>
                </template>

                <template #[`item.copy`]="{ item }">
                  <v-btn icon @click="text += item.caption">
                    <v-icon>mdi-content-copy</v-icon>
                  </v-btn>
                </template>

                <template #no-data>
                  <v-card-text
                    v-if="!loading.getCaption"
                    class="grey--text lighten-4 text-center text-subtitle-1 mt-16"
                  >
                    URLを入力後、字幕言語を選択すると<br>「字幕を取得する」ボタンをクリックできるようになります。
                  </v-card-text>
                </template>
              </v-data-table>
            </v-card>
          </v-col>
        </v-row>
      </v-col>

      <v-divider vertical />

      <v-col cols="5">
        <ValidationObserver v-slot="{ passes, validate }">
          <v-row justify="center">
            <v-col cols="5">
              <v-select
                v-model="translateLang"
                :items="translateLangList"
                item-text="text"
                item-value="value"
                placeholder="翻訳言語を選択"
                no-data-text="翻訳言語がありません"
                dense
              />
            </v-col>

            <v-col cols="3" align="right">
              <v-btn
                :disabled="!translateLang || !text"
                :loading="loading.translate"
                :class="borderRadius"
                color="primary"
                block
                @click="validate().then(passes(translate))"
              >
                DeepL翻訳
              </v-btn>
            </v-col>

            <v-col cols="3" align="right">
              <v-btn
                :disabled="!text"
                :class="borderRadius"
                color="primary"
                block
                @click="saveText(text)"
              >
                単語を追加
              </v-btn>
            </v-col>
          </v-row>

          <v-row justify="center" class="mt-1">
            <v-col cols="11">
              <ValidationProvider
                v-slot="{ errors }"
                rules="max:1000"
                name="テキスト"
              >
                <v-textarea
                  v-model="text"
                  :error-messages="errors"
                  :background-color="textArea.bgColor"
                  :flat="!textArea.isFocus"
                  placeholder="翻訳したい単語を入力"
                  dense solo clearable
                  @focus="
                    textArea.isFocus = true
                    textArea.bgColor = 'white'"
                  @blur="
                    textArea.isFocus = false
                    textArea.bgColor = '#EEEEEE'"
                  @keydown.enter.meta.exact="if (text) { saveText(text) }"
                />
              </ValidationProvider>
            </v-col>
          </v-row>
        </ValidationObserver>

        <v-row justify="center" class="mt-n5">
          <v-col cols="11">
            <v-card
              height="435"
              :loading="loading.getCharacterCount"
              outlined
            >
              <v-tabs
                v-model="tab"
                background-color="transparent"
                grow
              >
                <v-tab v-for="item in tabItems" :key="item">
                  {{ item }}
                </v-tab>
              </v-tabs>

              <v-tabs-items v-model="tab">
                <v-tab-item>
                  <transition>
                    <div v-show="characterCount">
                      <DoughnutChart
                        class="mt-6"
                        :character-count="characterCount"
                        :character-limit="characterLimit"
                        :width="chartSize"
                        :height="chartSize"
                      />
                    </div>
                  </transition>
                </v-tab-item>
                
                <v-tab-item>
                  <div v-if="!translatedText">
                    <v-card-text class="grey--text lighten-4 text-center">
                      翻訳された単語はありません
                    </v-card-text>
                  </div>

                  <div v-else>
                    <v-card-text>{{ translatedText }}</v-card-text>
                  </div>
                </v-tab-item>

                <v-tab-item>
                  <v-data-table
                    height="385"
                    :headers="wordsHeaders"
                    :items="words"
                    :items-per-page="-1"
                    hide-default-header
                    hide-default-footer
                    no-data-text="追加された単語がありません"
                  >
                    <template #[`item.mean`]="{ item }">
                      <v-text-field
                        v-model="item.mean"
                        class="mt-1 mb-n2"
                        dense
                      />
                    </template>

                    <template #[`item.delete`]="{ index }">
                      <v-btn icon @click="deleteWord(index)">
                        <v-icon>mdi-close</v-icon>
                      </v-btn>
                    </template>

                    <template #foot>
                      <v-btn
                        :loading="loading.storeWords"
                        :disabled="words.length === 0"
                        :class="`mt-2 ml-2 ${borderRadius}`"
                        color="primary"
                        dense solo
                        @click="storeWords()"
                      >
                        全て保存
                      </v-btn>
                    </template>
                  </v-data-table>
                </v-tab-item>
              </v-tabs-items>
            </v-card>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { mapGetters } from 'vuex'
import DoughnutChart from '~/components/DoughnutChart.vue'

export default {
  name: 'IndexPage',
  components: { DoughnutChart },
  data: () => ({
    loading: {
      translate: false,
      getCharacterCount: false,
      storeWords: false
    },
    textArea: {
      bgColor: '#EEEEEE',
      isFocus: false
    },
    translateLangList: [
      { text: '日本語', value: 'JA' },
      { text: '英語', value: 'EN' }
    ],
    headers: [
      { text: '時間', value: 'calcTime', width: 75 },
      { text: '字幕', value: 'caption' },
      { text: 'コピー', value: 'copy', sortable: false, width: 70 }
    ],
    wordsHeaders: [
      { text: '文字', value: 'text' },
      { text: '意味', value: 'mean' },
      { text: '削除', value: 'delete' }
    ],
    tabItems: [ 'DEEPL使用量', '翻訳単語', '追加した単語' ],
    borderRadius: 'rounded-lg',
    words: [],
    toggleDisplay: true,
    tab: null,
    text: null,
    translatedText: null,
    searchCaption: null,
    translateLang: null,
    characterCount: null,
    characterLimit: null,
    chartSize: 330,
    videoHeight: 415,
  }),
  async fetch() {
    await this.getCharacterCount()
  },
  computed: {
    ...mapGetters(['url', 'videoId', 'captions', 'captionLang', 'iframe'])
  },
  watch: {
    videoId(value) {
      if (value) { this.getVideoInfo(value) }
    },
    captionLang(value) {
      if (value) { this.translateLang = value.match(/(en)/) ? 'JA' : 'EN' }
    },
  },
  methods: {
    async getCharacterCount() {
      this.loading.getCharacterCount = true

      try {
        const res = await this.$axios.$post('character_count')
        this.loading.getCharacterCount = false
        this.characterCount = res.character_count
        this.characterLimit = res.character_limit
      } catch(e) {
        this.$toast.error('文字数カウントの取得に失敗しました。')
      }

      if (this.loading.getCharacterCount) { this.loading.getCharacterCount = false }
    },
    async getVideoInfo(videoId) {
      const res = await this.$axios.$get('videoInfo', { params: { videoId }})
      const iframe = this.createElementFromHTML(res.html)
      iframe.setAttribute('height', (this.getElWidth(this.$refs.iframe) / 16) * 9)
      iframe.setAttribute('width', '100%')

      this.$store.commit('setIframe', iframe.outerHTML)
    },
    createElementFromHTML(html) {
      const tempEl = document.createElement('div');
      tempEl.innerHTML = html;
      return tempEl.firstElementChild;
    },
    getElWidth(el) {
      const dom = el
      const rect = dom.getBoundingClientRect();
      return rect.width
    },
    async translate() {
      this.loading.translate = true

      try {
        const res = await this.$axios.$post('translate', {
          text: this.text,
          lang: this.translateLang
        })
        this.translatedText = res.translations[0].text
      } catch(e) {
        this.$toast.error('翻訳に失敗しました。')
      }
      
      this.saveText(this.text, this.translatedText)
      
      this.loading.translate = false

      this.getCharacterCount()
    },
    saveText(text, mean) {
      const word = 1
      const idiom = 2
      const word_type = text.includes('　') || text.includes(' ') ? idiom : word
      this.words.push({ text, mean, word_type })
      
      if (!mean) {this.text = ''}
    },
    deleteWord(index) {
      this.words.splice(index, 1)
    },
    async storeWords() {
      this.loading.storeWords = true

      try {
        await this.$axios.$post('words', this.words)
        this.$toast.show('単語を全て保存しました。')
      } catch(e) {
        this.$toast.error('単語の保存に失敗しました。')
      }

      this.words = []

      this.loading.storeWords = false
    }
  }
}
</script>

<style scoped>
.v-enter-active, .v-leave-active {
  transition: opacity .8s
}

.v-enter, .v-leave-to{
  opacity: 0
}

.logo {
  transition: all .2s
}

.logo:not(.on-hover) {
  transform: scale(1.19)
}

.title {
  transition: all .2s
}

.title:not(.on-hover) {
  opacity: 0;
  transform: translateX(-10px);
}
</style>