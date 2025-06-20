const a=`<mt-card position-identifier="blurElysium.slide.form.focusImage" :title="$tc('blurElysiumSlides.forms.slideFocusImage.label')" :subtitle="$tc('blurElysiumSlides.forms.slideFocusImage.description')" :class="['content-no-padding']" > <template #headerRight> <blur-device-switch layout="row" show-label="false" :default-viewport="device" :style="{ padding: '0', width: '100%', justifyContent: 'center', '--container-gap': '12px', '--icon-padding': '6px', '--icon-bg-color': '#ffffff', '--icon-hover-bg-color': 'var(--color-gray-100)', }" :device="device" @change-device="device => elysiumUI.setDevice(device)" /> </template> <blur-section> <blur-column padding :cols="12" :cols-desktop="8" :style="viewStyle({ desktop: { borderRight: '1px solid var(--color-border-primary-default)' } })" > <sw-upload-listener upload-tag="blur-elysium-slide-focus-image" auto-upload @media-upload-finish="setFocusImage" /> <sw-media-upload-v2 variant="regular" label="Medium" :disabled="!(permissionCreate || permissionEdit)" file-accept="image/*" upload-tag="blur-elysium-slide-focus-image" :source="focusImage" :allow-multi-select="false" :default-folder="'blur_elysium_slides'" @media-upload-remove-image="removeFocusImage" @media-upload-sidebar-open="mediaModal.open = true" /> </blur-column> <blur-column padding :cols="12" :cols-desktop="4" > <blur-section gap-y> <blur-column> <mt-switch class="blur-switch-input" @change="slideViewportSettings.image.imageFullWidth = $event" :checked="slideViewportSettings.image.imageFullWidth" :label="$tc('blurElysiumSlides.forms.focusImage.fullWidth')" /> </blur-column> <blur-column> <blur-number-input :label="$tc('blurElysiumSlides.forms.slideWidth.label')" :placeholder="viewportsPlaceholder('image.maxWidth', $t('blurElysium.general.noLimit'))" :show-device="true" :device="device" @on-device="deviceSwitch" :model-value="slideViewportSettings.image.maxWidth" @update-value="value => slideViewportSettings.image.maxWidth = value" /> </blur-column> <blur-column> <blur-select-input :label="$tc('blurElysium.general.jusitifyContent')" :placeholder="viewportsPlaceholder('image.justifyContent', 'center', 'blurElysium.general')" :options="[ { value: 'flex-start', label: $tc('blurElysium.general.jusitifyStart') }, { value: 'center', label: $tc('blurElysium.general.jusitifyCenter') }, { value: 'flex-end', label: $tc('blurElysium.general.jusitifyEnd') } ]" :device="device" :show-device="true" @on-device="deviceSwitch" v-model:value="slideViewportSettings.image.justifyContent" /> </blur-column> </blur-section> </blur-column> </blur-section> <sw-media-modal-v2 v-if="mediaModal.open" :allow-multi-select="false" entity-context="blur_elysium_slides" @media-modal-selection-change="onAddMediaModal" @modal-close="mediaModal.open = false" /> </mt-card>`,{Component:o,Mixin:t,Store:s,Context:d}=Shopware,r=o.wrapComponentConfig({template:a,mixins:[t.getByName("notification"),t.getByName("blur-device-utilities"),t.getByName("blur-style-utilities")],inject:["repositoryFactory","acl"],data(){return{mediaLoading:!1,mediaModal:{open:!1,type:null}}},computed:{elysiumUI(){return s.get("elysiumUI")},slide(){return s.get("elysiumSlide").slide},device(){return this.elysiumUI.device},mediaSidebar(){return this.elysiumUI.mediaSidebar},mediaRepository(){return this.repositoryFactory.create("media")},slideViewportSettings(){return this.slide.slideSettings.viewports[this.device]},focusImage(){return this.slide.presentationMedia?this.slide.presentationMedia:this.slide.presentationMedia||null},permissionView(){return this.acl.can("blur_elysium_slides.viewer")},permissionCreate(){return this.acl.can("blur_elysium_slides.creator")},permissionEdit(){return this.acl.can("blur_elysium_slides.editor")},permissionDelete(){return this.acl.can("blur_elysium_slides.deleter")}},methods:{setFocusImage(e){this.mediaLoading=!0;let i=e.id||e.targetId||null;i===null?(console.error("mediaId is null. Slide cover media can not be set."),this.mediaLoading=!1):(this.slide.presentationMediaId=i,e.path?(this.slide.presentationMedia=e,this.mediaLoading=!1):this.mediaRepository.get(i,d.api).then(l=>{this.slide.presentationMedia=l,this.mediaLoading=!1}).catch(l=>{console.error(l),this.mediaLoading=!1}))},removeFocusImage(){this.slide.presentationMediaId=null,this.slide.presentationMedia=null},onAddMediaModal(e){this.setFocusImage(e[0])}},created(){this.viewportsSettings=this.slide.slideSettings.viewports}});export{r as default};
//# sourceMappingURL=index-roblNq33.js.map
