import { ButtonColor, ButtonSize } from '@elysium/types/button';

interface ButtonColorOption {
    value: ButtonColor;
    label: string;
}

interface ButtonSizeOption {
    value: ButtonSize;
    label: string;
}

export const buttonColors: ButtonColorOption[] = [
    { value: 'primary', label: 'blurElysium.general.colorStates.primary' },
    { value: 'secondary', label: 'blurElysium.general.colorStates.secondary' },
    { value: 'success', label: 'blurElysium.general.colorStates.success' },
    { value: 'danger', label: 'blurElysium.general.colorStates.danger' },
    { value: 'warning', label: 'blurElysium.general.colorStates.warning' },
    { value: 'info', label: 'blurElysium.general.colorStates.info' },
    { value: 'light', label: 'blurElysium.general.colorStates.light' },
    { value: 'dark', label: 'blurElysium.general.colorStates.dark' },
    { value: 'link', label: 'blurElysium.general.colorStates.link' },
    {
        value: 'outline-primary',
        label: 'blurElysium.general.colorStates.outlinePrimary',
    },
    {
        value: 'outline-secondary',
        label: 'blurElysium.general.colorStates.outlineSecondary',
    },
    {
        value: 'outline-success',
        label: 'blurElysium.general.colorStates.outlineSuccess',
    },
    {
        value: 'outline-danger',
        label: 'blurElysium.general.colorStates.outlineDanger',
    },
    {
        value: 'outline-warning',
        label: 'blurElysium.general.colorStates.outlineWarning',
    },
    {
        value: 'outline-info',
        label: 'blurElysium.general.colorStates.outlineInfo',
    },
    {
        value: 'outline-light',
        label: 'blurElysium.general.colorStates.outlineLight',
    },
    {
        value: 'outline-dark',
        label: 'blurElysium.general.colorStates.outlineDark',
    },
];

export const buttonSizes: ButtonSizeOption[] = [
    { value: 'sm', label: 'blurElysium.general.sm' },
    { value: 'md', label: 'blurElysium.general.md' },
    { value: 'lg', label: 'blurElysium.general.lg' },
];
