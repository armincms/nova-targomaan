Nova.booting((Vue, router, store) => { 
  Vue.component('detail-targomaan', require('./components/DetailField').default)
  Vue.component('form-targomaan', require('./components/FormField').default)
})
