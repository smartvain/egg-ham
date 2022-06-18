export default ({ $axios, $toast}) => {
  $axios.onError(error => {
    const code = error.response.status
    switch (code) {
      case 500:
        $toast.error(`${code}: サーバーエラーが発生しました。時間をおいてもう一度お試しください。`)
        break
      default:
        $toast.error('予期しないエラーが発生しました。時間をおいてもう一度お試しください。')
        break
    }
  })
}