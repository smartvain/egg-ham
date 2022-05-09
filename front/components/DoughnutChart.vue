<script>
import { Doughnut } from 'vue-chartjs'

export default {
  extends: Doughnut,
  props: {
    characterCount: { type: Number },
    characterLimit: { type: Number }
  },
  data: () => ({
    chartdata: {
      labels: ['使用量', '残量'],
      datasets: [
        {
          backgroundColor: ['#FF6384','#36A2EB'],
          data: [0, 0],
          borderColor: 'transparent'
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false
    }
  }),
  watch: {
    characterCount(value) {
      this.chartdata.datasets[0].data.splice(0, 1, value)
      this.render()
    },
    characterLimit(value) {
      this.chartdata.datasets[0].data.splice(1, 1, value - this.characterCount)
      this.render()
    }
  },
  mounted() {
    this.render()
  },
  methods: {
    render() {
      this.renderChart(this.chartdata, this.options)
    }
  }
}
</script>