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
            :is="`form-${field.component}`"
            :errors="errors"
            :resource-id="resourceId"
            :resource-name="resourceName"
            :field="field"
            :via-resource="viaResource"
            :via-resource-id="viaResourceId"
            :via-relationship="viaRelationship"
            :shown-via-new-relation-modal="shownViaNewRelationModal"
            @file-deleted="$emit('update-last-retrieved-at-timestamp')"
            />
    </div>      
</template>

<script> 
import { FormField } from 'laravel-nova'

export default { 
    mixins: [FormField],
    
    props: {   
        errors: {
          type: Object,
          required: false,
        }, 

        resourceName: {
          type: String,
          required: true,
        },

        resourceId: {
          type: [Number, String],
        },

        viaResource: {
          type: String,
        },

        viaResourceId: {
          type: [Number, String],
        },

        viaRelationship: {
          type: String,
        },
    }, 

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
        /*
         * Set the initial, internal value for the field.
         */
        setInitialValue() { 
        },

        /**
         * Fill the given FormData object with the field's internal value.
         */
        fill(formData) {  
            _.toArray(this.field.fields).forEach(field => {
                field.fill(formData)
            }) 
        },

        /**
         * Update the field's internal value.
         */
        handleChange(value) { 
        }, 

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
        errors: function (errors) { 
            var firstError  = _.keys(errors.errors)[0]
            var errorLocale = _.keys(this.field.locales).find(
                locale => _.endsWith(firstError, this.field.delimiter + locale)
            ) 

            this.setActiveLocale(errorLocale ? errorLocale : this.activeLocale) 
        },

        'targomaan.locale': function(locale) {
            this.activeLocale === locale || this.setActiveLocale(locale)
        }
    }
}
</script>
