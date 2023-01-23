import template from './template.twig';

const { Criteria } = Shopware.Data;
const { Component, Context, Mixin } = Shopware;
const { mapPropertyErrors, mapState } = Shopware.Component.getComponentHelper();

const propErrors = [
    'name'
];

Component.register( 'blur-elysium-slide-selection', {
    template
});