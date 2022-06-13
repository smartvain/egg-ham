<script>
import { mapGetters } from 'vuex'
import { HorizontalBar } from 'vue-chartjs'

export default {
  extends: HorizontalBar,
  props: {
    searchWord: { type: String, default: '' }
  },
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
          this.$emit('update:searchWord', item[0]._model.label)
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
            footer: () => '※ グラフをクリックで字幕内検索',
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
  computed: {
    ...mapGetters(['labels', 'rates']),
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