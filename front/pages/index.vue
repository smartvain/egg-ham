<template>
  <ValidationObserver v-slot="{ passes, validate }">
    <v-row justify="center">
      <v-col cols="12" sm="8" md="6">
        <ValidationProvider v-slot="{ errors }" rules="required" name="URL">
          <v-text-field
            v-model="videoUrl"
            :error-messages="errors"
            label="URLを入力"
            dense solo
            @blur="langList = []; getLangList()"
          />
        </ValidationProvider>
      </v-col>
    </v-row>

    <v-row justify="center" class="mt-0">
      <v-col cols="12" sm="8" md="6">
        <v-select
          v-model="selectLang"
          :loading="loading.getLangList"
          :items="langList"
          item-text="text"
          item-value="value"
          label="言語を選択"
          no-data-text="URLが入力されていません"
          dense solo
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

        <v-card
          :loading="loading.getTranscript"
          class="overflow-y-auto overflow-x-hidden"
          height="500"
        >
          <v-card-text>{{ transcript }}</v-card-text>
        </v-card>
      </v-col>

    <v-col cols="12" sm="8" md="6">
      <v-select
        v-model="transLang"
        :items="transLangList"
        item-text="text"
        item-value="value"
        label="翻訳先を選択"
        dense solo
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
      
      <v-card
        height="500"
        class="overflow-y-auto overflow-x-hidden"
      >
        <v-row class="ml-0">
          <v-col cols="3">
            <v-btn
              :loading="loading.translate"
              :disabled="isTranscript"
              class="mt-3"
              color="primary"
              block
              @click="translate"
            >
              translate
            </v-btn>
          </v-col>
        </v-row>

        <v-card-text>{{ translatedScript }}</v-card-text>
      </v-card>
    </v-col>
  </v-row>
  </ValidationObserver>
</template>

<script>
export default {
  name: 'IndexPage',
  data: () => ({
    headers: [
      {text: 'id', value: 'id'},
      {text: 'name', value: 'name'}
    ],
    langList: [],
    transLangList: [
      {text: '日本語', value: 'JA'},
      {text: '英語', value: 'EN'}
    ],
    loading: {
      getLangList: false,
      getTranscript: false,
      translate: false
    },
    selectLang: null,
    transLang: null,
    transcript: null,
    translatedScript: '',
    videoUrl: ''
  }),
  computed: {
    isSelectLang() {
      return !this.selectLang
    },
    isTranscript() {
      return !this.transcript
    }
  },
  watch: {
    selectLang(newVal) {
      this.transLang = newVal === 'ja' ? 'EN' : 'JA'
    }
  },
  methods: {
    async getLangList() {
      this.loading.getLangList = true

      try {
        const url = new URL(this.videoUrl)
        this.langList = await this.$axios.$get('langList', {params: {
          videoId: url.searchParams.get('v'),
        }})
      } catch(e) {
        console.log('動画が存在しません。')
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
        
        this.transcript = res.join('').replace(/\r?\n/g, '').replace(/\s+/g, ' ')
      } catch(e) {
        console.log('字幕が存在しません。')
      }

      this.loading.getTranscript = false
    },
    async translate() {
      this.loading.translate = true

      try {
        const res = await this.$axios.$post('translate', {
          transcript: this.transcript,
          lang: this.transLang
        })
        console.log(res.translations[0].text)
        this.translatedScript = res.translations[0].text
      } catch(e) {
        console.log('翻訳に失敗しました。')
      }
      
      this.loading.translate = false
    }
  }
}
</script>
