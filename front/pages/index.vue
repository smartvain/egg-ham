<template>
  <ValidationObserver v-slot="{ passes, validate }">
    <v-container fluid>
      <v-row class="mt-0">
        <v-col cols="7">
          <v-row justify="center">
            <v-col cols="8">
              <ValidationProvider
                v-slot="{ errors }"
                rules="is_youtube_url"
                name="URL"
              >
                <v-text-field
                  v-model="videoInfo.url"
                  :error-messages="errors"
                  :background-color="inputUrlArea.bgColor"
                  :flat="!inputUrlArea.isFocus"
                  placeholder="URLを入力"
                  dense solo clearable
                  @focus="
                    inputUrlArea.isFocus = true
                    inputUrlArea.bgColor = 'white'"
                  @blur="
                    inputUrlArea.isFocus = false
                    inputUrlArea.bgColor = '#EEEEEE'
                    if (videoInfo.url) { langList = []; getLangList() }"
                  @change="initCaption()"
                  @click:clear="initCaption()"
                />
              </ValidationProvider>
            </v-col>

            <v-col cols="3">
              <v-select
                v-model="selectLang.caption"
                :loading="loading.getLangList"
                :items="langList"
                item-text="text"
                item-value="value"
                placeholder="字幕言語を選択"
                no-data-text="URLが入力されていません"
                dense
              >
                <template #no-data>
                  <div v-if="loading.getLangList" class="text-center">
                    <v-progress-circular color="primary" indeterminate/>
                  </div>

                  <div v-else class="text-center">
                    <span class="grey--text">URLが入力されていません</span>
                  </div>
                </template>
              </v-select>
            </v-col>
          </v-row>

          <v-row justify="center" class="mt-n6">
            <v-col cols="11">
              <v-card
                class="overflow-y-auto overflow-x-hidden"
                outlined
              >
                <v-data-table
                  :headers="headers"
                  :items="captions"
                  :items-per-page="-1"
                  :search="searchCaption"
                  :hide-default-header="captions.length === 0"
                  height="530"
                  fixed-header
                  hide-default-footer
                >
                  <template #top>
                    <v-row justify="center">
                      <v-col cols="11" align="right">
                        <div v-if="captions.length === 0">
                          <v-btn
                            :disabled="!selectLang.caption"
                            :loading="loading.getCaption"
                            color="primary"
                            class="my-3"
                            @click="validate().then(passes(getCaption))"
                          >
                            字幕を取得する
                          </v-btn>
                        </div>
                        
                        <div v-else>
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
                                color="primary"
                                @click="captions = []"
                              >
                                リセット
                              </v-btn>
                            </v-col>
                          </v-row>
                        </div>
                      </v-col>
                    </v-row>
                  </template>

                  <template #[`item.calcTime`]="{ item }">
                    <a
                      :href="`${videoInfo.url}&t=${item.start}s`"
                      target="subwindow"
                    >
                      {{ item.calcTime }}
                    </a>
                  </template>

                  <template #[`item.copy`]="{ item }">
                    <v-icon @click="text += item.caption">
                      mdi-content-copy
                    </v-icon>
                  </template>

                  <template #no-data>
                    <div
                      v-if="videoInfo.id"
                      ref="iframe"
                      v-html="videoInfo.html"
                    />

                    <div v-else>
                      <vue-loading
                        class="mt-16"
                        type="bars"
                        color="#1095FE"
                        :size="{ width: '50px', height: '50px' }"
                      />
                    </div>
                  </template>
                </v-data-table>
              </v-card>
            </v-col>
          </v-row>
        </v-col>

        <v-divider vertical />

        <v-col cols="5">
          <v-row justify="center">
            <v-col cols="5">
              <v-select
                v-model="selectLang.translate"
                :items="translateLang"
                item-text="text"
                item-value="value"
                placeholder="翻訳言語を選択"
                no-data-text="翻訳言語がありません"
                dense
              />
            </v-col>

            <v-col cols="3" align="right">
              <v-btn
                :disabled="!selectLang.translate || !text"
                :loading="loading.translate"
                color="primary"
                @click="validate().then(passes(translate))"
              >
                DeepL翻訳
              </v-btn>
            </v-col>

            <v-col cols="3" align="right">
              <v-btn
                :disabled="!text"
                color="primary"
                @click="validate().then(passes(saveText))"
              >
                単語を保存
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
                  :background-color="inputTextArea.bgColor"
                  :flat="!inputTextArea.isFocus"
                  placeholder="翻訳したい文を入力"
                  dense solo clearable
                  @focus="
                    inputTextArea.isFocus = true
                    inputTextArea.bgColor = 'white'"
                  @blur="
                    inputTextArea.isFocus = false
                    inputTextArea.bgColor = '#EEEEEE'"
                />
              </ValidationProvider>
            </v-col>
          </v-row>

          <v-row justify="center" class="mt-0">
            <v-col cols="11">
              <v-card
                height="415"
                :loading="loading.getCharacterCount"
                outlined
              >
                <v-tabs
                  v-model="tab"
                  background-color="transparent"
                  grow
                >
                  <v-tab v-for="item in items" :key="item">
                    {{ item }}
                  </v-tab>
                </v-tabs>

                <v-tabs-items v-model="tab">
                  <v-tab-item>
                    <transition>
                      <div v-show="characterCount">
                        <DoughnutChart
                          class="mt-2"
                          :character-count="characterCount"
                          :character-limit="characterLimit"
                          :width="chartSize"
                          :height="chartSize"
                        />
                      </div>
                    </transition>
                  </v-tab-item>
                  
                  <v-tab-item>
                    <v-card-text>
                      {{ translatedText }}
                    </v-card-text>
                  </v-tab-item>

                  <v-tab-item>
                    <v-data-table
                      height="365"
                      :headers="sentencesHeaders"
                      :items="sentences"
                      :items-per-page="-1"
                      :search="searchSentence"
                      hide-default-header
                      hide-default-footer
                      no-data-text="保存された単語がありません"
                    >
                      <template #[`item.mean`]="{ item, index }">
                        <v-text-field
                          v-model="newMeans[index]"
                          class="mt-1 mb-n2"
                          :value="item.mean"
                          dense
                          @input="item.mean = newMeans[index]"
                        />
                      </template>

                      <template #[`item.delete`]="{ index }">
                        <v-icon @click="deleteSentence(index)">
                          mdi-close
                        </v-icon>
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
  </ValidationObserver>
</template>

<script>
import { VueLoading } from 'vue-loading-template'
import DoughnutChart from '~/components/DoughnutChart.vue'

export default {
  name: 'IndexPage',
  components: {
    DoughnutChart, VueLoading
  },
  data: () => ({
    loading: {
      getLangList: false,
      getCaption: false,
      translate: false,
      getCharacterCount: false
    },
    videoInfo: {
      url: '',
      id: '',
      title: '',
      html: null
    },
    inputUrlArea: {
      bgColor: '#EEEEEE',
      isFocus: false
    },
    inputTextArea: {
      bgColor: '#EEEEEE',
      isFocus: false
    },
    translateLang: [
      { text: '日本語', value: 'JA' },
      { text: '英語', value: 'EN' }
    ],
    headers: [
      { text: '時間', value: 'calcTime', width: 75 },
      { text: '字幕', value: 'caption' },
      { text: 'コピー', value: 'copy', sortable: false, width: 70 }
    ],
    sentencesHeaders: [
      { text: '文字', value: 'text' },
      { text: '意味', value: 'mean' },
      { text: '削除', value: 'delete' }
    ],
    selectLang: {
      caption: null,
      translate: null
    },
    sentences: [],
    captions: [],
    langList: [],
    text: '',
    translatedText: '',
    switching: 1,
    searchCaption: null,
    searchSentence: null,
    characterCount: null,
    characterLimit: null,
    chartSize: 330,
    copyBtnSize: 33,
    tab: null,
    items: [
      'DEEPL使用量', '翻訳文', '保存した単語'
    ],
    newMeans: []
  }),
  async fetch() {
    await this.getCharacterCount()
  },
  watch: {
    'selectLang.caption'(value) {
      if (value) {
        this.selectLang.translate = value.match(/(en)/) ? 'JA' : 'EN'
      }
    },
    'videoInfo.id'(value) {
      this.getVideoInfo(value)
    }
  },
  created() {
    this.videoInfo.url = 'https://www.youtube.com/watch?v=NoJXn-Fh6CU&t=19s'
    // this.videoInfo.id = 'NoJXn-Fh6CU'
    // this.selectLang.caption = 'en-US'
    // this.getCaption()
    // ------------------------------------------
  },
  methods: {
    initCaption() {
      this.videoInfo.id = ''
      this.selectLang.caption = null
      this.captions = []
    },
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
    async getLangList() {
      this.loading.getLangList = true

      const url = new URL(this.videoInfo.url)
      this.videoInfo.id = url.searchParams.get('v')
      this.videoInfo.url = `${this.videoInfo.url.replace(/\?.*$/,"")}?v=${this.videoInfo.id}`

      try {
        this.langList = await this.$axios.$get('langList', {params: {
          videoId: this.videoInfo.id
        }})
      } catch(e) {
        this.$toast.error('動画が存在しません。')
      }

      this.loading.getLangList = false
    },
    async getVideoInfo(videoId) {
      const res = await this.$axios.$get(`https://www.youtube.com/oembed?url=https://www.youtube.com/watch?v=${videoId}&format=json`)

      const iframe = this.createElementFromHTML(res.html)
      iframe.setAttribute('height', (this.getElWidth(this.$refs.iframe) / 16) * 9)
      iframe.setAttribute('width', '100%')

      this.videoInfo.title = res.title
      this.videoInfo.html = iframe.outerHTML
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
    async getCaption() {
      this.loading.getCaption = true

      try {
        var res = await this.$axios.$get('caption', {params: {
          videoId: this.videoInfo.id,
          lang: this.selectLang.caption
        }})
      } catch(e) {
        this.$toast.error('字幕の取得に失敗しました。')
      }

      for (const el of res) {
        el.calcTime = this.calcTime(el.start)
      }
      this.captions = res

      this.loading.getCaption = false
    },
    calcTime(time) {
      const min = Math.floor(time % 3600 / 60);
      let rem = String(Math.floor(time % 60));
      if (rem < 10) { rem = rem.padStart(2, '0') }

      return `${min}:${rem}`
    },
    async translate() {
      this.loading.translate = true

      try {
        const res = await this.$axios.$post('translate', {
          text: this.text,
          lang: this.selectLang.translate
        })
        this.translatedText = res.translations[0].text
      } catch(e) {
        this.$toast.error('翻訳に失敗しました。')
      }
      
      this.loading.translate = false

      await this.getCharacterCount()
    },
    saveText() {
      this.sentences.push({
        text: this.text,
        mean: null
      })
    },
    deleteSentence(idx) {
      this.sentences.splice(idx, 1)
    }
  }
}
</script>

<style scoped>
.v-enter-active, .v-leave-active {
    transition: opacity .8s
}

.v-enter, .v-leave-to{
    opacity: 0;
}
</style>