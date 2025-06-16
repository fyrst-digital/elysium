export interface ElysiumSlideCollection {
    source: 'static';
    value: string[] | null;
}

export interface AspectRatio {
    width?: number | null;
    height?: number | null;
    auto?: boolean | null;
}

export interface Sizing {
    aspectRatio?: AspectRatio;
    maxHeight?: number | null;
    maxHeightScreen?: boolean | null;
    paddingY?: number | null;
    paddingX?: number | null;
    slidesGap?: number | null;
}

export interface Content {
    source: 'static';
    value: {
        title: string | null;
    };
}

export interface General {
    source: 'static';
    value: {
        overlay: boolean;
        containerWidth: 'content' | 'full';
        rewind: boolean;
        speed: number;
        pauseOnHover: boolean;
        autoplay: Autoplay;
    };
}

export interface Autoplay {
    active: boolean;
    interval: number;
    pauseOnHover: boolean;
}

export interface NavigationColors {
    default: string;
    active: string;
}

export interface Navigation {
    source: 'static';
    value: {
        active: boolean;
        position: 'below_slider';
        align: 'center';
        shape: 'circle' | 'bar' | 'ring';
        colors: NavigationColors;
    };
}

export interface ArrowColors {
    default: string;
    active: string;
}

export interface ArrowBgColors {
    default: string;
    active: string;
}

export interface ArrowIcon {
    default: string;
    customPrev: string;
    customNext: string;
}

export interface Arrows {
    source: 'static';
    value: {
        active: boolean;
        icon: ArrowIcon;
        colors: ArrowColors;
        bgColors: ArrowBgColors;
        position: 'in_slider';
    };
}

export interface ViewportGeneral {
    slidesPerPage: number | null;
}

export interface ViewportNavigation {
    size?: 'sm' | 'md' | null;
    gap?: number | null;
}

export interface ViewportArrows {
    iconSize: number | null;
}

export interface ViewportSettings {
    settings: ViewportGeneral;
    navigation: ViewportNavigation;
    arrows: ViewportArrows;
    sizing: Sizing;
}

export interface SliderViewports {
    source: 'static';
    value: {
        mobile: ViewportSettings;
        tablet: ViewportSettings;
        desktop: ViewportSettings;
    };
}

export interface SliderSettings {
    elysiumSlideCollection: ElysiumSlideCollection;
    content: Content;
    settings: General;
    navigation: Navigation;
    arrows: Arrows;
    viewports: SliderViewports;
}
