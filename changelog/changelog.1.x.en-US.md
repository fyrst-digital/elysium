# `unreleased`

## Changelog
- Änderung: Die Slide Bearbeitungsseite wurde umstrukturiert. Dies betrifft hauptsächlich die Code Qualität. Das Formular für Medien wurde in einen eigenen Tab ausgegliedert. Die Zusatzfelder Einstellungen sind nun im Tab "Erweitert" zu finden.
- Verbesserung: Das Löschen eines Slides ist jetzt auch auf der Bearbeitungsseite möglich.

# `1.5.6` 

## Changelog
- Bugfix: An error in the slide selection of the shopping experience slider element has been fixed. With empy slide headlines, no slides could be selected and the slide selection was not displayed. Now the entire slide selection should work as expected even if slide headlines are empty.

# `1.5.5` 

## Changelog
- Feature: It is now possible to display several slides per page. Previously, the view was limited to one slide per page. The **Slide behavior** settings can be found under **Sizes** in the Shopping Experiences Slider element. You can specify how many slides should be displayed per page.

# `1.5.4` 

## Changelog
- Bugfix: An error in the slide selection of the shopping experience slider element has been fixed. With different languages, no slides could be selected and the slide selection was not displayed. Now the entire slide selection should work as expected in every selected language.

# `1.5.3` 

## Changelog
- Feature: The inner container width of the content can now be set in the CMS Slider element. Possible options are "Page content width" or "Full width".

# `1.5.2` 

## Changelog
- Fix: Translations in the admin have been corrected
- Improvement: The display of the slider has been optimized. In the slider settings there is now the possibility to configure the padding
- Improvement: The slide selection in the admin has been optimized. The drag and drop function of the slides is now better recognizable

# `1.5.1` 2023-06-15

## Changelog
- Fixed a bug where the slider was displayed incorrectly

# `1.5.0` 2023-06-15

## Update Notes

**Changes and extension of the shopping worlds slider element settings**  
Besides bug fixes, this update relates to the settings of the shopping worlds slider element.  We have made an adjustment to the admin interface and options has been added.

**Important note**
These adjustments also result in changes to the data structure of the shopping worlds slider element. **[Please read our update notes](https://elysium-slider.blurcreative.de/documentation/update-notes#version-1-5-0)** for version 1.5.0 before updating the extension.

## Changelog
- Feature: It is now possible to assign a slider heading
- Feature: New settings has been added for the shopping worlds slider element
- Change: The interface of the configuration of the shopping worlds slider element has been adjusted

# `1.4.5` 2023-05-21

## Changelog
- Bugfix: A code dump in the template was removed

# `1.4.4` 2023-05-19

## Changelog
- Bugfix: ignore case sensitive on file extension of slide-cover media

# `1.4.3` 2023-05-19

## Changelog
- Chore: Version compatibility to Shopware 6.5.0

# `1.4.2` 2023-04-19

## Changelog
- Bugfix: The display of the slide cover background images now works again as expected

# `1.4.1` 2023-03-26

## Changelog
- Chore: The slider-overlay option is now inactive by default

# `1.4.0` 2023-03-25

## Update Notes

**Note for developers**  
The slide template has been refractored. Templates for individual slide components are now located under `storefront/component/blur-elysium-slide/`.  
The template for the main CMS element is still located under `storefront/element/cms-element-blur-elysium-slider.html.twig`.

## Changelog
- Feature: In the slide settings there is now the "Advanced" tab, this will contain advanced settings of a slide
- Feature: An individual Twig template file can be defined per slide. This is located in the "Advanced" tab of the slide settings (#44)
- Improvement: Optimisation of the slide selection view in the Elysium Slider CMS element (#55)
- Improvement: Optimization of ACL roles for admin users (#69)
- Chore: The slide template has been refractored
- Bugfix: The 'no slides available' dialogue in the Elysium Slider CMS element now appears as expected (#53)
- Bugfix: Fix wrong thumbnail order in frontend (#57)
- Bugfix: The slide button is now hidden when the URL overlay option is active (#63)

# `1.3.1` 2023-02-21

## Changelog
- Feature: Slide headline accepts br, i, u, b, strong and span HTML tags (#50)
- Bugfix: correct title attribute in url overlay template (#51 - thanks to Alexander Pankow)
- Bugfix: set text-indent to absolute value in url overlay template (#51 - thanks to Alexander Pankow)

# `1.3.0` 2022-12-12

## Update Notes

**New slide selection in CMS element**  

The slide selection in Elysium Slider CMS element has been revised. The aim is that shop managers can maintain and arrange the slides faster and more effectively. 
So there is an overview of the selected slides, where slides can be repositioned, edited or deleted. The user experience has also been improved with helpful dialogues and hints in the slide selection.  

**Video support of slide covers**  

Videos can now also be linked and uploaded as slide cover. For the time being, only .mp4 or .webm videos are displayed. As slide cover for portraits, as before, only images can be linked. This display is ignored as soon as a video is linked as slide cover.  

**Important note**  

If slides without HTML element or text color for Headlines were initially saved, they could not be saved afterwards. This error has been fixed.  
However, this can lead to the removal of information that has already been maintained (only affects HTML element or text color of the headline property) in created slides.  
**Therefore, these details should be checked in already created slides**

## Changelog
- Feature: New slide selection in CMS element (#11)
- Feature: Video support of slide covers (#9)
- Bugfix: Escaping CSS functions in `Resources/app/storefront/src/scss/_elysium-slider.scss 115:26` (#40)
- Bugfix: Double Quotes background-image inline CSS in `Resources/views/storefront/element/blur-elysium-slide-media.html.twig` added (#41)
- Bugfix: Addes Context menu actions media sidebar (#43)
- Bugfix: Its now possible to save HTML element and textcolor of headline property afterwards (#49)