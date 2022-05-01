<template>
  <ValidationObserver v-slot="{ passes, validate }">
    <v-container fluid>
      <v-row>
        <v-col cols="9">
          <v-row justify="center" class="my-4">
            <v-col cols="8">
              <ValidationProvider
                v-slot="{ errors }"
                rules="required"
                name="URL"
              >
                <v-text-field
                  v-model="videoInfo.url"
                  :error-messages="errors"
                  :background-color="inputUrlAria.bgColor"
                  :flat="!inputUrlAria.isFocus"
                  placeholder="URLを入力"
                  dense solo
                  @focus="
                    inputUrlAria.isFocus = true
                    inputUrlAria.bgColor = 'white'"
                  @blur="
                    inputUrlAria.isFocus = false
                    inputUrlAria.bgColor = '#EEEEEE'
                    if (videoInfo.url) { langList = []; getLangList() }"
                />
              </ValidationProvider>
            </v-col>

            <v-col cols="3">
              <v-select
                v-model="selectLang"
                :loading="loading.getLangList"
                :items="langList"
                item-text="text"
                item-value="value"
                placeholder="言語を選択"
                no-data-text="URLが入力されていません"
                dense
                @change="validate().then(passes(getTranscript))"
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

          <div v-if="typingScreen.step1">
            <v-row justify="center">
              <v-img
                max-width="800"
                lazy-src="'https://cdn.vuetifyjs.com/images/parallax/material.jpg'"
                :src="`https://img.youtube.com/vi/${videoInfo.id}/maxresdefault.jpg`"
                :aspect-ratio="16/9"
              />
            </v-row>

            <v-row justify="center">
              <v-btn
                :disabled="!selectLang"
                color="primary"
                class="my-4"
                @click="
                  typingScreen.step1 = false
                  typingScreen.step2 = true
                  typingScreen.inputAria = true"
              >
                start
              </v-btn>
            </v-row>
          </div>

          <div v-if="typingScreen.step2">
            <TypingScreen>
              <v-row
                align-content="center"
                style="height: 100%"
              >
                <v-col>
                  <v-card-text class="text-center">日本語入力モードをオフにしてください</v-card-text>
                  <v-card-text class="text-center orange--text text-h5">スペースキーで開始</v-card-text>
                  <v-card-text class="text-center">(終了はescキーです)</v-card-text>
                </v-col>
              </v-row>
            </TypingScreen>
          </div>
            
          <div v-if="typingScreen.step3">
            <TypingScreen>
              <v-row
                align-content="center"
                class="mt-0"
                style="height: 100%"
              >
                <v-col>
                  <v-card-text class="text-center orange--text text-h3">{{ countDownTime }}</v-card-text>
                </v-col>
              </v-row>
            </TypingScreen>
          </div>

          <div v-if="typingScreen.step4">
            <TypingScreen>
              <v-row
                class="mt-0"
                style="height: 100%"
              >
                <v-col>
                  <v-card-text class="text-center">
                    <span class="orange--text">{{ transcript.typed }}</span><span>{{ transcript.untyped }}</span>
                  </v-card-text>
                </v-col>
              </v-row>
            </TypingScreen>
          </div>

          <div v-if="typingScreen.step5">
            <TypingScreen>
              <v-row
                align-content="center"
                class="mt-0"
                style="height: 100%"
              >
                <v-col>
                  <v-card-text class="text-center">
                    Game Finish!
                  </v-card-text>
                </v-col>
              </v-row>
            </TypingScreen>
          </div>

          <div v-if="typingScreen.inputAria">
            <v-row justify="center">
              <v-col cols="8">
                <v-card height="100" outlined>
                  <v-row align-content="center" class="mt-0" style="height: 100%">
                    <v-col>
                      <div v-if="typingScreen.step4">
                        <v-card-text
                          class="mx-auto px-0 py-10 text-no-wrap overflow-x-hidden"
                          style="width: 95%; position: relative;"
                          ref="textBox"
                        >
                          <div :style="`left: ${subtractWidth}px; position: absolute` ">
                            <span
                              class="orange--text"
                            >
                              {{ hiraText.typed }}</span><span>{{ hiraText.untyped }}
                            </span>
                          </div>

                          <br>

                          <div :style="`left: ${subtractWidth}px; position: absolute`" class="text-h5">
                            <span
                              class="orange--text"
                              ref="typedRoman"
                            >
                              {{ roman.typed }}
                            </span>
                            <span ref="next">{{ roman.next }}</span>
                            <span>{{ roman.untyped }}</span>
                          </div>
                        </v-card-text>
                      </div>
                    </v-col>
                  </v-row>
                </v-card>
              </v-col>
            </v-row>
          </div>
        </v-col>

        <v-divider vertical />

        <v-col cols="3">
          <v-row justify="center">
            <img
              style="width: 90%"
              src="~/assets/img/example.png"
              class="my-4"
            >
          </v-row>

          <v-row justify="center">
            <v-card color="#F6F9F9" width="90%" outlined>
              <v-list color="#F6F9F9">
                <v-subheader class="font-weight-bold">今週のプレイ動画ランキング！</v-subheader>

                <v-list-item-group>
                  <v-list-item v-for="n of 3" :key="n">
                    <v-row class="my-4">
                      <v-col cols="4">
                        <v-img
                          lazy-src="'https://cdn.vuetifyjs.com/images/parallax/material.jpg'"
                          :src="'https://img.youtube.com/vi/ouf7rXDlkDk/maxresdefault.jpg'"
                          :aspect-ratio="16/9"
                        />
                      </v-col>

                      <v-col cols="8" class="text-truncate">
                        <span class="text-caption text-no-wrap">Nissy(⻄島隆弘) / 「君に触れた時から」Music Video</span><br>
                        <span class="text-caption">プレイ回数: 2000回</span><br>
                      </v-col>
                    </v-row>
                  </v-list-item>
                </v-list-item-group>
              </v-list>
            </v-card>
          </v-row>
        </v-col>
      </v-row>
    </v-container>
  </ValidationObserver>
</template>

<script>
import TypingScreen from '~/components/TypingScreen.vue'

export default {
  name: 'IndexPage',
  components: {
    TypingScreen
  },
  data: () => ({
    loading: {
      getLangList: false,
      getTranscript: false,
    },
    typingScreen: {
      step1: true,
      step2: false,
      step3: false,
      step4: false,
      step5: false,
      inputAria: false
    },
    transcript: {
      typed: '',
      untyped: ''
    },
    hiraText: {
      typed: '',
      untyped: ''
    },
    roman: {
      typed: '',
      untyped: '',
      next: ''
    },
    elWidth: {
      typedRoman: 0,
      next: 0,
      halfDisplayBox: 0
    },
    inputUrlAria: {
      bgColor: '#EEEEEE',
      isFocus: false
    },
    videoInfo: {
      url: '',
      id: ''
    },
    subtractWidth: 0,
    langList: [],
    selectLang: null,
    countDownTime: 3,
    typed: '',
    untyped: '',
  }),
  created() {
    this.videoUrl = 'https://www.youtube.com/watch?v=ouf7rXDlkDk'
    this.videoId = 'ouf7rXDlkDk'
    this.selectLang = 'ja'

    this.getTranscript()
  },
  mounted() {
    document.addEventListener('keydown', e => {
      if (this.typingScreen.step2 && e.code === 'Space') {
        this.typingScreen.step2 = false
        this.typingScreen.step3 = true
        this.countDown()
      }

      if (this.typingScreen.step4) {
        this.typingLogic(e.key)
      }
    })
  },
  watch: {
    'elWidth.typedRoman'() {
      if (this.elWidth.typedRoman > this.elWidth.halfDisplayBox) {
        this.subtractWidth -= this.elWidth.next
      }
    }
  },
  methods: {
    async getLangList() {
      this.loading.getLangList = true

      try {
        const url = new URL(this.videoInfo.url)
        this.videoInfo.id = url.searchParams.get('v')
        this.langList = await this.$axios.$get('langList', {params: {
          videoId: this.videoInfo.id
        }})
      } catch(e) {
        this.$toast.error('動画が存在しません。')
      }

      this.loading.getLangList = false
    },
    async getTranscript() {
      this.loading.getTranscript = true

      try {
        const res = await this.$axios.$get('transcript', {params: {
          videoId: this.videoInfo.id,
          lang: this.selectLang
        }})
        const roman = this.reviseText(res.hiraText)
        
        this.transcript.untyped = res.transcript.join('').replace(/\r?\n/g, '').replace(/\s+/g, ' ')// 改行と空行削除
        this.hiraText.untyped = res.hiraText
        this.roman.untyped = roman.substring(1)
        this.roman.next = roman.substring(0, 1)
      } catch(e) {
        this.$toast.error('字幕が存在しません。')
      }

      this.loading.getTranscript = false
    },
    countDown() {
      const countDown = setInterval(() => {
        this.countDownTime = this.countDownTime - 1

        if (this.countDownTime === 0 ) {
          this.typingScreen.step3 = false
          this.typingScreen.step4 = true
          clearInterval(countDown);
        }
      }, 1000)
    },
    getWidth(el) {
      const dom = el
      const rect = dom.getBoundingClientRect();
      return rect.width
    },
    reviseText (text) {
      return this.$kanaToRoman(text, 'hepburn', {bmp: true}).toUpperCase()
                  .replace(/\s+/g, "")
                  .replaceAll('（', "")
                  .replaceAll('(', "")
                  .replaceAll('）', "")
                  .replaceAll(')', "")
                  .replaceAll('「', "")
                  .replaceAll('」', "")
                  .replaceAll('’', "")
    },
    typingLogic(key) {
      if (key.toUpperCase() !== this.roman.next) { return }
        
      this.roman.typed   += this.roman.next
      this.roman.next    = this.roman.untyped.substring(0, 1)
      this.roman.untyped = this.roman.untyped.substring(1)

      this.elWidth.halfDisplayBox = this.getWidth(this.$refs.textBox) / 2
      this.elWidth.typedRoman     = this.getWidth(this.$refs.typedRoman)
      this.elWidth.next           = this.getWidth(this.$refs.next)

      if (this.roman.untyped === '') {
        this.typingScreen.step4 = false
        this.typingScreen.step5 = true
      }
    }
  }
}
</script>
