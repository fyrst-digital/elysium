import { module } from 'blurElysium/meta'

const { Module, Service } = Shopware

Module.register('blur-elysium-slides', {
    type: 'plugin',
    name: 'blurElysiumSlides',
    title: 'blurElysiumSlides.label',
    description: 'blurElysiumSlides.description',
    color: module.color,
    entity: 'blur_elysium_slides',

    /** Search configuration */
    defaultSearchConfiguration: <any>{
        _searchable: true,
        name: {
            _searchable: true,
            _score: 1000,
        },
        title: {
            _searchable: true,
            _score: 800,
        },
    },

    /** Module routes */
    routes: {
        /** Index route */
        overview: {
            path: 'overview',
            component: 'blur-elysium-slides-overview',
            meta: {
                privilege: 'blur_elysium_slides.viewer'
            }
        },

        /** Create slide route */
        create: {
            path: 'create',
            component: 'blur-elysium-slides-detail',
            props: {
                default: () => ({ newSlide: true }),
            },
            meta: {
                parentPath: 'blur.elysium.slides.overview',
                privilege: 'blur_elysium_slides.creator'
            },
            redirect: {
                name: 'blur.elysium.slides.create.content'
            },
            children: {
                content: {
                    component: 'blur-elysium-slides-section-base',
                    path: 'content',
                    meta: {
                        parentPath: 'blur.elysium.slides.overview'
                    }
                },
                media: {
                    component: 'blur-elysium-slides-section-media',
                    path: 'media',
                    meta: {
                        parentPath: 'blur.elysium.slides.overview'
                    }
                },
                display: {
                    component: 'blur-elysium-slides-section-display',
                    path: 'display',
                    meta: {
                        parentPath: 'blur.elysium.slides.overview'
                    }
                },
                advanced: {
                    component: 'blur-elysium-slides-section-advanced',
                    path: 'advanced',
                    meta: {
                        parentPath: 'blur.elysium.slides.overview'
                    }
                },
            }
        },

        /** Detail slide route */
        detail: {
            path: 'detail/:id',
            component: 'blur-elysium-slides-detail',
            props: {
                default (route: any) {
                    return {
                        slideId: route.params.id
                    }
                }
            },
            meta: {
                parentPath: 'blur.elysium.slides.overview',
                privilege: 'blur_elysium_slides.viewer'
            },
            redirect: {
                name: 'blur.elysium.slides.detail.content'
            },
            children: {
                content: {
                    component: 'blur-elysium-slides-section-base',
                    path: 'content',
                    meta: {
                        parentPath: 'blur.elysium.slides.overview'
                    }
                },
                media: {
                    component: 'blur-elysium-slides-section-media',
                    path: 'media',
                    meta: {
                        parentPath: 'blur.elysium.slides.overview'
                    }
                },
                display: {
                    component: 'blur-elysium-slides-section-display',
                    path: 'display',
                    meta: {
                        parentPath: 'blur.elysium.slides.overview'
                    }
                },
                advanced: {
                    component: 'blur-elysium-slides-section-advanced',
                    path: 'advanced',
                    meta: {
                        parentPath: 'blur.elysium.slides.overview'
                    }
                },
            }
        },

        settings: {
            component: 'blur-elysium-settings',
            path: 'settings',
            meta: {
                icon: 'regular-cog',
                parentPath: 'sw.settings.index.plugins',
                privilege: 'blur_elysium_slides.viewer'
            }
        }
    },

    /** Module navigation in shopware menu */
    navigation: [
        {
            id: 'blur-elysium-slides',
            label: 'blurElysiumSlides.label',
            /** @deprecated may be useless */
            // color: '#ff3d58',
            path: 'blur.elysium.slides.overview',
            /** @deprecated may be useless */
            // icon: 'default-shopping-paper-bag-product',
            parent: 'sw-content',
            position: 100,
            privilege: 'blur_elysium_slides.viewer'
        }
    ],

    /** Module navigation in shopware settings */
    settingsItem: [{
        group: 'plugins',
        to: 'blur.elysium.slides.settings',
        iconComponent: 'blur-elysium-icon',
        label: 'blurElysium.settingsLabel',
        privilege: 'blur_elysium_slides.viewer'
    }]
});

Service('privileges')
    .addPrivilegeMappingEntry({
        category: 'permissions',
        parent: 'content',
        key: 'blur_elysium_slides',
        roles: {
            viewer: {
                privileges: [
                    'blur_elysium_slides:read'
                ],
                dependencies: []
            },
            editor: {
                privileges: [
                    'blur_elysium_slides:update'
                ],
                dependencies: [
                    'blur_elysium_slides.viewer'
                ]
            },
            creator: {
                privileges: [
                    'blur_elysium_slides:create'
                ],
                dependencies: [
                    'blur_elysium_slides.viewer',
                    'blur_elysium_slides.editor'
                ]
            },
            deleter: {
                privileges: [
                    'blur_elysium_slides:delete'
                ],
                dependencies: [
                    'blur_elysium_slides.viewer',
                    'blur_elysium_slides.editor'
                ]
            }
        }
    });