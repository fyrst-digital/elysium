import template from './template.html.twig';
import { slides } from "@elysiumSlider/utilities/identifiers";

const { Component, Mixin } = Shopware;
const { mapMutations, mapState } = Component.getComponentHelper();

export default {
    template,

    mixins: [
        Mixin.getByName('blur-editable')
    ],

    computed: {
        ...mapState('blurElysiumSlidesDetail', [
            'slide',
            'loading',
            'acl'
        ]),

        positionIdentifiers() {
            return slides
        }
    },

    created() {
        console.log(this.editable)
    },

    methods: {
        ...mapMutations('blurElysiumSlidesDetail', [
            'setSlideSetting',
        ]),
    }
}