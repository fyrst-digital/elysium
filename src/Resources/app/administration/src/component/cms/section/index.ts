import template from './template.html.twig'
import './style.scss'
import { watch } from 'vue'

const { Component, State, Mixin } = Shopware

const elysiumBlockSettings = {
    'mobile': {
        colStart: 'auto',
        colEnd: 12,
        rowStart: 'auto',
        rowEnd: 'auto',
        order: null
    },
    'tablet': {
        colStart: 'auto',
        colEnd: 6,
        rowStart: 'auto',
        rowEnd: 'auto',
        order: null
    },
    'desktop': {
        colStart: 'auto',
        colEnd: 3,
        rowStart: 'auto',
        rowEnd: 'auto',
        order: null
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
            draggedBlock: null,
            draggedBlockStartPosX: 0,
            draggedBlockWidth: 0,
        }
    },

    watch: {
    
        'section.blocks.length': {
            handler() {
                /**
                 * @todo This solution is not optimal, because it iterates over all blocks and can lead to performance issues with a lot of blocks
                 * Better solution would be to add the setting to the block when it is dropped. 
                 * For now, this is the easiest and lightweight compromise.
                 */
                this.section.blocks.map((block, index) => {
                    if (block.customFields === null) {
                        block.customFields = {
                            elysiumBlockSettings: structuredClone(elysiumBlockSettings)
                        }
                    } else if (!block.customFields.hasOwnProperty('elysiumBlockSettings')) {
                        block.customFields.elysiumBlockSettings = structuredClone(elysiumBlockSettings)
                    }
                })
            },
        }
    },

    computed: {

        cmsPageState () {
            return State.get('cmsPageState')
        },

        currentDevice () {

            if (this.cmsPageState.currentCmsDeviceView === 'tablet-landscape') {
                return 'tablet'
            }

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

        onGridDrop (event) {
            this.draggedBlock.customFields.elysiumBlockSettings[this.currentDevice].colEnd = this.calculateDraggedBlockColWidth(event.x)
        },

        startBlockResizeX (event, block, index) {
            this.draggedBlock = block
            this.draggedBlockStartPosX = event.x
            this.draggedBlockWidth = event.target.offsetParent.offsetWidth
            let img = new Image()
            img.src = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'
            event.dataTransfer.setDragImage(img, 0, 0)
        },

        endBlockResizeX (event) {
            if (this.calculateDraggedBlockColWidth(event.x) >= 12) {
                this.draggedBlock.customFields.elysiumBlockSettings[this.currentDevice].colEnd = 12
            }
        },

        dragBlockResizingX (event) {
            if ((this.calculateDraggedBlockColWidth(event.x) > 0) && (this.calculateDraggedBlockColWidth(event.x) <= 12)) {
                this.draggedBlock.customFields.elysiumBlockSettings[this.currentDevice].colEnd = this.calculateDraggedBlockColWidth(event.x)
            }
        },

        calculateDraggedBlockColWidth (currentX) {
            const blockWidth = this.draggedBlockWidth
            const startX = this.draggedBlockStartPosX
            const endX = currentX
            const movedPx = endX - startX

            return Math.round((blockWidth + movedPx) / (this.$refs.elysiumectionGrid.offsetWidth / 12))
        }
    },

    created () {
        console.log(this)
    }
})