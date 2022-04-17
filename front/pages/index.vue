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
