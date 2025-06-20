import template from './template.html.twig';
import { blockSettings } from './config';
import './style.scss';

const { Component, Store, Mixin } = Shopware;

export default Component.wrapComponentConfig({
    template,

    props: ['section'],

    inject: ['cmsService'],

    mixins: [
        Mixin.getByName('cms-state'),
        Mixin.getByName('blur-device-utilities'),
    ],

    data() {
        return {
            draggedBlock: null,
            draggedBlockStartPosX: 0,
            draggedBlockWidth: 0,
        };
    },

    watch: {
        'section.blocks.length': {
            handler() {
                /**
                 * @todo This solution is not optimal, because it iterates over all blocks and can lead to performance issues with a lot of blocks
                 * Better solution would be to add the setting to the block when it is dropped.
                 * For now, this is the easiest and lightweight compromise.
                 */
                this.section.blocks.map((block) => {
                    if (block.customFields === null) {
                        block.customFields = {
                            elysiumBlockSettings:
                                structuredClone(blockSettings),
                        };
                    } else if (
                        !block.customFields.hasOwnProperty(
                            'elysiumBlockSettings'
                        )
                    ) {
                        block.customFields.elysiumBlockSettings =
                            structuredClone(blockSettings);
                    }
                });
            },
        },
    },

    computed: {
        cmsPage() {
            return Store.get('cmsPage');
        },

        device() {
            if (this.cmsPage.currentCmsDeviceView === 'tablet-landscape') {
                return 'tablet';
            }

            return this.cmsPage.currentCmsDeviceView;
        },

        gridStyle() {
            const gridStyle: Partial<CSSStyleDeclaration> = {
                display: 'grid',
                gridTemplateColumns: 'repeat(12, minmax(0, 1fr))',
                gap: `${this.viewportsPlaceholder('gridGap', 20)}px`,
                paddingBlock: `${this.viewportsPlaceholder('paddingY', 0)}px`,
                paddingInline: `${this.viewportsPlaceholder('paddingY', 0)}px`,
                alignItems: this.viewportsPlaceholder('alignItems', 'stretch'),
            };

            return gridStyle;
        },
    },

    methods: {
        getDropData(index, sectionPosition = 'main') {
            return { dropIndex: index, section: this.section, sectionPosition };
        },

        dropBlock(block, index) {
            if (this.section.blocks[index].hasOwnProperty('customFields')) {
                this.section.blocks[index].customFields.elysiumBlockSettings =
                    structuredClone(blockSettings);
            } else {
                this.section.blocks[index].customFields = {
                    elysiumBlockSettings: structuredClone(blockSettings),
                };
            }
        },

        setBlockStyles(block) {
            const styles: Partial<CSSStyleDeclaration> = {};
            const viewportsSettings =
                block.customFields?.elysiumBlockSettings?.viewports;

            if (viewportsSettings) {
                styles['--blur-elysium-section-block-col-start'] =
                    this.viewportsPlaceholder(
                        'colStart',
                        'auto',
                        null,
                        viewportsSettings
                    );
                styles['--blur-elysium-section-block-col-end'] =
                    this.viewportsPlaceholder(
                        'colEnd',
                        12,
                        null,
                        viewportsSettings
                    );
                styles['--blur-elysium-section-block-row-start'] =
                    this.viewportsPlaceholder(
                        'rowStart',
                        'auto',
                        null,
                        viewportsSettings
                    );
                styles['--blur-elysium-section-block-row-end'] =
                    this.viewportsPlaceholder(
                        'rowEnd',
                        'auto',
                        null,
                        viewportsSettings
                    );
                styles['--blur-elysium-section-block-order'] =
                    this.viewportsPlaceholder(
                        'order',
                        999,
                        null,
                        viewportsSettings
                    );
            }

            return styles;
        },

        hasBlockErrors(block) {
            return [
                this.hasUniqueBlockErrors(block),
                this.hasSlotConfigErrors(block),
            ].some((error) => !!error);
        },

        hasUniqueBlockErrors(block) {
            const errorElements = this.pageSlotsError?.parameters?.elements;

            if (!errorElements) {
                return false;
            }

            return errorElements.some((errorType) =>
                errorType.blockIds.includes(block.id)
            );
        },

        hasSlotConfigErrors(block) {
            const errorElements =
                this.pageSlotConfigError?.parameters?.elements;

            if (!errorElements) {
                return false;
            }

            return errorElements.some(
                (missingConfig) => missingConfig.blockId === block.id
            );
        },

        onBlockSelection(block) {
            this.cmsPage.setBlock(block);
            this.$emit('on-select-block', block);
        },

        onGridDrop(event) {
            this.draggedBlock.customFields.elysiumBlockSettings.viewports[
                this.device
            ].colEnd = this.calculateDraggedBlockColWidth(event.x);
        },

        startBlockResizeX(event, block) {
            this.draggedBlock = block;
            this.draggedBlockStartPosX = event.x;
            this.draggedBlockWidth = event.target.offsetParent.offsetWidth;
            const img = new Image();
            img.src =
                'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
            event.dataTransfer.setDragImage(img, 0, 0);
        },

        dragBlockResizingX(event) {
            if (
                this.calculateDraggedBlockColWidth(event.x) > 0 &&
                this.calculateDraggedBlockColWidth(event.x) <= 12
            ) {
                this.draggedBlock.customFields.elysiumBlockSettings.viewports[
                    this.device
                ].colEnd = this.calculateDraggedBlockColWidth(event.x);
            }
        },

        endBlockResizeX(event) {
            if (this.calculateDraggedBlockColWidth(event.x) >= 12) {
                this.draggedBlock.customFields.elysiumBlockSettings.viewports[
                    this.device
                ].colEnd = 12;
            }
        },

        calculateDraggedBlockColWidth(currentX) {
            const blockWidth = this.draggedBlockWidth;
            const startX = this.draggedBlockStartPosX;
            const endX = currentX;
            const movedPx = endX - startX;
            const computedStyles = window.getComputedStyle(
                this.$refs.elysiumSectionGrid
            );
            const gridWidth =
                this.$refs.elysiumSectionGrid.offsetWidth -
                parseFloat(computedStyles.getPropertyValue('padding-inline')) *
                    2;

            return Math.round((blockWidth + movedPx) / (gridWidth / 12));
        },

        onAddBlock() {
            this.$emit('on-add-block');
        },
    },

    created() {
        this.viewportsSettings =
            this.section.customFields?.elysiumSectionSettings?.viewports;
    },
});
