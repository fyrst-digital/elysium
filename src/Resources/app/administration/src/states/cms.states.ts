/**
 * @todo replace any with proper types
 */
interface CMSState {
    elementId: string | null;
    elementConfig: any | null;
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

        setElementConfig(elementConfig: any | null) {
            this.elementConfig = elementConfig;
        },
    },
};
