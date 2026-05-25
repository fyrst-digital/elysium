export interface FieldMapping {
    path: string;
    fields: string[];
    deep?: boolean;
}

export interface FragmentDefinition {
    name: string;
    mode: string;
    watchedFields: string[];
    domSelector?: string;
    insertStrategy?: string;
    fallbackContainer?: string;
    fallbackPosition?: string;
}

export interface PreviewSchema {
    elementType: string;
    fieldMappings: FieldMapping[];
    fragments: FragmentDefinition[];
}

export { previewSchema } from '../schema/slide-preview';
export { getFragmentsForFields } from '../utils/fragment-helpers';
