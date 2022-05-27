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
    calcTime(seconds) {
      let hour = String(Math.floor(seconds / 3600))
      let min  = String(Math.floor(seconds % 3600 / 60))
      let rem  = String(Math.floor(seconds % 60))
      if (hour < 10) { hour = hour.padStart(2, '0') }
      if (min < 10) { min = min.padStart(2, '0') }
      if (rem < 10) { rem = rem.padStart(2, '0') }

      let time = `${hour}:${min}:${rem}`
      if (hour === '00') { time = `${min}:${rem}` }

      return time
    },
    calcSeconds(minutes) {
      const splitMinutes = String(minutes).split(':')
      const hour = Number(splitMinutes[0]) * 3600
      const min = Number(splitMinutes[1]) * 60
      const rem = Number(splitMinutes[2])

      return hour + min + rem
    }
  }
}