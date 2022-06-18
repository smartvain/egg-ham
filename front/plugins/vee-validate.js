import { extend, ValidationObserver, ValidationProvider } from 'vee-validate'
import * as rules from 'vee-validate/dist/rules'
import { messages } from 'vee-validate/dist/locale/ja.json'
import Vue from 'vue'

Vue.component('ValidationProvider', ValidationProvider)
Vue.component('ValidationObserver', ValidationObserver)

Object.keys(rules).forEach((rule) => {
  extend(rule, {
    ...rules[rule], // eslint-disable-line
    message: messages[rule]
  })
})

extend('is_youtube_url', {
  message: 'YouTubeの動画URLを入力してください。',
  validate(value) {
    return value.match(/(www.youtube.com)/g)
  }
})