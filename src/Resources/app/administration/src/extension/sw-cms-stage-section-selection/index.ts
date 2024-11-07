import template from './template.html.twig'
import './style.scss'
import { computed } from 'vue'

const { Component } = Shopware

export default Component.wrapComponentConfig({
    template,

    computed: {
        blurElysiumSectionName() {
            return 'blur-elysium-section'
        }
    }
})