const { Mixin, Component, Context } = Shopware;

// give the mixin a name and feed it into the register function as the second argument
export default Mixin.register('blur-media', Component.wrapComponentConfig({

    inject: [
        'repositoryFactory'
    ],

    computed: {

        mediaRepository () {
            return this.repositoryFactory.create('media')
        },
    },

    methods: {
        fetchMedia (propertyId: string, property: string) {

            this.mediaRepository.get(
                propertyId,
                Context.api
            ).then((media) => {
                this.setSlideProperty({
                    key: property,
                    value: media
                })
            }).catch((exception) => {
                console.error(exception)
            })
        },

        /**
         * @param property 
         * @param mediaId 
         * @param allowedType 
         */
        setMedia (property: string, mediaId: string) {
            const propertyId = `${property}Id`

            this.setSlideProperty({
                key: propertyId,
                value: mediaId
            })
        },

        /**
         * @param property 
         */
        resetMedia (property: string) {
            const propertyId = `${property}Id`

            this.setSlideProperty({
                key: property,
                value: null
            })
            this.setSlideProperty({
                key: propertyId,
                value: null
            })
        }
    }
}));