import template from './template.html.twig'

const { Component, Filter, Feature } = Shopware

export default Component.wrapComponentConfig({
    template,

    computed: {
        isSectionFeatureActive () {
            return Feature.isActive('BLUR_ELYSIUM_CMS_SECTION') ?? false
        },
        
        assetFilter() {
            return Filter.getByName('asset');
        },
    },
})