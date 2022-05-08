<template>
  <ValidationObserver v-slot="{ passes, validate }">
    <v-container fluid>
      <v-row class="mt-0">
        <v-col cols="8">
          <v-row justify="center">
            <!-- URL入力欄 -->
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
            <v-col cols="10">
              <v-card
                class="overflow-y-auto overflow-x-hidden"
                outlined
              >
                <v-data-table
                  :headers="headers"
                  :items="captions"
                  :items-per-page="-1"
                  :search="searchTime"
                  height="550"
                  fixed-header
                  hide-default-footer
                >
                  <template #top>
                    <v-text-field
                      v-model="searchTime"
                      placeholder="search"
                      class="mx-2 mt-4"
                      dense
                    />
                  </template>

                  <!-- <template v-slot:[`item.start`]="{ item }">
                    {{ item.start | calcTime }}
                  </template> -->

                  <template #no-data>
                    <!-- サムネイル表示エリア -->
                    <v-img
                      :src="`https://img.youtube.com/vi/${videoInfo.id}/maxresdefault.jpg`"
                      :aspect-ratio="16/9"
                    />

                    <!-- 字幕取得ボタン -->
                    <v-row justify="center" class="mt-2">
                      <v-btn
                        :disabled="!selectLang.caption"
                        color="primary"
                        class="my-4"
                        @click="validate().then(passes(getCaption))"
                      >
                        字幕を取得する
                      </v-btn>
                    </v-row>
                  </template>
                </v-data-table>
              </v-card>
            </v-col>
          </v-row>
        </v-col>

        <v-divider vertical />

        <v-col cols="4">
          <v-row justify="center">
            <v-col cols="10">
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
          </v-row>

          <v-row justify="center" class="mt-4">
            <v-col cols="10">
              <v-card
                class="overflow-y-auto overflow-x-hidden"
                height="300"
                outlined
              >
              </v-card>
            </v-col>
          </v-row>

          <!-- <v-row justify="center">
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
          </v-row> -->
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
    captions: [],
    langList: [],
    selectLang: {
      caption: null,
      translate: null
    },
    searchTime: null
  }),
  created() {
    this.videoInfo.url = 'https://www.youtube.com/watch?v=NoJXn-Fh6CU&t=19s'
    this.videoInfo.id = 'NoJXn-Fh6CU'
    this.selectLang.caption = 'en-US'

    this.getCaption()
  },
  methods: {
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
        this.$toast.error('字幕が存在しません。')
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
