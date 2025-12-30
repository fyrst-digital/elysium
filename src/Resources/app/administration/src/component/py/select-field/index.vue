<template>
    <py-base-field
        :label="label">

        <mt-select
            class="py-field"
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
        name: 'pySelectField',
    },
    () => {
        return {
            public: {}
        }
    },
)
</script>