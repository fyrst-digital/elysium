import template from './template.html.twig'

export default {
    template,

    computed: {
        parentRoute () {
            return this.$route.meta.parentPath ?? null
        }
    },

    created () {
        console.log(this)
    },

    methods: {
        async onSave() {
            this.$refs.systemConfig.saveAll();
        }
    }
}