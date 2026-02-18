const { Mixin, Component } = Shopware;

// give the mixin a name and feed it into the register function as the second argument
export default Mixin.register(
    'blur-device-utilities',
    Component.wrapComponentConfig({
        data() {
            return {
                viewportsSettings: null,
            };
        },

        computed: {
            currentViewportIndex() {
                return Object.entries(this.viewportsSettings).findIndex(
                    (element) => element[0] === this.device
                );
            },
        },

        methods: {
            deviceSwitch(device: string) {
                if (device === 'desktop') {
                    this.device = 'mobile';
                } else if (device === 'mobile') {
                    this.device = 'tablet';
                } else if (device === 'tablet') {
                    this.device = 'desktop';
                }
            },

            viewportsPlaceholder(
                property: string,
                fallback: string | number,
                snippetPrefix: string | null = null,
                viewportsSettings: object = this.viewportsSettings
            ) {
                const kebabToCamelCase = (string: string) => {
                    return string
                        .split('-')
                        .map((word, index) => {
                            return index === 0
                                ? word
                                : word.charAt(0).toUpperCase() + word.slice(1);
                        })
                        .join('');
                };

                let placeholder: string | number = fallback;

                Object.values(viewportsSettings).forEach((settings, index) => {
                    const settingValue: string | number | undefined | null = <
                        string | number | undefined | null
                    >property.split('.').reduce((r, k) => r?.[k], settings);
                    if (
                        !(
                            settingValue === null || settingValue === undefined
                        ) &&
                        this.currentViewportIndex >= index
                    ) {
                        placeholder = settingValue;
                    }
                });

                if (
                    typeof snippetPrefix === 'string' &&
                    typeof placeholder === 'string'
                ) {
                    const snippet = [kebabToCamelCase(placeholder)];
                    snippet.unshift(snippetPrefix);
                    return this.$t(snippet.join('.'));
                }

                return placeholder;
            },
        },
    })
);
