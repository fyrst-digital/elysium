interface CMSState {
    elementId: string | null;
    elementConfig: Record<string, unknown> | null;
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

        setElementConfig(elementConfig: Record<string, unknown> | null) {
            this.elementConfig = elementConfig;
        },
    },
};
