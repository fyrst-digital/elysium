<template>
    <py-base-field
        :label="label">

        <mt-select
            ref="MtSelectElement"
            class="py-field"
            v-bind="$attrs"
            @select-expanded="onOpen"
        >
        </mt-select>
    </py-base-field>
</template>

<script setup lang="ts">
import { nextTick } from 'vue'

const { Component } = Shopware

const props = defineProps<{
    label?: string;
}>();

const onOpen = async () => {
    await nextTick();
    const popover = document.body.querySelector('.mt-select-result-list-popover-wrapper') || null;
    if (popover) {
        (popover as HTMLElement).classList.add('py-select-popover');
    }
}

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