import template from './template.html.twig';
import { slides } from "@elysiumSlider/utilities/identifiers";

export default {
    template,

    inject: [
        'repositoryFactory'
    ],

    props: {
        slideId: {
            type: String,
            required: true
        }
    },

    computed: {
        repository() {
            return this.repositoryFactory.create( 'blur_elysium_slides' );
        },

        positionIdentifiers() {
            return slides
        }
    },

    methods: {
        onCloseModal() {
            this.$emit('modal-close')
        },

        onDelete() {
            this.repository.delete( this.slideId, Shopware.Context.api ).then(() => {
                this.$emit('delete-finish')
            }).catch((error) => {
                console.error(error)
                this.$emit('delete-error')
            });
        }
    },

    beforeDestroy() {
        this.$emit('modal-close')
    }
}