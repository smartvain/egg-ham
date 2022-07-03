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
        EggHamはWelio様の「TOEFL®に出る単語リスト」を参考にYouTube動画に含まれているTOEFL単語を探し出し、すぐに単語の意味を調べられるサイトです。<br>
        アカウントを作成すると、単語を保存したり編集することができます。
      </v-card-text>

      <!-- <v-card-title :class="aboutTitleClass">
        アクセス解析ツールについて
      </v-card-title>
      <v-card-text>
        当サイトでは、Googleによるアクセス解析ツール「Googleアナリティクス」を利用しています。<br>
        このGoogleアナリティクスはトラフィックデータの収集のためにクッキー（Cookie）を使用しております。<br>
        トラフィックデータは匿名で収集されており、個人を特定するものではありません。<br>
      </v-card-text> -->

      <v-card-title :class="aboutTitleClass">
        お問い合わせ
      </v-card-title>
      <v-card-text>
        こちらの<a href="https://docs.google.com/forms/d/e/1FAIpQLSfPMRHz2iztNIfoj2PtGgihaLOlD9R8Kz60ge5MkQckv4vspQ/viewform?usp=sf_link" target="subwindow">GoogleForms</a>からお問い合わせお願いします。
      </v-card-text>

      <v-card-title :class="aboutTitleClass">
        開発者について
      </v-card-title>
      <v-card-text>
        GitHub: <a href="https://github.com/smartvain" target="subwindow">https://github.com/smartvain</a>
      </v-card-text>

      <v-card-actions>
        <v-spacer />
        
        <v-btn color="primary" text @click="$emit('update:show', false)">
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