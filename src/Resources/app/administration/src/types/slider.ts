// export interface ElysiumSlideCollection {
//     source: 'static';
//     value: any[];
// }

// export interface Content {
//     source: 'static';
//     value: {
//         title: string;
//     };
// }

// export interface Autoplay {
//     active: boolean;
//     interval: number;
//     pauseOnHover: boolean;
// }

// export interface Settings {
//     source: 'static';
//     value: {
//         overlay: boolean;
//         containerWidth: 'content' | 'full';
//         rewind: boolean;
//         speed: number;
//         pauseOnHover: boolean;
//         autoplay: Autoplay;
//     };
// }

// export interface NavigationColors {
//     default: string;
//     active: string;
// }

// export interface Navigation {
//     source: 'static';
//     value: {
//         active: boolean;
//         position: 'below_slider';
//         align: 'center';
//         shape: 'circle' | 'bar' | 'ring';
//         colors: NavigationColors;
//     };
// }

// export interface ArrowIcon {
//     default: string;
//     customPrev: string;
//     customNext: string;
// }

// export interface ArrowColors {
//     default: string;
//     active: string;
// }

// export interface ArrowBgColors {
//     default: string;
//     active: string;
// }

// export interface Arrows {
//     source: 'static';
//     value: {
//         active: boolean;
//         icon: ArrowIcon;
//         colors: ArrowColors;
//         bgColors: ArrowBgColors;
//         position: 'in_slider';
//     };
// }

// export interface AspectRatio {
//     width: number;
//     height: number;
//     auto: boolean;
// }

// export interface Sizing {
//     aspectRatio: AspectRatio;
//     maxHeight: number | 'screen' | null;
//     maxHeightScreen: boolean;
//     paddingY: number;
//     paddingX: number;
//     slidesGap: number;
// }

// export interface ViewportSettings {
//     slidesPerPage: number;
// }

// export interface ViewportNavigation {
//     size: 'sm' | 'md';
//     gap: number;
// }

// export interface ViewportArrows {
//     iconSize: number;
// }

// export interface SliderSettingsViewport {
//     settings: ViewportSettings;
//     navigation: ViewportNavigation;
//     arrows: ViewportArrows;
//     sizing: Sizing;
// }

// export interface SliderSettingsViewports {
//     source: 'static';
//     value: {
//         mobile: SliderSettingsViewport;
//         tablet: SliderSettingsViewport;
//         desktop: SliderSettingsViewport;
//     };
// }

// export interface SliderSettings {
//     elysiumSlideCollection: ElysiumSlideCollection;
//     content: Content;
//     settings: Settings;
//     navigation: Navigation;
//     arrows: Arrows;
//     viewports: SliderSettingsViewports;
// }

// export const sliderSettings: SliderSettings = {
//     elysiumSlideCollection: {
//         source: 'static',
//         value: []
//     },
//     content: {
//         source: 'static',
//         value: {
//             title: ''
//         }
//     },
//     settings: {
//         source: 'static',
//         value: {
//             overlay: false,
//             containerWidth: 'content',
//             rewind: true,
//             speed: 300,
//             pauseOnHover: true,
//             autoplay: {
//                 active: true,
//                 interval: 5000,
//                 pauseOnHover: true
//             }
//         }
//     },
//     navigation: {
//         source: 'static',
//         value: {
//             active: true,
//             position: 'below_slider',
//             align: 'center',
//             shape: 'circle',
//             colors: {
//                 default: '',
//                 active: ''
//             }
//         }
//     },
//     arrows: {
//         source: 'static',
//         value: {
//             active: true,
//             icon: {
//                 default: 'arrow-head',
//                 customPrev: '',
//                 customNext: ''
//             },
//             colors: {
//                 default: '',
//                 active: ''
//             },
//             bgColors: {
//                 default: '',
//                 active: ''
//             },
//             position: 'in_slider'
//         }
//     },
//     viewports: {
//         source: 'static',
//         value: {
//             mobile: {
//                 settings: {
//                     slidesPerPage: 1
//                 },
//                 navigation: {
//                     size: 'sm',
//                     gap: 16
//                 },
//                 arrows: {
//                     iconSize: 16
//                 },
//                 sizing: {
//                     aspectRatio: {
//                         width: 1,
//                         height: 1,
//                         auto: false
//                     },
//                     maxHeight: null,
//                     maxHeightScreen: false,
//                     paddingY: 40,
//                     paddingX: 40,
//                     slidesGap: 0
//                 }
//             },
//             tablet: {
//                 settings: {
//                     slidesPerPage: 1
//                 },
//                 navigation: {
//                     size: 'sm',
//                     gap: 20
//                 },
//                 arrows: {
//                     iconSize: 20
//                 },
//                 sizing: {
//                     aspectRatio: {
//                         width: 4,
//                         height: 3,
//                         auto: false
//                     },
//                     maxHeight: null,
//                     maxHeightScreen: false,
//                     paddingY: 64,
//                     paddingX: 64,
//                     slidesGap: 0
//                 }
//             },
//             desktop: {
//                 settings: {
//                     slidesPerPage: 1
//                 },
//                 navigation: {
//                     size: 'md',
//                     gap: 24
//                 },
//                 arrows: {
//                     iconSize: 24
//                 },
//                 sizing: {
//                     aspectRatio: {
//                         width: 16,
//                         height: 9,
//                         auto: false
//                     },
//                     maxHeight: null,
//                     maxHeightScreen: false,
//                     paddingY: 64,
//                     paddingX: 80,
//                     slidesGap: 0
//                 }
//             }
//         }
//     }
// };

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