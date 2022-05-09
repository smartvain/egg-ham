<script>
import { Doughnut } from 'vue-chartjs'

export default {
  extends: Doughnut,
  props: {
    characterCount: { type: Number, default: 0 },
    characterLimit: { type: Number, default: 0 }
  },
  data: () => ({
    chartdata: {
      labels: ['使用文字数', '残り文字数'],
      datasets: [
        {
          data: [0, 0],
          borderColor: 'transparent',
          backgroundColor: ['#FF6384','#36A2EB'],
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