interface ButtonColor {
    value: 'primary' | 'secondary' | 'success' | 'danger' | 'warning' | 'info' | 'light' | 'dark' | 'link' | 'outline-primary' | 'outline-secondary' | 'outline-success' | 'outline-danger' | 'outline-warning' | 'outline-info' | 'outline-light' | 'outline-dark';
    label: string;
}

export const buttonColors: ButtonColor[] = [
    { value: 'primary', label: 'blurElysium.general.colorStates.primary' },
    { value: 'secondary', label: 'blurElysium.general.colorStates.secondary' },
    { value: 'success', label: 'blurElysium.general.colorStates.success' },
    { value: 'danger', label: 'blurElysium.general.colorStates.danger' },
    { value: 'warning', label: 'blurElysium.general.colorStates.warning' },
    { value: 'info', label: 'blurElysium.general.colorStates.info' },
    { value: 'light', label: 'blurElysium.general.colorStates.light' },
    { value: 'dark', label: 'blurElysium.general.colorStates.dark' },
    { value: 'link', label: 'blurElysium.general.colorStates.link' },
    { value: 'outline-primary', label: 'blurElysium.general.colorStates.outlinePrimary' },
    { value: 'outline-secondary', label: 'blurElysium.general.colorStates.outlineSecondary' },
    { value: 'outline-success', label: 'blurElysium.general.colorStates.outlineSuccess' },
    { value: 'outline-danger', label: 'blurElysium.general.colorStates.outlineDanger' },
    { value: 'outline-warning', label: 'blurElysium.general.colorStates.outlineWarning' },
    { value: 'outline-info', label: 'blurElysium.general.colorStates.outlineInfo' },
    { value: 'outline-light', label: 'blurElysium.general.colorStates.outlineLight' },
    { value: 'outline-dark', label: 'blurElysium.general.colorStates.outlineDark' },
]

interface ButtonSize {
    value: 'sm' | 'default' | 'lg';
    label: string;
}

export const buttonSizes: ButtonSize[] = [
    { value: 'sm', label: 'blurElysium.general.sm' },
    { value: 'default', label: 'blurElysium.general.default' },
    { value: 'lg', label: 'blurElysium.general.lg' },
]