import { useComponentRegister } from '@elysium/composables/components'
import { defineBlockConfig } from '@elysium/component/cms/blocks/config'
import type { ComponentConfig } from 'src/core/factory/async-component.factory'

const { Service } = Shopware;

useComponentRegister([
    { name: 'sw-cms-block-blur-elysium-banner', path: () => import('@elysium/component/cms/blocks/blur-elysium-banner/component') as Promise<ComponentConfig> },
    { name: 'sw-cms-block-blur-elysium-banner-preview', path: () => import('@elysium/component/cms/blocks/blur-elysium-banner/preview') as Promise<ComponentConfig> },
]);

Service('cmsService').registerCmsBlock({
    name: 'blur-elysium-banner',
    category: 'blur-elysium-blocks',
    label: 'blurElysiumBanner.label',
    component: 'sw-cms-block-blur-elysium-banner',
    previewComponent: 'sw-cms-block-blur-elysium-banner-preview',
    defaultConfig: defineBlockConfig(),
    slots: {
        main: 'blur-elysium-banner',
    },
});
