<script> 
export default {   
  data() {
    return {
      activeLocale: {
        type: String,
      }
    }
  },

  mounted() {
    this.handleActiveLocale(this.field.activeLocale)

    Nova.$on('targomaan.locale', (locale) => {
      this.handleChangeLocale(locale) 
    })
  },

  methods: {
    handleChangeLocale: function (locale) {  
      this.activeLocale === locale || this.setActiveLocale(locale)
    },

    handleActiveLocale(locale) {
      this.activeLocale = locale; 
    }, 

    /**
     * Set the active locale.
     */
     setActiveLocale(locale) {
      this.activeLocale = locale

      Nova.$emit('targomaan.locale', locale)
    }
  }, 

  computed: {
    availableLocales() {
      return this.field.locales;
    },

    shouldDisplayLocales() {
      return Object.keys(this.field.locales).length > 1 && this.field.displayLocales
    }
  },
};
</script>
