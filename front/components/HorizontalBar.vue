<script>
import { mapGetters } from 'vuex'
import { HorizontalBar } from 'vue-chartjs'

export default {
  extends: HorizontalBar,
  data() {
    return {
      chartData: {
        labels: [],
        datasets: [
          {
            label: '単語出現回数',
            data: [],
            borderColor: 'transparent',
            backgroundColor: '#1095FE',
          }
        ]
      },
      options: {
        scales: {
          xAxes: [{
            ticks: {min: 0, stepSize: 1},
            position: 'top'
          }],
          yAxes: [{
            ticks: {fontSize: 16},
          }]
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
      this.renderChart(this.chartData, this.options)
    }
  }
}
</script>