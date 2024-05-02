export default {
    methods: {
        loadIconSvgData (variant: string, iconName: string, iconFullName: string) {
            return import(`blurElysium/icons/${variant}/${iconName}.svg`).then((iconSvgData) => {

                if (iconSvgData.default) {
                    this.iconSvgData = iconSvgData.default;
                } else {
                    // note this only happens if the import exists but does not export a default
                    console.error(`The SVG file for the icon name ${iconFullName} could not be found and loaded.`);
                    this.iconSvgData = '';
                }
            });
        }
    }
};