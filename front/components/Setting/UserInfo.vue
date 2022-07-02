<template>
  <div>
    <v-card-title class="mb-3">アカウント情報</v-card-title>

    <form>
      <div>
        <v-card-text v-if="userInfo.name !== null">
          ユーザー名：
          <span v-if="editMode">
            <ValidationProvider
              v-slot="{ errors }"
              rules="required|max:20"
              name="ユーザー名"
              mode="passive"
            >
              <v-text-field
                v-model="userName"
                class="d-inline-block"
                dense outlined
                :error-messages="errors"
                @input="isEdited = true"
              />
            </ValidationProvider>
          </span>
          
          <span v-else>{{ userName }}</span>

          <v-btn
            color="primary"
            class="ml-5"
            :outlined="editMode"
            small
            @click="editMode = !editMode"
          >
            編集する
          </v-btn>
        </v-card-text>

        <v-card-text v-else>
          名前：<span class="grey--text lighten-4">なし</span>
        </v-card-text>
      </div>
      
      <v-card-text>メールアドレス：{{ userInfo.email }}</v-card-text>

      <div>
        <v-card-text v-if="userInfo.provider_name !== null">
          連携SNS：{{ userInfo.provider_name }}
        </v-card-text>

        <v-card-text v-else>
          連携SNS：<span class="grey--text lighten-4">なし</span>
        </v-card-text>
      </div>

      <v-row>
        <v-col cols="2" class="pr-0">
          <v-card-text>アイコン：</v-card-text>
        </v-col>

        <v-col class="pl-0 mt-4">
          <v-img
            :src="$auth.user.avatar ? $auth.user.avatar : require('~/assets/img/default_user.png')"
            class="rounded-circle"
            max-width="100"
          />
        </v-col>
      </v-row>

      <v-row>
        <v-col cols="12" align="right">
          <v-btn
            color="primary"
            type="submit"
            :loading="loading"
            :disabled="!isEdited"
            @click="$emit('change-name', { name: userName }); isEdited = false"
          >
            変更内容を保存
          </v-btn>
        </v-col>
      </v-row>
    </form>
  </div>
</template>

<script>
export default {
  props: {
    loading: { type: Boolean, default: false }
  },
  data: () => ({
    userInfo: null,
    userName: null,
    editMode: false,
    isEdited: false
  }),
  created() {
    this.userInfo = this.$auth.user
    this.userName = this.$auth.user.name
  }
}
</script>

<style>

</style>