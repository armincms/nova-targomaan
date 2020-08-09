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
import HandleActiveLocale from './HandleActiveLocale.vue'
import HandleToolbar from './HandleToolbar.vue'

export default {
    mixins: [HandleActiveLocale, HandleToolbar],
    props: ['resource', 'resourceName', 'resourceId', 'field'],     
}
</script>
