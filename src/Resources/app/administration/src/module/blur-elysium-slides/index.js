import '@elysiumSlider/module/page/blur-elysium-slides-list'; // List page component
import '@elysiumSlider/module/page/blur-elysium-slides-detail'; // Detail page component
import './acl'; // ACL Privilages

const { Application } = Shopware;

Shopware.Component.register('blur-elysium-slides-basic-form', () => import('@elysiumSlider/module/component/blur-elysium-slides-basic-form'));
Shopware.Component.register('blur-elysium-slides-media-form', () => import('@elysiumSlider/module/component/blur-elysium-slides-media-form'));
Shopware.Component.register('blur-elysium-slides-content-general-appearance', () => import('@elysiumSlider/module/component/blur-elysium-slides-content-general-appearance'));
Shopware.Component.register('blur-elysium-slides-content-container-settings', () => import('@elysiumSlider/module/component/blur-elysium-slides-content-container-settings'));

/**
 * Add Entity `blur_elysium_slides` to search types
 */
Application.addServiceProviderDecorator( 'searchTypeService' , searchTypeService => {
    searchTypeService.upsertType('blur_elysium_slides', {
        entityName: 'blur_elysium_slides',
        placeholderSnippet: 'BlurElysiumSlides.placeholderSearchBar',
        listingRoute: 'blur.elysium.slides.index'
    });

    return searchTypeService;
});

/**
 * Register Module
 */
Shopware.Module.register( 'blur-elysium-slides', {
    type: 'plugin',
    name: 'blur_elysium_slides',
    title: 'BlurElysiumSlides.entityLabel',
    description: 'BlurElysiumSlides.description',
    color: '#e868a1',
    icon: 'regular-sidebar',
    favicon: 'icon-module-content.png',
    entity: 'blur_elysium_slides',

    routes: {

        index: {
            components: {
                default: 'blur-elysium-slides-list'
            },
            path: 'index',
            meta: {
                privilege: 'blur_elysium_slides.viewer'
            }
        },

        create: {
            component: 'blur-elysium-slides-detail',
            path: 'create',
            meta: {
                parentPath: 'blur.elysium.slides.index'
            }
        },

        detail: {
            component: 'blur-elysium-slides-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'blur.elysium.slides.index'
            },
            props: {
                default(route) {
                    return {
                        blurElysiumSlideId: route.params.id,
                    };
                },
            },
            children: {
                base: {
                    component: 'blur-elysium-slides-base',
                    path: 'base',
                    meta: {
                        parentPath: 'blur.elysium.slides.index'
                    },
                }
            }
        }
    },

    navigation: [{
        id: 'blur-elysium-slides',
        label: 'BlurElysiumSlides.entityLabel',
        color: '#ff3d58',
        path: 'blur.elysium.slides.index',
        icon: 'default-shopping-paper-bag-product',
        parent: 'sw-content',
        position: 100,
        privilege: 'blur_elysium_slides.viewer'
    }],
});