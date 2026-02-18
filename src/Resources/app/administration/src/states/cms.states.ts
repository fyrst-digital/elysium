interface CMSState {
    elementId: string | null;
    elementConfig: unknown;
}

export default {
    id: 'elysiumCMS',

    state: (): CMSState => ({
        elementId: null,
        elementConfig: null,
    }),

    actions: {
        setElementId(elementId: string | null) {
            this.elementId = elementId;
        },

        setElementConfig(elementConfig: unknown) {
            this.elementConfig = elementConfig;
        },
    },
};
