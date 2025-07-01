import type { ComponentConfig } from 'src/core/factory/async-component.factory'

const { Component } = Shopware

interface ComponentCollection {
    name: string
    path: () => Promise<ComponentConfig>
}

export function useComponentRegister(components: ComponentCollection[]) {

    components.forEach((component) => {
        Component.register(component.name, component.path)
    })
}

export function useComponentOverride(components: ComponentCollection[]) {

    components.forEach((component) => {
        Component.override(component.name, component.path)
    })
}