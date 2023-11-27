Shopware.Service('privileges').addPrivilegeMappingEntry({
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
                'blur_elysium_slides:create',
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