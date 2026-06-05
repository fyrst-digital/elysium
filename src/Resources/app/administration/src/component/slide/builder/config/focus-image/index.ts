import template from "./template.html.twig";
import { getFocusImageInheritanceSource } from "@elysium/composables/content-settings-display";
import { Media } from "@elysium/types/slide";

const { Component, Mixin, Store, Context } = Shopware;

export default Component.wrapComponentConfig({
  template,
  mixins: [
    Mixin.getByName("notification"),
    Mixin.getByName("blur-device-utilities"),
    Mixin.getByName("blur-style-utilities"),
  ],

  inject: ["repositoryFactory", "acl"],

  data() {
    return {
      mediaModal: {
        open: false,
        type: null as string | null,
      },
    };
  },

  computed: {
    elysiumUI() {
      return Store.get("elysiumUI");
    },

    elysiumMedia() {
      return Store.get("elysiumMedia");
    },

    slide() {
      return Store.get("elysiumSlide").slide;
    },

    device() {
      return this.elysiumUI.device;
    },

    mediaRepository() {
      return this.repositoryFactory.create("media");
    },

    slideViewportSettings() {
      return this.slide.slideSettings.viewports[this.device];
    },

    focusImageMediaId(): string | null {
      return this.slide.contentSettings?.focusImageId ?? null;
    },

    focusImage(): Media | null {
      const mediaId = this.focusImageMediaId;
      if (!mediaId) return null;
      return this.elysiumMedia.getMedia(mediaId);
    },

    isDefaultLanguage(): boolean {
      const context = Store.get("context");
      return context.api.languageId === context.api.systemLanguageId;
    },

    focusImageInheritanceSource() {
      return getFocusImageInheritanceSource(this.slide, this.isDefaultLanguage);
    },

    permissionView() {
      return this.acl.can("blur_elysium_slides.viewer");
    },

    permissionCreate() {
      return this.acl.can("blur_elysium_slides.creator");
    },

    permissionEdit() {
      return this.acl.can("blur_elysium_slides.editor");
    },

    permissionDelete() {
      return this.acl.can("blur_elysium_slides.deleter");
    },
  },

  methods: {
    setFocusImage(media: Media) {
      const mediaId = media?.id || media?.targetId || null;

      if (mediaId === null) {
        console.error("mediaId is null. Focus image media can not be set.");
        return;
      }

      this.slide.contentSettings.focusImageId = mediaId;

      this.syncContentSettingsToTranslation();

      if (media?.path) {
        this.elysiumMedia.setMedia(mediaId, media);
      } else {
        this.mediaRepository
          .get(mediaId, Context.api)
          .then((loadedMedia: Media) => {
            this.elysiumMedia.setMedia(mediaId, loadedMedia);
          })
          .catch((exception: Error) => {
            console.error(exception);
          });
      }
    },

    removeFocusImage() {
      this.slide.contentSettings.focusImageId = null;
      this.syncContentSettingsToTranslation();
    },

    onAddMediaModal(payload: Media[]) {
      this.setFocusImage(payload[0]);
    },

    closeMediaModal() {
      this.mediaModal.open = false;
    },

    loadMediaForCurrentSlide() {
      const mediaId = this.slide.contentSettings?.focusImageId;
      if (!mediaId) return;

      if (this.elysiumMedia.hasMedia(mediaId)) return;

      const criteria = new Shopware.Data.Criteria();
      criteria.setIds([mediaId]);

      this.mediaRepository
        .search(criteria, Context.api)
        .then((result: Media[]) => {
          result.forEach((media: Media) => {
            this.elysiumMedia.setMedia(media.id, media);
          });
        })
        .catch((exception: Error) => {
          console.error(exception);
        });
    },

    syncContentSettingsToTranslation() {
      const context = Store.get("context");
      const currentLanguageId = context.api.languageId;
      if (!currentLanguageId || !this.slide.translations) {
        return;
      }
      const translations = Array.isArray(this.slide.translations)
        ? this.slide.translations
        : Object.values(this.slide.translations as Record<string, unknown>);
      const currentTranslation = translations.find((t: unknown) => {
        const translation = t as Record<string, unknown>;
        return translation.languageId === currentLanguageId;
      }) as Record<string, unknown> | undefined;
      if (currentTranslation) {
        currentTranslation.contentSettings = this.slide.contentSettings;
      }
    },
  },

  created() {
    this.viewportsSettings = this.slide.slideSettings.viewports;
    this.loadMediaForCurrentSlide();
  },
});
