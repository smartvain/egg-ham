<template>
  <ValidationObserver v-slot="{ passes, validate }">
    <v-container fluid>
      <v-row>
        <v-col cols="9">
          <v-row justify="center" class="my-4">
            <v-col cols="8">
              <ValidationProvider v-slot="{ errors }" rules="required" name="URL">
                <v-text-field
                  v-model="videoUrl"
                  :error-messages="errors"
                  :background-color="inputUrlAria"
                  :flat="!isFocus"
                  placeholder="URLを入力"
                  dense solo
                  @focus="
                    isFocus = true
                    inputUrlAria = 'white'"
                  @blur="
                    isFocus = false
                    inputUrlAria = '#EEEEEE'
                    if (videoUrl) { langList = []; getLangList() }"
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
                :src="`https://img.youtube.com/vi/${videoId}/maxresdefault.jpg`"
                :aspect-ratio="16/9"
              />
            </v-row>

            <v-row justify="center">
              <v-btn
                :disabled="isSelectLang"
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
              <v-row align-content="center" style="height: 100%">
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
              <v-row align-content="center" style="height: 100%">
                <v-col>
                  <v-card-text class="text-center orange--text text-h3">{{ countDownTime }}</v-card-text>
                </v-col>
              </v-row>
            </TypingScreen>
          </div>

          <div v-if="typingScreen.step4">
            <TypingScreen>
              <v-row style="height: 100%">
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
              <v-row align-content="center" style="height: 100%">
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
                  <v-card-text
                    class="mx-auto px-0 text-no-wrap overflow-x-hidden"
                    style="width: 95%"
                  >
                    <div v-if="typingScreen.step4">
                    </div>
                  </v-card-text>
                          <span class="orange--text">{{ hiraText.typed }}</span><span>{{ hiraText.untyped }}</span><br>
                          <span class="orange--text">{{ roman.typed }}</span><span>{{ roman.untyped }}</span>
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
    roman: {
      typed: '',
      untyped: ''
    },
    hiraText: {
      typed: '',
      untyped: ''
    },
    videoUrl: '',
    videoId: '',
    langList: [],
    selectLang: null,
    inputUrlAria: '#EEEEEE', // search aria
    isFocus: false, // search aria
    countDownTime: 3,
    typed: '',
    untyped: ''
  }),
  computed: {
    isSelectLang() {
      return !this.selectLang
    }
  },
  mounted() {
    document.addEventListener('keydown', (e) => {
      if (this.typingScreen.step4) {
        if (e.key !== this.roman.untyped.substring(0, 1)) { return }
        this.roman.typed += this.roman.untyped.substring(0, 1)
        this.roman.untyped = this.roman.untyped.substring(1)

        if (this.roman.untyped === '') {
          this.typingScreen.step4 = false
          this.typingScreen.step5 = true
        }
      }

      if (this.typingScreen.step2 && e.code === 'Space') {
        this.typingScreen.step2 = false
        this.typingScreen.step3 = true
        this.countDown()
      }
    })
  },
  methods: {
    async getLangList() {
      this.loading.getLangList = true

      try {
        const url = new URL(this.videoUrl)
        this.videoId = url.searchParams.get('v')
        this.langList = await this.$axios.$get('langList', {params: {
          videoId: this.videoId,
        }})
      } catch(e) {
        this.$toast.error('動画が存在しません。')
      }

      this.loading.getLangList = false
    },
    async getTranscript() {
      this.loading.getTranscript = true

      try {
        const url = new URL(this.videoUrl)
        const res = await this.$axios.$get('transcript', {params: {
          videoId: url.searchParams.get('v'),
          lang: this.selectLang
        }})
        this.transcript.untyped = res.transcript.join('').replace(/\r?\n/g, '').replace(/\s+/g, ' ')
        this.hiraText.untyped = res.hiraText
        this.roman.untyped = this.$kanaToRoman(res.hiraText, 'hepburn', {bmp: true, longSound: 'hyphen'})
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
    }
  }
}
</script>
