<template>
  <ValidationObserver v-slot="{ passes, validate }">
    <v-row class="mt-3" style="width: 1000px">
      <v-col cols="7">
        <ValidationProvider
          v-slot="{ errors }"
          rules="is_youtube_url"
          name="URL"
        >
          <v-text-field
            v-model="url"
            :background-color="textField.bgColor"
            :flat="!textField.isFocus"
            placeholder="YouTubeのURLを入力して下さい"
            dense solo clearable
            @focus="
              textField.isFocus = true
              textField.bgColor = $vuetify.theme.dark ? 'light-blue darken-2' : 'light-blue lighten-5'"
            @blur="
              textField.isFocus = false
              textField.bgColor = null
              validateUrl(errors)
              if (url) { getLangList() }"
          />
        </ValidationProvider>
      </v-col>

      <v-col cols="3" class="mt-1">
        <v-select
          v-model="captionLang"
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

      <v-col cols="2">
        <v-btn
          :disabled="!captionLang"
          :loading="loading.getCaptions"
          class="rounded-lg"
          color="primary"
          @click="validate().then(passes(getCaptions))"
        >
          字幕を表示
        </v-btn>
      </v-col>
    </v-row>
  </ValidationObserver>
</template>

<script>
import { mapGetters } from 'vuex'
import Mixin from '~/mixins/mixin.js'

export default {
  mixins: [ Mixin ],
  data: () => ({
    textField: {
      bgColor: null,
      isFocus: false
    },
    loading: {
      getLangList: false,
      getCaptions: false
    },
    url: null,
    captionLang: null,
  }),
  computed: {
    ...mapGetters(['videoId', 'langList'])
  },
  watch: {
    url() {
      this.$store.commit('setLangList', [])
      this.captionLang = null
    },
    videoId(value) {
      if (!value) { return }
      this.getVideoInfo(value)
    }
  },
  created() {
    this.url = 'https://www.youtube.com/watch?v=NoJXn-Fh6CU&t=19s'
    // this.videoId = 'NoJXn-Fh6CU'
    // this.captionLang = 'en-US'
    // this.getCaptions()
    // ------------------------------------------
  },
  methods: {
    async getVideoInfo(videoId) {
      const res = await this.$axios.$get('videoInfo', { params: { videoId }})
      this.$store.commit('setVideoTitle', res.title)
    },
    async getLangList() {
      this.loading.getLangList = true

      const url        = new URL(this.url)
      const videoId    = url.searchParams.get('v')
      const extractUrl = `${this.url.replace(/\?.*$/,"")}?v=${videoId}`

      this.$store.commit('setVideoId', videoId)
      this.$store.commit('setUrl', extractUrl)
      
      try {
        const langList = await this.$axios.$get('langList', {params: { videoId }})
        this.$store.commit('setLangList', langList)
      } catch(e) {
        this.$toast.error('字幕データがありません。')
      }

      this.loading.getLangList = false
    },
    async getCaptions() {
      this.loading.getCaptions = true
      
      try {
        var res = await this.$axios.$get('captions', {params: {
          videoId: this.videoId,
          lang: this.captionLang
        }})
      } catch(e) {
        this.$toast.error('字幕の取得に失敗しました。')
      }
      this.addCalcTime(res.captions)
      this.addChartData(res.words)
      
      this.loading.getCaptions = false
    },
    addCalcTime(captions) {
      captions.forEach(caption => caption.calcTime = this.calcTime(caption.startSecond))
      this.$store.commit('setCaptions', captions)
    },
    addChartData(words) {
      this.$store.commit('setLabels', Object.keys(words).map(key => key))
      this.$store.commit('setRates', Object.keys(words).map(key => words[key]))
    },
    validateUrl(errorMessage) {
      if (errorMessage.length > 0) { this.$toast.error(errorMessage) }
    }
  }
}
</script>

<style>

</style>