<template>
  <div>
    <v-card-title class="mb-3">アカウント情報</v-card-title>

    <template>
      <v-card-text v-if="userInfo.name !== null">
        名前：
        <span v-if="editMode">
          <v-text-field
            dense outlined
            class="d-inline-block"
            v-model="userName"
            @input="isEdited = true"
          />
        </span>
        
        <span v-else>
          {{ userName }}
        </span>

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
    </template>
    
    <v-card-text>
      メールアドレス：{{ userInfo.email }}
    </v-card-text>

    <template>
      <v-card-text v-if="userInfo.provider_name !== null">
        連携SNS：{{ userInfo.provider_name }}
      </v-card-text>
      <v-card-text v-else>
        連携SNS：<span class="grey--text lighten-4">なし</span>
      </v-card-text>
    </template>

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
          :loading="loading"
          :disabled="!isEdited"
          @click="$emit('change-user-info', { name: userName }); isEdited = false"
        >
          変更内容を保存
        </v-btn>
      </v-col>
    </v-row>
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