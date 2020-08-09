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
        this.handleActiveLocale(this.field.active)

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
    }
}
</script>
