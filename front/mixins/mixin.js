export default {
  data: () => ({
    wordTypes: {
      word: { text: '単語', value: 1 },
      idiom: { text: '慣用句', value: 2 }
    },
    wordTypesArray: [
      { text: '単語', value: 1 },
      { text: '慣用句', value: 2 }
    ]
  }),
  methods: {
    calcTime(time) {
      const min = Math.floor(time % 3600 / 60);
      let rem = String(Math.floor(time % 60));
      if (rem < 10) { rem = rem.padStart(2, '0') }

      return `${min}:${rem}`
    },
  }
}