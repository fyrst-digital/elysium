import { 
    TextAlign, 
    JustifyContent, 
    AlignItems, 
    ObjectFit, 
    ObjectPositionX, 
    ObjectPositionY, 
    BgGradient, 
    BgEffect, 
} from 'blurElysium/types/styles'

export type SlideLayoutOrder = 'default' | 'reverse'

export type SlideHeadline = {
    color: string | null
    element: 'div' | 'h1' | 'h2' | 'h3' | 'h4' | 'h5' | 'h6'
}

export type SlideDescription = {
    color: string | null
}

export type SlideLinking = {
    type: 'custom' | 'product' | 'category',
    buttonAppearance: 'primary' | 'secondary' | 'info' | 'success' | 'danger' | 'warning',
    openExternal: boolean
    overlay: boolean
    showProductFocusImage: boolean
}

export type SlideTemplate = string | null | 'default'

export type CustomTemplateFile = string | null

export interface SlideConfig {
    headline: SlideHeadline
    description: SlideDescription
    linking: SlideLinking
    bgColor: string | null
    bgGradient: BgGradient
    cssClass: string | null
}

export interface ContainerConfig {
    bgColor: string | null
    bgEffect: BgEffect
}

export interface ViewportContainerConfig {
    paddingX?: number
    paddingY?: number
    borderRadius?: number
    maxWidth?: number
    maxWidthDisabled?: boolean
    gap?: number
    justifyContent?: JustifyContent
    alignItems?: AlignItems
    columnWrap?: boolean
    order?: SlideLayoutOrder
}

export interface ViewportContentConfig {
    paddingX?: number
    paddingY?: number
    maxWidth?: number
    maxWidthDisabled?: boolean
    textAlign?: TextAlign
}

export interface ViewportImageConfig {
    justifyContent?: JustifyContent
    maxWidth?: number
    maxWidthDisabled?: boolean
    imageFullWidth?: false
}

export interface ViewportSlideConfig {
    paddingX?: number
    paddingY?: number
    borderRadius?: number
    alignItems?: AlignItems
    justifyContent?: JustifyContent
}

export interface ViewportCoverMediaConfig {
    objectPosX?: ObjectPositionX
    objectPosY?: ObjectPositionY
    objectFit?: ObjectFit
}

export interface ViewportCoverImageConfig extends ViewportCoverMediaConfig {}

export interface ViewportCoverVideoConfig extends ViewportCoverMediaConfig {}

export interface ViewportTextConfig {
    fontSize?: number | null
}

export interface ViewportHeadlineConfig extends ViewportTextConfig {}

export interface ViewportDescriptionConfig extends ViewportTextConfig {}

export interface ViewportConfig {
    container: ViewportContainerConfig
    content: ViewportContentConfig
    image: ViewportImageConfig
    slide: ViewportSlideConfig
    coverImage: ViewportCoverImageConfig
    coverVideo: ViewportCoverVideoConfig
    headline: ViewportHeadlineConfig
    description: ViewportDescriptionConfig
}

export interface SlideViewports {
    mobile: ViewportConfig
    tablet: ViewportConfig
    desktop: ViewportConfig
}

export interface SlideSettings {
    slide: SlideConfig
    container: ContainerConfig
    viewports: SlideViewports
    slideTemplate: SlideTemplate
    customTemplateFile: CustomTemplateFile
}