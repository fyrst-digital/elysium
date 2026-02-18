export interface MediaItem {
    id?: string;
    targetId?: string;
    path?: string;
}

export interface MediaModal {
    open: boolean;
    type: string | null;
}