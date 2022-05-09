<template>
  <ValidationObserver v-slot="{ passes, validate }">
    <v-container fluid>
      <v-row class="mt-0">
        <v-col cols="7">
          <v-row justify="center">
            <!-- URL入力欄 -->
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

            <!-- 言語選択欄 -->
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
                            class="my-4"
                            @click="validate().then(passes(getCaption))"
                          >
                            字幕を取得する
                          </v-btn>
                        </div>
                        
                        <div v-else>
                          <v-text-field
                            v-model="searchCaption"
                            class="mt-4 mb-n2"
                            placeholder="字幕を検索"
                            dense
                          />
                        </div>
                      </v-col>
                    </v-row>
                  </template>

                  <template #no-data>
                    <div v-if="videoInfo.id">
                      <v-img
                        :src="`https://img.youtube.com/vi/${videoInfo.id}/maxresdefault.jpg`"
                        :aspect-ratio="16/9"
                      />
                    </div>

                    <div v-else>
                      <vue-loading
                        class="mt-16"
                        type="bars"
                        color="#1976D1"
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
            <v-col cols="7">
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

            <v-col cols="4">
              <v-btn
                :disabled="!selectLang.translate || !text"
                :loading="loading.translate"
                color="primary"
                @click="validate().then(passes(translate))"
              >
                DeepLで翻訳する
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

          <v-row justify="center" class="mt-1">
            <v-col cols="11">
              <v-card height="415" outlined :loading="loading.getCharacterCount">
                <div v-if="translatedText">
                  <v-card-text height="300">
                    {{ translatedText }}
                  </v-card-text>
                </div>

                <div v-else>
                  <DoughnutChart
                    :character-count="characterCount"
                    :character-limit="characterLimit"
                  />
                </div>
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
      id: ''
    },
    inputUrlArea: {
      bgColor: '#EEEEEE',// 薄いグレー
      isFocus: false
    },
    inputTextArea: {
      bgColor: '#EEEEEE',// 薄いグレー
      isFocus: false
    },
    translateLang: [
      { text: '日本語', value: 'JA' },
      { text: '英語', value: 'EN' }
    ],
    headers: [
      { text: 'time', value: 'start', width: 75 },
      { text: 'caption', value: 'caption' }
    ],
    selectLang: {
      caption: null,
      translate: null
    },
    captions: [],
    langList: [],
    searchCaption: null,
    text: '',
    translatedText: '',
    characterCount: 0,
    characterLimit: 0
  }),
  async fetch() {
    await this.getCharacterCount()
  },
  watch: {
    'selectLang.caption'(value) {
      this.selectLang.translate = value.match(/(en)/) ? 'JA' : 'EN'
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
    async getLangList() {
      this.loading.getLangList = true

      const url = new URL(this.videoInfo.url)
      this.videoInfo.id = url.searchParams.get('v')

      try {
        this.langList = await this.$axios.$get('langList', {params: {
          videoId: this.videoInfo.id
        }})
      } catch(e) {
        this.$toast.error('動画が存在しません。')
      }

      this.loading.getLangList = false
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
        el.start = this.calcTime(el.start)
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
    async getCharacterCount() {
      this.loading.getCharacterCount = true

      try {
        const res = await this.$axios.$post('character_count')
        this.loading.getCharacterCount = false
        this.characterCount = res.character_count
        this.characterLimit = res.character_limit
        console.log('ok')
      } catch(e) {
        this.$toast.error('文字数カウントの取得に失敗しました。')
      }

      if (this.loading.getCharacterCount) { this.loading.getCharacterCount = false }
    }
  }
}
</script>