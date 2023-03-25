Shopware.Service('privileges').addPrivilegeMappingEntry({
    category: 'permissions',
    parent: 'content',
    key: 'blur_elysium_slides',
    roles: {
        viewer: {
            privileges: ['blur_elysium_slides:read'],
            dependencies: []
        },
        editor: {
            privileges: [],
            dependencies: []
        },
        creator: {
            privileges: ['blur_elysium_slides:create','blur_elysium_slides:update'],
            dependencies: ['blur_elysium_slides:create']
        },
        deleter: {
            privileges: [],
            dependencies: []
        }
    }
});