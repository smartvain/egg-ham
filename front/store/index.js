export const state = () => ({
  captions       : [],
  videoId        : null,
  url            : null,
  captionLang    : null,
  iframe         : null,
  videoTitle     : null,
  characterLimit : null,
  characterCount : null
});

export const getters = {
  captions        : state => state.captions,
  videoId         : state => state.videoId,
  url             : state => state.url,
  captionLang     : state => state.captionLang,
  iframe          : state => state.iframe,
  videoTitle      : state => state.videoTitle,
  characterLimit  : state => state.characterLimit,
  characterCount  : state => state.characterCount,
  characterRemain : state => state.characterLimit - state.characterCount
}

export const mutations = {
  setCaptions       : (state, captions)       => state.captions       = captions,
  setVideoId        : (state, videoId)        => state.videoId        = videoId,
  setUrl            : (state, url)            => state.url            = url,
  setCaptionLang    : (state, captionLang)    => state.captionLang    = captionLang,
  setIframe         : (state, iframe)         => state.iframe         = iframe,
  setVideoTitle     : (state, videoTitle)     => state.videoTitle     = videoTitle,
  setCharacterLimit : (state, characterLimit) => state.characterLimit = characterLimit,
  setCharacterCount : (state, characterCount) => state.characterCount = characterCount,
}