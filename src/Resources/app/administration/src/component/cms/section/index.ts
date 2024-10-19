import template from './template.html.twig'

const { Component, State, Mixin } = Shopware

export default Component.wrapComponentConfig({
    template,

    props: ['section'],

    inject: [
        'cmsService',
    ],

    mixins: [
        Mixin.getByName('cms-state'),
    ],

    computed: {

        cmsPageState () {
            return State.get('cmsPageState')
        },

        gridStyle() {
            const gridStyle: Partial<CSSStyleDeclaration> = {
                display: 'grid',
                gridTemplateColumns: 'repeat(12, minmax(0, 1fr))',
                gap: '24px',
                alignItems: 'stretch',
            }

            return gridStyle
        }
    },

    methods: {
        getDropData (index, sectionPosition = 'main') {
            console.log('getDropData', index, sectionPosition)
            return { dropIndex: index, section: this.section, sectionPosition };
        },
        
        dropBlock (block, index) {
            this.section.blocks[index].customFields = {
                meddl: 'loide'
            }
            console.log('dropBlock', index, this.section.blocks[index], block)
        },


        hasBlockErrors (block) {
            return [
                this.hasUniqueBlockErrors(block),
                this.hasSlotConfigErrors(block),
            ].some(error => !!error);
        },

        hasUniqueBlockErrors (block) {
            const errorElements = this.pageSlotsError?.parameters?.elements;

            if (!errorElements) {
                return false;
            }

            return errorElements.some(errorType => errorType.blockIds.includes(block.id));
        },

        hasSlotConfigErrors(block) {
            const errorElements = this.pageSlotConfigError?.parameters?.elements;

            if (!errorElements) {
                return false;
            }

            return errorElements.some(missingConfig => missingConfig.blockId === block.id);
        },

        onBlockSelection(block) {
            Shopware.Store.get('cmsPageState').setBlock(block);
            this.$emit('page-config-open', 'itemConfig');
        },

    },

    created () {
        console.log(this)
    }
})