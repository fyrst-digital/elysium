<template>
    <py-base-field
        :label="label">

        <mt-slider
            class="py-slider-field"
            v-bind="$attrs"
        />
    </py-base-field>
</template>

<script setup lang="ts">

const { Component } = Shopware

const props = defineProps<{
    label?: string;
}>();

Component.createExtendableSetup(
    {
        props,
        name: 'pySliderField',
    },
    () => {
        return {
            public: {}
        }
    },
)
</script>