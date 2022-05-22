<template>
  <v-navigation-drawer
    v-model="_drawer"
    width="450"
    right temporary fixed
  >
    <v-card height="100%" flat>
      <v-row justify="center" class="ml-0">
        <v-spacer />

        <v-col cols="6">
          <v-card-title class="justify-center">ログイン</v-card-title>
        </v-col>

        <v-col cols="3">
          <v-btn
            class="float-right mt-1 mr-1"
            icon
            @click="_drawer = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-col>
      </v-row>

      <v-card-text>
        <v-btn
          class="text-capitalize caption"
          color="#00ACEE"
          width="100%"
          height="48px"
          rounded dark depressed block
        >
          <img
            class="button-logo-img mr-4"
            src="~/assets/img/twitter_logo_white.png"
            style="height: 20px"
          />
          twitterでログイン
        </v-btn>

        <v-btn
          class="text-capitalize caption"
          style="border-color: #979797"
          width="100%"
          height="48px"
          rounded outlined block
        >
          <img
            class="button-logo-img mr-4"
            src="https://madeby.google.com/static/images/google_g_logo.svg"
            style="height: 24px"
          />
          Googleでログイン
        </v-btn>

        <p class="text-center pt-3 mt-3 text-subtitle-1 siginIn-border-top">
          メールアドレスでログイン
        </p>
        
        <v-form class="mx-9" ref="form" v-model="valid">
          <v-text-field
            placeholder="メールアドレス"
            outlined
            dense
          />

          <v-text-field
            placeholder="パスワード"
            outlined
            dense
          />

          <p class="pointer">パスワードを忘れた方</p>

          <div class="text-center">
            <v-btn class="primary" :disabled="!valid">ログイン</v-btn>
          </div>
        </v-form>
      </v-card-text>
    </v-card>
  </v-navigation-drawer>
</template>

<script>
export default {
  props: {
    drawer: { type: Boolean, default: false }
  },
  data: () => ({
    valid: false,
  }),
  computed: {
    _drawer: {
      get() {
        return this.drawer
      },
      set(value) {
        this.$emit('close', value)
      }
    }
  }
}
</script>

<style>

</style>