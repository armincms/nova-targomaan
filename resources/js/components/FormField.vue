<template>
  <div class="divide-y divide-gray-100 dark:divide-gray-700">
    <field-wrapper v-if="shouldDisplayLocales">
      <div class="w-1/5"></div>
      <div class="px-6 flex">
        <h5
          v-for="(language, locale) in availableLocales"
          :key="field.attribute + '-' + locale"
          class="remove-last-margin-bottom text-center cursor-pointer px-2 py-1 font-semibold uppercase"
          @click="setActiveLocale(locale)"
          :class="{
            'text-red-600': locale == activeLocale,
            'text-gray-400 dark:text-gray-300': locale != activeLocale,
          }"
        >
          {{ language }}
        </h5>
      </div>
    </field-wrapper>
    <component
      v-for="(field, index) in field.fields"
      v-show="field.locale == activeLocale"
      :index="index"
      :key="index"
      :is="`form-${field.component}`"
      :errors="validationErrors"
      :resource-id="resourceId"
      :resource-name="resourceName"
      :related-resource-name="relatedResourceName"
      :related-resource-id="relatedResourceId"
      :field="field"
      :via-resource="viaResource"
      :via-resource-id="viaResourceId"
      :via-relationship="viaRelationship"
      :shown-via-new-relation-modal="shownViaNewRelationModal"
      :form-unique-id="formUniqueId"
      :mode="mode"
      @field-shown="handleFieldShown"
      @field-hidden="handleFieldHidden"
      @field-changed="$emit('field-changed')"
      @file-deleted="$emit('update-last-retrieved-at-timestamp')"
      @file-upload-started="$emit('file-upload-started')"
      @file-upload-finished="$emit('file-upload-finished')"
      :show-help-text="showHelpText"
    />
  </div>
</template>

<script>
import HandleActiveLocale from "./HandleActiveLocale.vue";
import HandleToolbar from "./HandleToolbar.vue";
import { FormField, HandlesValidationErrors } from "laravel-nova";

export default {
  mixins: [
    FormField,
    HandlesValidationErrors,
    HandleActiveLocale,
    HandleToolbar,
  ],

  props: ["resourceName", "resourceId", "field"],

  methods: {
    /*
     * Set the initial, internal value for the field.
     */
    setInitialValue() {},

    /**
     * Fill the given FormData object with the field's internal value.
     */
    fill(formData) {
      _.each(this.field.fields, (field) => field.fill(formData));
    },
  },

  watch: {
    errors: function(errors) {
      var firstError = _.keys(errors.errors)[0];
      var errorLocale = _.keys(this.field.locales).find((locale) =>
        _.endsWith(firstError, this.field.delimiter + locale)
      );

      this.setActiveLocale(errorLocale ? errorLocale : this.activeLocale);
    },
  },
};
</script>
