export function useViewportProp(
        property: string,
        deviceView: string,
        // fallback: string | number,
        viewportsSettings: object,
        // snippetPrefix: string | null = null
) {
    let result = null

    const currentViewportIndex = (deviceViewport, viewportsSettings) => {
        return Object.entries(viewportsSettings).findIndex(
            (element) => element[0] === deviceViewport
        )
    }

    Object.values(viewportsSettings).forEach((settings, index) => {
        const settingValue: string | number | undefined | null = <
            string | number | undefined | null
        >property.split('.').reduce((r, k) => r?.[k], settings);
        if (
            !(
                settingValue === null || settingValue === undefined
            ) &&
            currentViewportIndex(deviceView, viewportsSettings) >= index
        ) {
            result = settingValue;
        }
    })

    return result;
}