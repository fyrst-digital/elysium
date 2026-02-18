export interface DragConfigData {
    draggedItemIndex?: number;
}

export interface DragConfig {
    data?: DragConfigData;
}

export interface DragData {
    draggedItemIndex?: number;
}

export interface DropData {
    index?: number;
}

export interface TabRoute {
    name: string;
    params?: Record<string, string>;
}

export interface SidebarTab {
    label: string;
    routeSuffix: string;
    route: () => TabRoute;
}