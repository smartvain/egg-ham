<template>
  <v-dialog v-model="_show" width="670">
    <v-card>
      <v-card-title class="text-h6 font-weight-bold">
        サイト概要
      </v-card-title>

      <v-divider />
      
      <v-card-title :class="aboutTitleClass">
        当サイトについて
      </v-card-title>
      <v-card-text>
        EggHamはWelio様の「TOEFL®に出る単語リスト」を参考にYouTube動画に含まれているTOEFL単語を探し出し、すぐに単語の意味を調べられるサイトです。
        アカウントを作成すると、単語を保存することができます。
      </v-card-text>

      <v-card-title :class="aboutTitleClass">
        アクセス解析ツールについて
      </v-card-title>
      <v-card-text>
        当サイトでは、Googleによるアクセス解析ツール「Googleアナリティクス」を利用しています。
        このGoogleアナリティクスはトラフィックデータの収集のためにクッキー（Cookie）を使用しております。
        トラフィックデータは匿名で収集されており、個人を特定するものではありません。
      </v-card-text>

      <v-card-title :class="aboutTitleClass">
        お問い合わせ
      </v-card-title>
      <v-card-text>
        こちらのGoogleFormsからお問い合わせお願いします。
      </v-card-text>

      <v-card-title :class="aboutTitleClass">
        開発者について
      </v-card-title>
      <v-card-text>
        Twitter: _________
      </v-card-text>
      <v-card-text>
        GitHub: _________
      </v-card-text>

      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn
          color="primary"
          text
          @click="$emit('update:show', false)"
        >
          閉じる
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  props: {
    show: { type: Boolean, default: false }
  },
  data: () => ({
    aboutTitleClass: 'text-subtitle-1 font-weight-bold'
  }),
  computed: {
    _show: {
      get() { return this.show },
      set() {}
    }
  }
}
</script>

<style>

</style>