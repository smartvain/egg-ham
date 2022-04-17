<template>
  <v-row justify="center" align="center">
    <v-col cols="12" sm="8" md="6">
      <!-- test -->
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
        >
          <template v-slot:no-data>
            <div v-if="loading.getLangList" class="text-center">
              <v-progress-circular indeterminate color="primary" />
            </div>
            <div v-else class="text-center">
              <span class="grey--text">URLが入力されていません</span>
            </div>
          </template>
        </v-select>
    </v-col>
  </v-row>
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
    loading: {
      getLangList: false,
    },
    selectLang: null,
    videoUrl: ''
  }),
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
    }
  }
}
</script>
