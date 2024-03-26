import template from './template.html.twig'

export default {
    template,

    computed: {
        parentRoute () {
            return this.$route.meta.parentPath ?? null
        }
    },

    methods: {
        async onSave () {
            this.$refs.systemConfig.saveAll()
        }
    }
}
