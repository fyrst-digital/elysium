import template from './template.html.twig'
import './style.scss'

const { Component, State, Mixin } = Shopware

const elysiumBlockSettings = {
    viewports: {
        mobile: {
            colStart: 'auto',
            colEnd: 12,
            rowStart: 'auto',
            rowEnd: 'auto',
            order: null
        },
        tablet: {
            colStart: 'auto',
            colEnd: 6,
            rowStart: 'auto',
            rowEnd: 'auto',
            order: null
        },
        desktop: {
            colStart: 'auto',
            colEnd: 3,
            rowStart: 'auto',
            rowEnd: 'auto',
            order: null
        },
    }
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
        },

        $refs: {
            handler() {
                console.log('refs', this.$refs)
            },
            deep: true,
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
            console.log('gridStyle', this.getViewportSetting(this.section.customFields?.elysiumSectionSettings, 'gridGap'))
            const gridStyle: Partial<CSSStyleDeclaration> = {
                display: 'grid',
                gridTemplateColumns: 'repeat(12, minmax(0, 1fr))',
                gap: `${this.getViewportSetting(this.section.customFields?.elysiumSectionSettings, 'gridGap')}px`,
                paddingBlock: `${this.getViewportSetting(this.section.customFields?.elysiumSectionSettings, 'paddingY')}px`,
                paddingInline: `${this.getViewportSetting(this.section.customFields?.elysiumSectionSettings, 'paddingX')}px`,
                alignItems: this.getViewportSetting(this.section.customFields?.elysiumSectionSettings, 'alignItems'),
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

            if (block.customFields && block.customFields.elysiumBlockSettings?.viewports) {
                styles['--blur-elysium-section-block-col-start'] = this.getViewportSetting(block.customFields.elysiumBlockSettings, 'colStart')
                styles['--blur-elysium-section-block-col-end'] = this.getViewportSetting(block.customFields.elysiumBlockSettings, 'colEnd')
                styles['--blur-elysium-section-block-row-start'] = this.getViewportSetting(block.customFields.elysiumBlockSettings, 'rowStart')
                styles['--blur-elysium-section-block-row-end'] = this.getViewportSetting(block.customFields.elysiumBlockSettings, 'rowEnd')
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
            this.draggedBlock.customFields.elysiumBlockSettings.viewports[this.currentDevice].colEnd = this.calculateDraggedBlockColWidth(event.x)
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
                this.draggedBlock.customFields.elysiumBlockSettings.viewports[this.currentDevice].colEnd = 12
            }
        },

        dragBlockResizingX (event) {
            if ((this.calculateDraggedBlockColWidth(event.x) > 0) && (this.calculateDraggedBlockColWidth(event.x) <= 12)) {
                this.draggedBlock.customFields.elysiumBlockSettings.viewports[this.currentDevice].colEnd = this.calculateDraggedBlockColWidth(event.x)
            }
        },

        calculateDraggedBlockColWidth (currentX) {
            const blockWidth = this.draggedBlockWidth
            const startX = this.draggedBlockStartPosX
            const endX = currentX
            const movedPx = endX - startX

            return Math.round((blockWidth + movedPx) / (this.$refs.elysiumectionGrid.offsetWidth / 12))
        },

        getViewportSetting (settings, property) {
            // return block.customFields.elysiumBlockSettings.viewports[this.currentDevice][property]
            return settings?.viewports[this.currentDevice][property] ?? null
        },

        onAddBlock () {
            this.$emit('on-add-block')
        }
    },

    created () {
        console.log(this.section)
    }
})