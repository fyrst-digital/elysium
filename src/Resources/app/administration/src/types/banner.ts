interface AspectRatio {
    width: number | null;
    height: number | null;
    auto: boolean;
}

export interface ViewportSettings {
    aspectRatio: AspectRatio;
    maxHeight: number | null;
}

interface ElysiumSlide {
    source: 'static';
    value: string | null;
}

interface LazyLoading {
    source: 'static';
    value: boolean | null;
}

interface BannerViewports {
    source: 'static';
    value: {
        mobile: ViewportSettings;
        tablet: ViewportSettings;
        desktop: ViewportSettings;
    };
}

export interface BannerSettings {
    elysiumSlide: ElysiumSlide;
    lazyLoading: LazyLoading;
    viewports: BannerViewports;
}
