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
            placeholder="URLを入力"
            dense solo clearable
            @focus="
              textField.isFocus = true
              textField.bgColor = 'white'"
            @blur="
              textField.isFocus = false
              textField.bgColor = '#EEEEEE'
              validateUrl(errors)
              if (url) { langList = []; getLangList() }"
            @click:clear="captionLang = null"
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
          color="primary"
          @click="validate().then(passes(getCaptions))"
        >
          字幕を取得する
        </v-btn>
      </v-col>
    </v-row>
  </ValidationObserver>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  data: () => ({
    textField: {
      bgColor: '#EEEEEE',
      isFocus: false
    },
    loading: {
      getLangList: false,
      getCaptions: false
    },
    url: null,
    captionLang: null,
    langList: []
  }),
  watch: {
    captionLang(value) {
      this.$store.commit('setCaptionLang', value)
    },
  },
  computed: {
    ...mapGetters(['videoId'])
  },
  created() {
    this.url = 'https://www.youtube.com/watch?v=NoJXn-Fh6CU&t=19s'
    // this.videoId = 'NoJXn-Fh6CU'
    // this.captionLang = 'en-US'
    // this.getCaptions()
    // ------------------------------------------
  },
  methods: {
    async getLangList() {
      this.loading.getLangList = true

      const url        = new URL(this.url)
      const videoId    = url.searchParams.get('v')
      const extractUrl = `${this.url.replace(/\?.*$/,"")}?v=${videoId}`

      this.$store.commit('setVideoId', videoId)
      this.$store.commit('setUrl', extractUrl)
      
      try {
        this.langList = await this.$axios.$get('langList', {params: { videoId }})
      } catch(e) {
        this.$toast.error('動画が存在しません。')
      }

      this.loading.getLangList = false
    },
    async getCaptions() {
      this.loading.getCaptions = true
      
      try {
        var captions = await this.$axios.$get('captions', {params: {
          videoId: this.videoId,
          lang: this.captionLang
        }})
      } catch(e) {
        this.$toast.error('字幕の取得に失敗しました。')
      }

      for (const caption of captions) {
        caption.calcTime = this.calcTime(caption.start)
      }
      this.$store.commit('setCaptions', captions)
      
      this.loading.getCaptions = false
    },
    calcTime(time) {
      const min = Math.floor(time % 3600 / 60);
      let rem = String(Math.floor(time % 60));
      if (rem < 10) { rem = rem.padStart(2, '0') }

      return `${min}:${rem}`
    },
    validateUrl(errorMessage) {
      if (errorMessage.length > 0) { this.$toast.error(errorMessage) }
    }
  }
}
</script>

<style>

</style>