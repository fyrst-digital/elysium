import template from './template.html.twig'

// eslint-disable-next-line no-undef
const { Component } = Shopware

Component.override('sw-cms-sidebar', {
    template
})
