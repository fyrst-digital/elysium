import type { ComponentConfig } from 'src/core/factory/async-component.factory'
import template from './template.html.twig';

const { Component } = Shopware;

export default Component.wrapComponentConfig({
    template,
}) as ComponentConfig;
