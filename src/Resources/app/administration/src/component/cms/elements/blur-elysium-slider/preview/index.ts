import template from './template.html.twig';

const { Component, Filter } = Shopware;

export default Component.wrapComponentConfig({
    template,

    computed: {
        assetFilter() {
            return Filter.getByName('asset');
        },
    },
});
