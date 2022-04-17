<template>
  <v-row justify="center" align="center">
    <v-col cols="12" sm="8" md="6">
      <!-- test -->
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
    users: []
  }),
  methods: {
    async getUsers() {
      this.users = await this.$axios.$get('getUsers')
    }
  }
}
</script>
