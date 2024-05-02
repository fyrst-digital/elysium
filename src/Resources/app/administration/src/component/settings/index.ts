import template from './template.html.twig'

const { Component } = Shopware

export default Component.wrapComponentConfig({
    template,

    computed: {
        parentRoute () {
            return this.$route.meta.parentPath ?? null
        }
    },

    methods: {
        async onSave () {
            // @ts-ignore
            this.$refs.systemConfig.saveAll()
        }
    }
})
