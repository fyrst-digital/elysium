import enGB from './snippet/en-GB.json';
import './component/blur-elysium-slides-basic-form'; // Elysium Slide Basic Form
import './page/blur-elysium-slides-list'; // List page component
import './page/blur-elysium-slides-create'; // Create page component
import './page/blur-elysium-slides-detail'; // Detail page component
import './acl'; // ACL Privilages

const { Application } = Shopware;

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
    color: '#ff3d58',
    icon: 'default-tools-ruler-pencil',
    favicon: 'icon-module-content.png',
    entity: 'blur_elysium_slides',

    routes: {

        index: {
            components: {
                default: 'blur-elysium-slides-list'
            },
            path: 'index'
        },

        create: {
            component: 'blur-elysium-slides-create',
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
            }
        },
            /**
     * Importaant for further use
        list: {
            component: 'blur-elysium-slides-list',
            path: 'list'
        },

        detail: {
            component: 'swag-example-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'swag.example.list'
            }
        },
        create: {
            component: 'swag-example-create',
            path: 'create',
            meta: {
                parentPath: 'swag.example.list'
            }
        }
            */
    },


    navigation: [{
        id: 'blur-elysium-slides',
        label: 'BlurElysiumSlides.entityLabel',
        color: '#ff3d58',
        path: 'blur.elysium.slides.index',
        icon: 'default-shopping-paper-bag-product',
        parent: 'sw-content',
        position: 100
    }]
});