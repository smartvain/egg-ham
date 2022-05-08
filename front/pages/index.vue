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
                    <v-img
                      :src="`https://img.youtube.com/vi/${videoInfo.id}/maxresdefault.jpg`"
                      :aspect-ratio="16/9"
                    />
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
                color="primary"
              >
                DeepLで翻訳する
              </v-btn>
            </v-col>
          </v-row>

          <v-row justify="center" class="mt-1">
            <v-col cols="11">
              <v-card
                class="overflow-y-auto overflow-x-hidden"
                height="300"
                outlined
              >
              </v-card>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </v-container>
  </ValidationObserver>
</template>

<script>
export default {
  name: 'IndexPage',
  data: () => ({
    loading: {
      getLangList: false,
      getCaption: false,
    },
    videoInfo: {
      url: '',
      id: ''
    },
    inputUrlAria: {
      bgColor: '#EEEEEE',// 薄いグレー
      isFocus: false
    },
    translateLang: [
      { text: '日本語', value: 'JA' }
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
    searchCaption: null
  }),
  created() {
    this.videoInfo.url = 'https://www.youtube.com/watch?v=NoJXn-Fh6CU&t=19s'
    // this.videoInfo.id = 'NoJXn-Fh6CU'
    // this.selectLang.caption = 'en-US'

    // this.getCaption()
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
    }
  }
}
</script>
