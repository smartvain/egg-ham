<template>
  <v-container fluid>
    <v-row>
      <v-col cols="8">
        <v-card
          class="overflow-y-auto overflow-x-hidden"
          :height="height"
          outlined
        >
          <HorizontalBar :styles="horizontalBarStyle"/>
        </v-card>
      </v-col>

      <v-col cols="4">
        <v-card
          class="overflow-y-auto overflow-x-hidden"
          :height="height"
          outlined
        >
          <v-data-table
            :headers="headers"
            :items="captions"
            :search="searchCaption"
            :hide-default-header="!isCaptions"
            :items-per-page="-1"
            hide-default-footer fixed-header
          >
            <template #top>
              <v-row justify="center" class="mt-1">
                <v-col cols="11">
                  <div v-show="isCaptions">
                    <v-text-field
                      v-model="searchCaption"
                      placeholder="字幕を検索"
                      dense clearable
                    />
                  </div>
                </v-col>
              </v-row>
            </template>

            <template #[`item.calcTime`]="{ item }">
              <a
                :href="`${url}&t=${item.startSecond}s`"
                target="subwindow"
              >
                {{ item.calcTime }}
              </a>
            </template>

            <template #no-data>
              <v-card-text class="grey--text lighten-4 text-center text-subtitle-1 mt-16">
                URLを入力後、字幕言語を選択すると<br>「字幕を取得する」ボタンをクリックできるようになります。
              </v-card-text>
            </template>
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { mapGetters } from 'vuex'
import HorizontalBar from '~/components/HorizontalBar.vue'

export default {
  name: 'IndexPage',
  components: { HorizontalBar },
  data: () => ({
    headers: [
      { text: '時間', value: 'calcTime', width: 75 },
      { text: '字幕', value: 'caption' },
    ],
    searchCaption: null,
    height: 680,
  }),
  computed: {
    ...mapGetters(['url', 'captions', 'labels']),
    isCaptions() {
      return this.captions.length > 0
    },
    horizontalBarStyle() {
      return {
        height: `${this.labels.length * 30}px`,
        position: 'relative'
      }
    }
  }
}
</script>