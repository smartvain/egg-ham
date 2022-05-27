<script>
import { mapGetters } from 'vuex'
import { Doughnut } from 'vue-chartjs'

export default {
  extends: Doughnut,
  data() {
    return {
      chartData: {
        labels: ['今月の使用文字数', '今月の残り文字数'],
        datasets: [
          {
            data: [this.characterCount, this.characterRemain],
            borderColor: 'transparent',
            backgroundColor: ['#1095FE','#EBF6FF'],
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutoutPercentage: 70,
      }
    }
  },
  watch: {
    characterCount(value) {
      this.$set(this.chartData.datasets[0].data, [0], value)
    },
    characterRemain(value) {
      this.$set(this.chartData.datasets[0].data, [1], value)

      this.insertCenterText(value)
      this.render()
    }
  },
  computed: {
    ...mapGetters([ 'characterCount', 'characterLimit', 'characterRemain' ]),
  },
  created() {
    const chartData = this.chartData.datasets[0].data
    this.$set(chartData, [0], this.characterCount)
    this.$set(chartData, [1], this.characterRemain)
    this.insertCenterText(this.characterRemain)
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