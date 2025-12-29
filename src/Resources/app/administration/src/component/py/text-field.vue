<template>
    <label v-if="label">{{ label }}</label>
    <mt-text-field
        class="py-field"
        v-bind="$attrs"
    ></mt-text-field>
</template>

<script setup lang="ts">
import {computed, reactive, ref} from "vue";

const { Component } = Shopware;

const props = defineProps<{
    label?: string;
}>();

const {
    baseValue,
    reactiveValue,
    increment,
    privateStuff,
    message,
} = Component.createExtendableSetup(
    {
        props,
        name: 'pyTextField',
    },
    () => {
        const baseValue = ref(1);
        const reactiveValue = reactive({
            very: {
                deep: {
                    value: 'deep',
                }
            }
        })

        const increment = () => {
            baseValue.value++;
        }

        const privateStuff = ref('Very private stuff')

        const message = ref('Original message')

        return {
            private: {
                privateStuff,
            },
            public: {
                baseValue,
                reactiveValue,
                increment,
                message,
            },
        };
    },
)
</script>