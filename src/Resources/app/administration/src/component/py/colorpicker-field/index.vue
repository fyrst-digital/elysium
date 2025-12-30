<template>
    <py-base-field
        :label="label">

        <mt-colorpicker
            class="py-field"
            v-bind="$attrs"
            :model-value="validatedValue"
            @update:modelValue="update"
        />
    </py-base-field>
</template>

<script setup lang="ts">
    import { computed } from 'vue';

const { Component } = Shopware

const props = defineProps<{
    label?: string;
    value?: string | null;
}>();

const emit = defineEmits<{
    (e: 'update:value', value: string | null): void;
}>();

const update = (value: string | null) => {
    emit('update:value', value);
};

const validatedValue = computed(() => {
    return props.value ?? '';
});

Component.createExtendableSetup(
    {
        props,
        name: 'pyColorpickerField',
    },
    () => {
        return {
            public: {}
        }
    },
)
</script>