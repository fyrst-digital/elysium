import {
    TextAlign,
    JustifyContent,
    AlignItems,
    ObjectFit,
    ObjectPositionX,
    ObjectPositionY,
    BgGradient,
    BgEffect,
} from '@elysium/types/styles';

import { ButtonColor, ButtonSize } from '@elysium/types/button';

export type SlideLayoutOrder = 'default' | 'reverse';

export type SlideHeadline = {
    color: string | null;
    element: 'div' | 'h1' | 'h2' | 'h3' | 'h4' | 'h5' | 'h6';
};

export type SlideDescription = {
    color: string | null;
};

export type SlideLinking = {
    type: 'custom' | 'product' | 'category';
    buttonAppearance: ButtonColor;
    buttonSize: ButtonSize;
    openExternal: boolean;
    overlay: boolean;
    showProductFocusImage: boolean;
};

export type SlideTemplate = string | null | 'default';

export type CustomTemplateFile = string | null;

export interface SlideConfig {
    headline: SlideHeadline;
    description: SlideDescription;
    linking: SlideLinking;
    bgColor: string | null;
    bgGradient: BgGradient;
    cssClass: string | null;
}

export interface ContainerConfig {
    bgColor: string | null;
    bgEffect: BgEffect;
}

export interface ViewportContainerConfig {
    paddingX?: number | null;
    paddingY?: number | null;
    borderRadius?: number;
    maxWidth?: number | null;
    gap?: number | null;
    justifyContent?: JustifyContent | null;
    alignItems?: AlignItems | null;
    columnWrap?: boolean;
    order?: SlideLayoutOrder | null;
}

export interface ViewportContentConfig {
    paddingX?: number | null;
    paddingY?: number | null;
    maxWidth?: number | null;
    textAlign?: TextAlign | null;
}

export interface ViewportImageConfig {
    justifyContent?: JustifyContent | null;
    maxWidth?: number | null;
}

export interface ViewportSlideConfig {
    paddingX?: number | null;
    paddingY?: number | null;
    borderRadius?: number | null;
    alignItems?: AlignItems | null;
    justifyContent?: JustifyContent | null;
}

export interface ViewportCoverMediaConfig {
    objectPosX?: ObjectPositionX | null;
    objectPosY?: ObjectPositionY | null;
    objectFit?: ObjectFit | null;
}

export interface ViewportCoverImageConfig extends ViewportCoverMediaConfig {}

export interface ViewportCoverVideoConfig extends ViewportCoverMediaConfig {}

export interface ViewportTextConfig {
    fontSize?: number | null;
}

export interface ViewportHeadlineConfig extends ViewportTextConfig {}

export interface ViewportDescriptionConfig extends ViewportTextConfig {}

export interface ViewportConfig {
    container: ViewportContainerConfig;
    content: ViewportContentConfig;
    image: ViewportImageConfig;
    slide: ViewportSlideConfig;
    coverMedia: ViewportCoverMediaConfig;
    headline: ViewportHeadlineConfig;
    description: ViewportDescriptionConfig;
}

export interface SlideViewports {
    mobile: ViewportConfig;
    tablet: ViewportConfig;
    desktop: ViewportConfig;
}

export interface SlideSettings {
    slide: SlideConfig;
    container: ContainerConfig;
    viewports: SlideViewports;
    slideTemplate: SlideTemplate;
    customTemplateFile: CustomTemplateFile;
}

export interface ContentSettings {
    slideCover: {
        alt: string | null;
        title: string | null;
    }
}

export interface Product {
    id: string;
    name: string | null;
    cover?: {
        media: {
            url: string;
        };
    };
}

export interface Category {
    id: string;
    name: string | null;
}

export interface Media {
    id: string;
    url: string;
    name: string | null;
}

export interface ElysiumSlide {
    id: string;
    name: string | null;
    active: boolean;
    slideSettings: SlideSettings;
    slideCover: Media | null;
    slideCoverId: string | null;
    slideCoverMobile: Media | null;
    slideCoverMobileId: string | null;
    slideCoverTablet: Media | null;
    slideCoverTabletId: string | null;
    slideCoverVideo: Media | null;
    slideCoverVideoId: string | null;
    productId: string | null;
    product: Product | null;
    categoryId: string | null;
    category: Category | null;
    url: string | null;
    buttonLabel: string | null;
    translated: {
        name: string | null;
        title: string | null;
    };
    customFields: Record<string, unknown>;
}