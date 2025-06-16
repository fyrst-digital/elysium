import defaultSliderSettings from '@elysium/component/cms/elements/blur-elysium-slider/settings';
import SliderConfig from '@elysium/component/cms/elements/blur-elysium-slider/config';
import SliderConfigSettings from '@elysium/component/cms/elements/blur-elysium-slider/config/settings';

const { Component, Service } = Shopware;

Component.register(
    'cms-el-blur-elysium-slider',
    () =>
        import('@elysium/component/cms/elements/blur-elysium-slider/component')
);

Component.register('blur-elysium-slider-config-settings', SliderConfigSettings);

Component.register('cms-el-blur-elysium-slider-config', SliderConfig);

Component.register(
    'cms-el-blur-elysium-slider-preview',
    () => import('@elysium/component/cms/elements/blur-elysium-slider/preview')
);

Component.register(
    'blur-elysium-slider-config-sizing',
    () =>
        import(
            '@elysium/component/cms/elements/blur-elysium-slider/config/sizing'
        )
);
Component.register(
    'blur-elysium-slider-config-navigation',
    () =>
        import(
            '@elysium/component/cms/elements/blur-elysium-slider/config/navigation'
        )
);
Component.register(
    'blur-elysium-slider-config-arrows',
    () =>
        import(
            '@elysium/component/cms/elements/blur-elysium-slider/config/arrows'
        )
);

Service('cmsService').registerCmsElement({
    name: 'blur-elysium-slider',
    label: 'blurElysiumSlider.label',
    component: 'cms-el-blur-elysium-slider',
    configComponent: 'cms-el-blur-elysium-slider-config',
    previewComponent: 'cms-el-blur-elysium-slider-preview',
    defaultConfig: defaultSliderSettings,
});
