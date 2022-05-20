<template>
  <div />
</template>

<script>
export default {
  created () {
    this.$auth.logout()
    this.$toast.show('ログアウトしました')
  }
}
</script>
