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
import TypingScreen from '~/components/TypingScreen.vue'

export default {
  name: 'IndexPage',
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
      inputAria: false
    },
    videoUrl: '',
    videoId: '',
    langList: [],
    selectLang: null,
    transcript: [],
    countDown: 3
  }),
  components: {
    TypingScreen
  },
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
    displayStep4() {
      const countDown = setInterval(() => {
        this.countDown = this.countDown - 1

        if (this.countDown === 0 ) {
          this.typingScreen.step3 = false
          this.typingScreen.step4 = true
          clearInterval(countDown);
        }
      }, 1000)
    }
  }
}
</script>
