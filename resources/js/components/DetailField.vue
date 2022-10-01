<template>
  <div class="divide-y divide-gray-100 dark:divide-gray-700">
    <div class="flex -mx-6" v-if="shouldDisplayLocales">
      <h5
        v-for="(language, locale) in availableLocales"
        :key="field.attribute + '-' + locale"
        class="remove-last-margin-bottom text-center cursor-pointer px-6 py-1 font-semibold uppercase"
        @click="setActiveLocale(locale)"
        :class="{
          'text-red-600': locale == activeLocale,
          'text-gray-400 dark:text-gray-300': locale != activeLocale,
        }"
      >
        {{ language }}
      </h5>
    </div>
    <component
      v-for="(field, index) in field.fields"
      :key="index"
      :index="index"
      :is="resolveComponentName(field)"
      :resource-name="resourceName"
      :resource-id="resourceId"
      :resource="resource"
      :field="field"
      @actionExecuted="actionExecuted"
    />
  </div>
</template>

<script>
import HandleActiveLocale from "./HandleActiveLocale.vue";
import HandleToolbar from "./HandleToolbar.vue";
export default {
  mixins: [HandleActiveLocale, HandleToolbar],
  props: ["index", "resource", "resourceName", "resourceId", "field"],

  methods: {
    /**
     * Resolve the component name.
     */
    resolveComponentName(field) {
      return field.prefixComponent
        ? "detail-" + field.component
        : field.component;
    },

    removeBottomBorder(index) {
      if (index < this.fields.length - 1) {
        return false;
      }

      return this.$el.classList.contains("remove-bottom-border");
    },
  },

  computed: {
    fields() {
      return this.field.fields.filter(
        (field) => field.locale == this.activeLocale
      );
    },
  },
};
</script>
