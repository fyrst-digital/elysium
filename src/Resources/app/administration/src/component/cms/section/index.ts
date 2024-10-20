import template from './template.html.twig'
import './style.scss'
import { onMounted } from 'vue'
import { watch } from 'vue'

const { Component, State, Mixin } = Shopware

const elysiumBlockSettings = {
    'mobile': {
        colStart: 'auto',
        colEnd: 12,
        rowStart: 'auto',
        rowEnd: 'auto',
    },
    'tablet-landscape': {
        colStart: 'auto',
        colEnd: 6,
        rowStart: 'auto',
        rowEnd: 'auto'
    },
    'desktop': {
        colStart: 'auto',
        colEnd: 3,
        rowStart: 'auto',
        rowEnd: 'auto'
    },
}

export default Component.wrapComponentConfig({
    template,

    props: ['section'],

    inject: [
        'cmsService',
    ],

    mixins: [
        Mixin.getByName('cms-state'),
    ],

    data () {
        return {
            draggedBlock: null
        }
    },

    computed: {

        cmsPageState () {
            return State.get('cmsPageState')
        },

        currentDevice () {
            return this.cmsPageState.currentCmsDeviceView
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

            if (this.section.blocks[index].hasOwnProperty('customFields')) {
                this.section.blocks[index].customFields.elysiumBlockSettings = structuredClone(elysiumBlockSettings)    
            } else {
                this.section.blocks[index].customFields = {
                    elysiumBlockSettings: structuredClone(elysiumBlockSettings)
                }
            }

            console.log('dropBlock', index, this.section.blocks[index], block)
        },

        setBlockStyles (block) {
            const styles: Partial<CSSStyleDeclaration> = {}

            if (block.customFields && block.customFields.elysiumBlockSettings) {
                styles['--blur-elysium-section-block-col-start'] = block.customFields.elysiumBlockSettings[this.currentDevice].colStart
                styles['--blur-elysium-section-block-col-end'] = block.customFields.elysiumBlockSettings[this.currentDevice].colEnd
                styles['--blur-elysium-section-block-row-start'] = block.customFields.elysiumBlockSettings[this.currentDevice].rowStart
                styles['--blur-elysium-section-block-row-end'] = block.customFields.elysiumBlockSettings[this.currentDevice].rowEnd
            }

            return styles;
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

        drop (event) {
            const blockWidth = parseFloat(event.dataTransfer.getData('blockWidth'))
            const startX = parseFloat(event.dataTransfer.getData('startX'))
            const endX = event.x
            const movedPx = endX - startX
            const gridCol = Math.round((blockWidth + movedPx) / (this.$refs.elysiumectionGrid.offsetWidth / 12))

            this.draggedBlock.customFields.elysiumBlockSettings[this.currentDevice].colEnd = gridCol
            console.log('drop', gridCol)
        },

        dragStart (event, block, index) {
            this.draggedBlock = block
            event.dataTransfer.setData('startX', event.x)
            event.dataTransfer.setData('blockWidth', event.target.offsetParent.offsetWidth)        }
    },

    created () {
        console.log(this)
    }
})