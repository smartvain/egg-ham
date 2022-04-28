<template>
  <v-row justify="center">
    <v-col cols="8">
      <v-card height="400" outlined>
        <v-row align-content="center" style="height: 100%">
          <v-col>
            <slot />
          </v-col>
        </v-row>
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