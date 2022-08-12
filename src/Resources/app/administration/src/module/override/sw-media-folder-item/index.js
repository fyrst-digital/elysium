import template from './sw-media-folder-item.html.twig';
import './style.scss';

const { Component, Context } = Shopware;
const { Criteria } = Shopware.Data;

Shopware.Component.override( 'sw-media-folder-item', {
    template,

    data() {
        return { associatedEntity: null }
    },

    created() {
        /*
        console.log(this.mediaFolder.name)
        console.log(this.mediaFolder.defaultFolderId)
        console.log(this.mediaFolder.id)

        console.dir(this)
        console.dir(await this.getEntityName().entity)
        */
        // this.mediaFolderRepository

        const test = this.getEntityName()
        console.dir(this)
    }, 

    methods: {
        async getEntityName() {
            this.mediaDefaultFolderRepository.get( this.mediaFolder.defaultFolderId, Context.api).then((result) => {
                this.associatedEntity = result ? result.entity : null
            });
        }
    }
});