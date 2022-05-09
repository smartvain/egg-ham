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
                      <v-card
                        class="mx-auto mt-16"
                        color="grey lighten-4"
                        max-width="600"
                      >
                        <v-card-title>
                          <v-icon
                            :color="checking ? 'red lighten-2' : 'indigo'"
                            class="mr-12"
                            size="64"
                            @click="takePulse"
                          >
                            mdi-heart-pulse
                          </v-icon>
                          <v-row align="start">
                            <div class="text-caption grey--text text-uppercase">
                              Heart rate
                            </div>
                            <div>
                              <span
                                class="text-h3 font-weight-black"
                                v-text="avg || '—'"
                              ></span>
                              <strong v-if="avg">BPM</strong>
                            </div>
                          </v-row>

                          <v-spacer></v-spacer>

                          <!-- <v-btn
                            icon
                            class="align-self-start"
                            size="28"
                          >
                            <v-icon>mdi-arrow-right-thick</v-icon>
                          </v-btn> -->
                        </v-card-title>

                        <v-sheet color="transparent">
                          <v-sparkline
                            :key="String(avg)"
                            :smooth="16"
                            :gradient="['#f72047', '#ffd200', '#1feaea']"
                            :line-width="3"
                            :value="heartbeats"
                            auto-draw
                            stroke-linecap="round"
                          ></v-sparkline>
                        </v-sheet>
                      </v-card>
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
              <v-card height="415" outlined>
                <div v-if="translatedText">
                  <v-card-text height="300">
                    {{ translatedText }}
                  </v-card-text>
                </div>

                <div v-else>
                  <DoughnutChart
                    :characterCount="characterCount"
                    :characterLimit="characterLimit"
                  ></DoughnutChart>
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
import DoughnutChart from '~/components/DoughnutChart.vue'

const exhale = ms => new Promise(resolve => setTimeout(resolve, ms))

export default {
  name: 'IndexPage',
  components: {
    DoughnutChart
  },
  data: () => ({
    loading: {
      getLangList: false,
      getCaption: false,
      translate: false
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
    // sparklines
    checking: false,
    heartbeats: [],
    // -----------
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
  created() {
    // this.videoInfo.url = 'https://www.youtube.com/watch?v=NoJXn-Fh6CU&t=19s'
    // this.videoInfo.id = 'NoJXn-Fh6CU'
    // this.selectLang.caption = 'en-US'
    // this.getCaption()
    // ------------------------------------------

    setInterval(this.takePulse, 3000)
    this.takePulse(false)
  },
  computed: {
    avg() {
      const sum = this.heartbeats.reduce((acc, cur) => acc + cur, 0)
      const length = this.heartbeats.length

      if (!sum && !length) return 0

      return Math.ceil(sum / length)
    },
  },
  watch: {
    'selectLang.caption'(value) {
      this.selectLang.translate = value.match(/(en)/) ? 'JA' : 'EN'
    }
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
    heartbeat () {
      return Math.ceil(Math.random() * (120 - 80) + 80)
    },
    async takePulse (inhale = true) {
      this.checking = true

      inhale && await exhale(1000)

      this.heartbeats = Array.from({ length: 20 }, this.heartbeat)

      this.checking = false
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

      try {
        await this.getCharacterCount()
      } catch(e) {
        console.log(e)
      }
    },
    async getCharacterCount() {
      try {
        const res = await this.$axios.$post('character_count')
        this.characterCount = res.character_count
        this.characterLimit = res.character_limit
      } catch(e) {
        this.$toast.error('文字数カウントの取得に失敗しました。')
      }
    }
  }
}
</script>