<script>
import { Doughnut } from 'vue-chartjs'

export default {
  extends: Doughnut,
  props: {
    characterCount: { type: Number, default: 0 },
    characterLimit: { type: Number, default: 0 }
  },
  data: () => ({
    characterRemain: 0,
    chartData: {
      labels: ['今月の使用文字数', '今月の残り文字数'],
      datasets: [
        {
          data: [0, 0],
          borderColor: 'transparent',
          backgroundColor: ['#1095FE','#EBF6FF'],
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutoutPercentage: 70,
    },
  }),
  watch: {
    characterCount(value) {
      this.chartData.datasets[0].data.splice(0, 1, value)
      this.characterRemain = this.characterLimit - value
    },
    characterRemain(value) {
      this.chartData.datasets[0].data.splice(1, 1, value)
      this.insertCenterText(value)
      this.render()
    }
  },
  mounted() {
    this.render()
  },
  methods: {
    render() {
      this.renderChart(this.chartData, this.options)
    },
    insertCenterText(remain) {
      this.addPlugin({
        beforeDraw(chart) {
          const ctx = chart.ctx
          const centerWidth = chart.width / 2
          const centerHeight = chart.height / 2
          const fontSize = 18;
          const fontStyle = 'normal';
          const fontFamily = "Helvetica Neue";
          ctx.clearRect(0, 0, chart.width, chart.height)
          ctx.textAlign = 'center';
          ctx.textBaseline = 'middle';

          ctx.font = Chart.helpers.fontString(fontSize, fontStyle, fontFamily);// eslint-disable-line
          ctx.fillText('残り文字数', centerWidth, centerHeight);

          ctx.font = Chart.helpers.fontString(fontSize * 2, fontStyle, fontFamily);// eslint-disable-line
          ctx.fillText(remain.toLocaleString(), centerWidth, centerHeight + 30);
        }
      })
    }
  }
}
</script>