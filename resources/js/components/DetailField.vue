<template>
    <div>
        <field-wrapper v-if="showToolbar">
            <div 
                v-for="(language, locale) in field.locales" 
                class="bg-20 remove-last-margin-bottom py-2 px-8 w-full text-center cursor-pointer font-semibold" 
                :class="{'bg-success text-white': locale == activeLocale}"
                @click="setActiveLocale(locale)"
            >{{
                language
            }}</div>
        </field-wrapper>
        <component 
            v-for="(field, index) in field.fields"
            v-show="field.locale == activeLocale"
            :key="index"
            :is="`detail-${field.component}`" 
            :resource-id="resourceId"
            :resource-name="resourceName"
            :field="field" 
            />
    </div> 
</template>

<script>
export default {
    props: ['resource', 'resourceName', 'resourceId', 'field'],

    data() {
        return {
            activeLocale: {
                type: String,
            }
        }
    },

    mounted() {
        this.activeLocale = this.field.active; 
    },

    methods: { 
        /**
         * Set the active locale.
         */
        setActiveLocale(locale) {
            this.activeLocale = locale

            this.$emit('targomaan.locale', locale)
        }
    },

    computed: {
        showToolbar: function() { 
            return this.field.showToolbar && _.keys(this.field.locales).length > 1;
        }
    },

    watch: {   
        'targomaan.locale': function(locale) {
            this.activeLocale === locale || this.setActiveLocale(locale)
        }
    }
}
</script>
