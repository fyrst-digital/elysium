import "./component";
import "./preview";

Shopware.Service( 'cmsService' ).registerCmsBlock({
    name: 'blur-elysium-slider-block',
    category: 'text-image',
    label: 'Elysium Slider',
    component: 'sw-cms-block-blur-elysium-slider-block',
    previewComponent: 'sw-cms-preview-blur-elysium-slider-block',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed'
    },
    slots: {
        main: 'blur-elysium-slider'
    }
});