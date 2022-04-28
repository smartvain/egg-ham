<template>
  <v-row justify="center">
    <v-col cols="8">
      <v-card
        class="overflow-y-auto overflow-x-hidden"
        height="400"
        outlined
      >
        <slot />
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
export default {

}
</script>

<style>

</style>