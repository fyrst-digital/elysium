import { Media } from '@elysium/types/slide';
import { ContentSettings } from '@elysium/types/slide';

interface MediaState {
    cache: Record<string, Media>;
}

export default {
    id: 'elysiumMedia',

    state: (): MediaState => ({
        cache: {},
    }),

    actions: {
        setMedia(id: string, media: Media): void {
            this.cache[id] = media;
        },

        getMedia(id: string): Media | null {
            return this.cache[id] ?? null;
        },

        hasMedia(id: string): boolean {
            return id in this.cache;
        },

        getResolvedMedia(contentSettings: ContentSettings | null | undefined): Record<string, Media> {
            const resolved: Record<string, Media> = {};
            if (!contentSettings) return resolved;

            const cover = contentSettings.slideCover ?? {};
            const ids = [
                cover.mobileId,
                cover.tabletId,
                cover.desktopId,
                cover.videoId,
                contentSettings.focusImageId,
            ].filter((id): id is string => Boolean(id));

            ids.forEach((id) => {
                if (this.cache[id]) {
                    resolved[id] = this.cache[id];
                }
            });

            return resolved;
        },

        getUncachedIds(ids: string[]): string[] {
            return ids.filter((id) => !this.cache[id]);
        },
    },
};
