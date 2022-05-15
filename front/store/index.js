export const state = () => ({
  captions    : [],
  videoId     : null,
  url         : null,
  captionLang : null,
  iframe      : null
});

export const getters = {
  captions    : state => state.captions,
  videoId     : state => state.videoId,
  url         : state => state.url,
  captionLang : state => state.captionLang,
  iframe      : state => state.iframe
}

export const mutations = {
  setCaptions    : (state, captions)    => state.captions = captions,
  setVideoId     : (state, videoId)     => state.videoId = videoId,
  setUrl         : (state, url)         => state.url = url,
  setCaptionLang : (state, captionLang) => state.captionLang = captionLang,
  setIframe      : (state, iframe)      => state.iframe = iframe
}