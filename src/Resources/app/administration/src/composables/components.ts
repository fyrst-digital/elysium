const { Component } = Shopware

interface ComponentCollection {
    name: string
    path: any
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