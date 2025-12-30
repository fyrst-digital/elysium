<template>
    <div
        :class="[
            'py-base-field',
            props.layout ? `layout-${props.layout}` : 'layout-row'
        ]">
        <div
            :style="{
                display: 'flex',
                flexDirection: 'row',
                flexWrap: 'wrap',
                alignItems: 'center',
                gap: '4px',
                marginBlock: '6px'
            }">
            <label 
                v-if="props.label"
                v-html="props.label" 
                class="py-label"
                :style="{
                    fontSize: '14px',
                    textOverflow: 'ellipsis',
                    overflow: 'hidden',
                }" />
            <span 
                v-if="props.required"
                v-html="'*'"
                :style="{ 
                    color: 'var(--color-icon-critical-default)',
                    fontWeight: '600' 
                }" />
            <blur-icon
                v-if="props.tooltip"
                v-tooltip="{
                    message: props.tooltip,
                }"
                :style="{
                    verticalAlign: 'text-top',
                    cursor: 'pointer'
                }"
                name="blurph-info-bold"
                :size="14"
                color="var(--color-interaction-primary-default)" />
        </div>
        <slot />
    </div>
</template>

<script setup lang="ts">
import './style.scss'

type BaseFieldProps = {
    label?: string;
    layout?: 'row' | 'column';
    required?: boolean;
    tooltip?: string;
}

const { Component } = Shopware

const props = defineProps<BaseFieldProps>()

Component.createExtendableSetup(
    {
        props,
        name: 'pyBaseField',
    },
    () => {
        return {
            public: {}
        }
    },
)
</script>