const { Utils } = Shopware;

export interface BlockViewportSettings {
    colStart: number | null | 'auto';
    colEnd: number | null | 'auto';
    rowStart: number | null | 'auto';
    rowEnd: number | null | 'auto';
    order: number | null;
}

function defineViewportSettings(
    overrides?: Partial<BlockViewportSettings>
): BlockViewportSettings {
    return Utils.object.deepMergeObject(
        structuredClone(blockViewportSettings),
        overrides
    );
}

const blockViewportSettings: BlockViewportSettings = {
    colStart: null,
    colEnd: 12,
    rowStart: null,
    rowEnd: null,
    order: null,
};

export const blockSettings = {
    viewports: {
        mobile: defineViewportSettings(),
        tablet: defineViewportSettings({
            colEnd: 6,
        }),
        desktop: defineViewportSettings({
            colEnd: 6,
        }),
    },
};
