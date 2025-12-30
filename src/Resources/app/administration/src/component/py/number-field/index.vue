<template>
    <py-base-field
        :label="label">

        <mt-number-field
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
        name: 'pyTextField',
    },
    () => {
        return {
            public: {}
        }
    },
)
</script>