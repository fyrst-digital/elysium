const { Utils } = Shopware;

export interface BlockDefaultConfig {
    marginBottom: string;
    marginTop: string;
    marginLeft: string;
    marginRight: string;
    sizingMode: string;
    customFields: {
        elysiumBlockAdvanced: {
            activeFrom: string;
            activeUntil: string;
        };
    };
}

const blockDefaults: BlockDefaultConfig = {
    marginBottom: '',
    marginTop: '',
    marginLeft: '',
    marginRight: '',
    sizingMode: 'boxed',
    customFields: {
        elysiumBlockAdvanced: {
            activeFrom: '',
            activeUntil: '',
        },
    },
};

export function defineBlockConfig(overrides?: Partial<BlockDefaultConfig>): BlockDefaultConfig {
    return Utils.object.deepMergeObject(structuredClone(blockDefaults), overrides ?? {});
}
