import template from './template.html.twig'

const { Component } = Shopware

export default Component.wrapComponentConfig({
    template,

    inject: [
        'acl'
    ],

    computed: {
        parentRoute () {
            return this.$route.meta.parentPath ?? null
        },

        permissionEdit() {
            return this.acl.can('blur_elysium_slides.editor')
        }
    },

    methods: {
        async onSave () {
            if (this.permissionEdit) {
                // @ts-ignore
                this.$refs.systemConfig.saveAll()
            }
        }
    },

    created() {
        console.log(this.permissionEdit)
    },
})
