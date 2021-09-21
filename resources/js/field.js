Nova.booting((Vue, router, store) => { 
  Vue.component('detail-targomaan', require('./components/DetailField').default)
  Vue.component('index-targomaan', require('./components/IndexField').default)
  Vue.component('form-targomaan', require('./components/FormField').default)
})
