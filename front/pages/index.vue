<template>
  <v-container fluid>
    <v-row>
      <v-col cols="8">
        <v-card
          class="overflow-y-auto overflow-x-hidden"
          :height="height"
          outlined
        >
          <HorizontalBar :search-word.sync="localSearchWord" :styles="horizontalBarStyle"/>
          <v-card-text v-if="!labels.length > 0" class="grey--text lighten-4 text-center text-subtitle-1 mt-16">
            検索したYouTube動画に含まれているTOEFL単語が表示されます。
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="4">
        <v-card
          class="overflow-y-auto overflow-x-hidden"
          :height="height"
          outlined
        >
          <v-data-table
            :headers="headers"
            :items="captions"
            :search="localSearchWord"
            :hide-default-header="!isCaptions"
            :items-per-page="-1"
            hide-default-footer fixed-header
          >
            <template #top>
              <v-row justify="center" class="mt-1">
                <v-col cols="11">
                  <div v-show="isCaptions">
                    <v-text-field
                      v-model="localSearchWord"
                      placeholder="字幕を検索"
                      dense clearable
                      @input="$store.commit('setSearchWord', localSearchWord)"
                    />
                  </div>
                </v-col>
              </v-row>
              <v-row justify="center" class="my-0">
                <v-col cols="4" align="right" class="py-0">
                  <v-btn
                    v-if="captions.length > 0"
                    :disabled="!localSearchWord"
                    :loading="loading.storeWord"
                    color="primary"
                    @click="storeWord"
                  >
                    単語帳に保存
                  </v-btn>
                </v-col>
                <v-col cols="4" align="right" class="py-0">
                  <v-btn
                    v-if="captions.length > 0"
                    :disabled="!localSearchWord"
                    color="primary"
                    @click="moveWeblio"
                  >
                    Weblio検索
                  </v-btn>
                </v-col>
              </v-row>
            </template>

            <template #[`item.calcTime`]="{ item }">
              <a
                :href="`${url}&t=${item.startSecond}s`"
                target="subwindow"
              >
                {{ item.calcTime }}
              </a>
            </template>

            <template #no-data>
              <v-card-text class="grey--text lighten-4 text-center text-subtitle-1 mt-12">
                検索したYouTube動画の字幕が表示されます。
              </v-card-text>
            </template>
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { mapGetters } from 'vuex'
import HorizontalBar from '~/components/HorizontalBar.vue'

export default {
  name: 'IndexPage',
  components: { HorizontalBar },
  beforeRouteEnter(to, from, next) {
    next( vm => {
      const labels = vm.$store.getters.labels
      const rates = vm.$store.getters.rates
      const searchWord = vm.$store.getters.searchWord
      if (labels.length > 0) {
        vm.$store.commit('setRates', [])
        vm.$store.commit('setRates', rates)
        vm.$store.commit('setLabels', [])
        vm.$store.commit('setLabels', labels)
      }
      if (searchWord) {vm.localSearchWord = searchWord}
    })
  },
  data: () => ({
    headers: [
      { text: '時間', value: 'calcTime', width: 75 },
      { text: '字幕', value: 'caption' },
    ],
    loading: {
      storeWord: false
    },
    localSearchWord: null,
    height: 680,
  }),
  computed: {
    ...mapGetters(['url', 'captions', 'labels', 'searchWord', 'videoTitle']),
    isCaptions() {
      return this.captions.length > 0
    },
    horizontalBarStyle() {
      return { height: `${this.labels.length * 30}px`, position: 'relative' }
    }
  },
  watch: {
    localSearchWord(value) {
      this.$store.commit('setSearchWord', value)
    }
  },
  methods: {
    moveWeblio() {
      window.open(`https://ejje.weblio.jp/content/${this.localSearchWord}`, '_blank')
    },
    async storeWord() {
      this.loading.storeWord = true

      try {
        await this.$axios.$post('word', {
          text        : this.searchWord,
          video_title : this.videoTitle,
          url         : this.url,
        })
        this.$toast.show('単語を保存しました。')
      } catch(e) {
        this.$toast.error('単語の保存に失敗しました。')
      }

      this.loading.storeWord = false
    }
  }
}
</script>