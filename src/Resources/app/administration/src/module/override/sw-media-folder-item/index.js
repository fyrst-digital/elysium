import template from './sw-media-folder-item.html.twig';
import './style.scss';

const { Component, Context } = Shopware;
const { Criteria } = Shopware.Data;

Shopware.Component.override( 'sw-media-folder-item', {
    template,

    data() {
        return { associatedEntity: null }
    },

    methods: {
        async getEntityName() {
            this.mediaDefaultFolderRepository.get( this.mediaFolder.defaultFolderId, Context.api).then((result) => {
                this.associatedEntity = result ? result.entity : null
            });
        }
    }
});