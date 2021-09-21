<template>
  <div>
    <field-wrapper v-if="shouldDisplayLocales">
      <div class="w-1/5"></div>
      <div class="px-6 flex">        
        <h5 
          v-for="(language, locale) in availableLocales" 
          class="remove-last-margin-bottom text-center cursor-pointer p-3 text-70" 
          :class="{'text-info': locale == activeLocale}"
          @click="setActiveLocale(locale)"
        >{{
          language
        }}</h5>
      </div> 
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
      @field-changed="$emit('field-changed')"
      @file-deleted="$emit('update-last-retrieved-at-timestamp')"
      @file-upload-started="$emit('file-upload-started')"
      @file-upload-finished="$emit('file-upload-finished')"
      :show-help-text="field.helpText != null"
    />  
  </div>      
</template>

<script> 
import HandleActiveLocale from './HandleActiveLocale.vue'
import HandleToolbar from './HandleToolbar.vue'
import { FormField } from 'laravel-nova'

export default { 
  mixins: [FormField, HandleActiveLocale, HandleToolbar],

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
      _.each(this.field.fields, field => field.fill(formData)) 
    }, 

    /**
     * Update the field's internal value.
     */
     handleChange(value) { 
    }, 
  }, 

  watch: {  
    errors: function (errors) { 
      var firstError  = _.keys(errors.errors)[0]
      var errorLocale = _.keys(this.field.locales).find(
        locale => _.endsWith(firstError, this.field.delimiter + locale)
        ) 

      this.setActiveLocale(errorLocale ? errorLocale : this.activeLocale) 
    }, 
  }
};
</script>
