import template from './template.html.twig';

const { mapMutations, mapState } = Shopware.Component.getComponentHelper();

export default {
    template,

    computed: {
        ...mapState('blurElysiumSlidesDetail', [
            'slide',
            'loading'
        ]),
    },
};