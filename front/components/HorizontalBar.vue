<script>
import { mapGetters } from 'vuex'
import { HorizontalBar } from 'vue-chartjs'

export default {
  extends: HorizontalBar,
  data() {
    return {
      chartData: {
        labels: [],
        datasets: [{
            label: '単語出現回数',
            data: [],
            borderColor: 'transparent',
            backgroundColor: '',
        }]
      },
      options: {
        onClick: (evt, item) => {
          if (!item[0]) {return}
          window.open(`https://ejje.weblio.jp/content/${item[0]._model.label}`, '_blank')
        },
        scales: {
          xAxes: [{
            ticks: {beginAtZero: true, stepSize: 1},
            position: 'top'
          }],
          yAxes: [{
            ticks: {fontSize: 16, fontStyle: 500},
          }]
        },
        tooltips: {
          callbacks: {
            footer: () => '※ グラフをクリックでWeblio英和辞典を表示',
          }
        },
        animation: {
          duration: 1500,
          easing: 'easeOutQuint'
        },
        responsive: true,
        maintainAspectRatio: false
      }
    }
  },
  watch: {
    labels(value) {
      this.chartData.labels = value
    },
    rates(value) {
      this.chartData.datasets[0].data = value
      this.render()
    }
  },
  computed: {
    ...mapGetters(['labels', 'rates']),
  },
  mounted() {
    this.render()
  },
  methods: {
    render() {
      const gradient = this.$refs.canvas.getContext('2d').createLinearGradient(500, 0, 100, 0)
      gradient.addColorStop(0, 'rgba(0, 191, 255, 0.8)')
      gradient.addColorStop(0.5, 'rgba(0, 191, 255, 0.4)');
      gradient.addColorStop(1, 'rgba(0, 191, 255, 0.2)');
      this.chartData.datasets[0].backgroundColor = gradient
      
      this.renderChart(this.chartData, this.options)
    }
  }
}
</script>