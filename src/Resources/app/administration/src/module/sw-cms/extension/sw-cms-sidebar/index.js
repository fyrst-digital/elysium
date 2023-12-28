import template from './template.html.twig'

/* eslint no-undef: 'off' */
const { Component } = Shopware

Component.override('sw-cms-sidebar', {
    template
})
