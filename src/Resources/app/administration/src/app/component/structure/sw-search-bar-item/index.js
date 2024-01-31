import template from './sw-search-bar-item.html.twig'

// eslint-disable-next-line no-undef
const { Component } = Shopware

Component.override('sw-search-bar-item', {
    template
})
