<template>
  <div>
    <div class="flex border-b border-40 -mx-6 px-6"> 
      <div class="flex -mx-3">        
        <h5 
          v-for="(language, locale) in availableLocales" 
          class="remove-last-margin-bottom text-center cursor-pointer p-3 text-70" 
          :class="{'text-info': locale == activeLocale}"
          @click="setActiveLocale(locale)"
        >{{
          language
        }}</h5>
      </div>
    </div>  

    <component 
      :key="index"
      v-for="(field, index) in fields"
      v-if="field.locale == activeLocale"
      :is="resolveComponentName(field)"
      :resource-name="resourceName"
      :resource-id="resourceId"
      :resource="resource"
      :field="field" 
      :class="{'remove-bottom-border': removeBottomBorder(index, field.locale)}"
    /> 
  </div>      
</template>

<script>
import HandleActiveLocale from './HandleActiveLocale.vue'
import HandleToolbar from './HandleToolbar.vue'

export default {
    mixins: [HandleActiveLocale, HandleToolbar],
    props: ['resource', 'resourceName', 'resourceId', 'field'],  

    methods: { 
      /**
       * Resolve the component name.
       */
      resolveComponentName(field) {
        return field.prefixComponent
          ? 'detail-' + field.component
          : field.component
      }, 

      removeBottomBorder(index) {
        if (index < this.fields.length - 1) {
          return false;
        } 

        return this.$el.classList.contains('remove-bottom-border');
      }
    },

    computed: {
      fields() {
        return this.field.fields.filter(field => field.locale == this.activeLocale)
      }
    }
};
</script>
