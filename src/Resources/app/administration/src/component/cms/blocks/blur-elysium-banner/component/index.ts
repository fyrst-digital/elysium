import type { ComponentConfig } from 'src/core/factory/async-component.factory'
import template from './template.html.twig';

const { Component, Store } = Shopware;

export default Component.wrapComponentConfig({
    template,
    props: ['block'],
    computed: {
        blockErrors() {
            return Store.get('error').getErrorsForEntity(
                'cms_block',
                this.block.id
            );
        },

        blockClasses() {
            const classes = {
                'cms-block-elysium': true,
                'has-error': Boolean(this.blockErrors)
            }
            return classes
        }
    },
}) as ComponentConfig;
