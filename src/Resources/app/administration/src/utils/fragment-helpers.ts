import { previewSchema } from '../schema/slide-preview';

export function getFragmentsForFields(changedFields) {
    const hasSlideWildcard = changedFields.includes('slide');
    return previewSchema.fragments.filter((fragment) => {
        if (hasSlideWildcard) return true;
        return fragment.watchedFields.some((f) => changedFields.includes(f));
    });
}
