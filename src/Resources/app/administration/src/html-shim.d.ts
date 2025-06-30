declare module '*.html.twig' {
    const content: string;

    // eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
    export default content;
}

// Only allow raw imports for html files
declare module '*.html?raw' {
    const content: string;

    // eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
    export default content;
}