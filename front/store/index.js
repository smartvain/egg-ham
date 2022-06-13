export const state = () => ({
  captions  : [],
  langList  : [],
  videoId   : null,
  url       : null,
  labels    : [],
  rates     : [],
  searchWord: null
});

export const getters = {
  captions  : state => state.captions,
  langList  : state => state.langList,
  videoId   : state => state.videoId,
  url       : state => state.url,
  labels    : state => state.labels,
  rates     : state => state.rates,
  searchWord: state => state.searchWord,
}

export const mutations = {
  setCaptions  : (state, captions)   => state.captions   = captions,
  setLangList  : (state, langList)   => state.langList   = langList,
  setVideoId   : (state, videoId)    => state.videoId    = videoId,
  setUrl       : (state, url)        => state.url        = url,
  setLabels    : (state, labels)     => state.labels     = labels,
  setRates     : (state, rates)      => state.rates      = rates,
  setSearchWord: (state, searchWord) => state.searchWord = searchWord,
}