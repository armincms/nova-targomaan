import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-targomaan', IndexField)
  app.component('detail-targomaan', DetailField)
  app.component('form-targomaan', FormField)
})
