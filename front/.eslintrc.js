module.exports = {
  root: true,
  env: {
    browser: true,
    node: true,
  },
  parserOptions: {
    parser: '@babel/eslint-parser',
    requireConfigFile: false,
  },
  extends: ['@nuxtjs', 'plugin:nuxt/recommended', 'prettier'],
  plugins: [],
  // add your custom rules here
  rules: {
    'no-unmodified-loop-condition': 'off',
    'no-var': 'off',
    'no-console': 'off',
    'no-vue/multi-word-component-names': 'off',
  },
}
