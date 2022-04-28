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
          height="200"
        >
          <v-card-text v-for="(line, index) in transcript" :key="index">
            {{ line }}
          </v-card-text>
        </v-card>

        <v-btn
          :disabled="isSelectLang"
          color="primary"
          class="my-4"
          @click="
            activeTypingMode = !activeTypingMode
            currentLine = transcript[0]"
        >
          start
        </v-btn>

        <v-text-field
          v-model="currentLine"
          :disabled="true"
        />

        <!-- tmp -->
        <span class="ml-2">
          タイピングモード: {{ !!activeTypingMode ? 'ON' : 'OFF' }}
        </span>
        <span class="ml-2">
          判定回数: {{ judgeCount }}回
        </span>
        <br>
        <span>{{ inputText }}</span>
        <!-- tmp -->

        <v-text-field
          v-model="inputText"
          :disabled="!activeTypingMode"
          dense solo
          @change="judge"
        ></v-text-field>
      </v-col>
    </v-row>
  </ValidationObserver>
</template>

<script>
export default {
  name: 'IndexPage',
  data: () => ({
    loading: {
      getLangList: false,
      getTranscript: false,
    },
    langList: [],
    videoUrl: '',
    currentLine: '',
    judgeCount: 0,
    videoId: '',
    selectLang: null,
    transcript: null,
    inputText: null,
    activeTypingMode: false
  }),
  computed: {
    isSelectLang() {
      return !this.selectLang
    }
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
        this.transcript = await this.$axios.$get('transcript', {params: {
          videoId: url.searchParams.get('v'),
          lang: this.selectLang
        }})
      } catch(e) {
        this.$toast.error('字幕が存在しません。')
      }

      this.loading.getTranscript = false
    },
    judge() {
      if (this.transcript[this.judgeCount] === this.inputText) {
        this.judgeCount++
        this.currentLine = this.transcript[this.judgeCount]
        this.inputText = null
      }
    }
  }
}
</script>
