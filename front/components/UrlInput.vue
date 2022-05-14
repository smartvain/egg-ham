<template>
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
</template>

<script>
export default {

}
</script>

<style>

</style>