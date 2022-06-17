export default function ({ $auth, store, $vuetify }) {
  if (!$auth.user) { return }
  store.commit('setTheme', $auth.user.theme)
  $vuetify.theme.dark = $auth.user.theme
}