import template from './template.html.twig';
import { slides } from "@elysiumSlider/utilities/identifiers";

const { mapMutations, mapState } = Shopware.Component.getComponentHelper();

export default {
    template,

    computed: {
        ...mapState('blurElysiumSlidesDetail', [
            'slide',
            'loading'
        ]),

        positionIdentifiers() {
            return slides
        },
    },

    methods: {
        ...mapMutations('blurElysiumSlidesDetail', [
            'setSlideSetting',
        ]),
    }
};