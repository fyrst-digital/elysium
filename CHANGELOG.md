# 4.0.0

## Release Notes
Compatibility update for Shopware 6.7

## Changelog
- Complete migration from vuex to pinia and to meteor components in the admin 
- Adjustments to Vite configuration
- Code clean-up, bug fixes, and optimizations

# 3.7.1

## Changelog
- A bug in the admin was fixed where the content element tab in the slide settings could not be opened

# 3.7.0

## Changelog
- A bug in the slide selection of the slider cms element has been fixed. Slides can now be added and removed again
- Code adjustments and optimizations
    - The switch from vuex to Pinia has been fully implemented
    - Optimization of the slide selection and device switch components

# 3.6.3

## Changelog
- Lazy loading of section styles has been removed and replaced with static styling via SCSS. There should no longer be any display errors in sections.
- A display error in the Admin UI has been fixed. Context menus in the Slide Builder description editor were previously cut off; all menus should now be fully accessible.

# 3.6.2

## Changelog
- Errors in the slide editing have been fixed. All slide settings are visible again

# 3.6.1

## Changelog
- Blocks in standard sections can now be selected as usual again

# 3.6.0

## Changelog
- Elysium section has been added. It is available in the Shopping Experiences section selection and extends the Shopping Experience with dynamically scalable blocks, merging block rows, changing the visual block order, and more. All settings can be configured separately for smartphone, tablet, and desktop views.
- Codebase has been cleaned up and optimized.

# 3.5.4

## Changelog
- The values in number input fields are now correctly applied. The corresponding components have been adjusted and should function as expected starting with Shopware 6.6.4
- Adjustment of translations

# 3.5.3

## Changelog
- A CSS rule that could negatively impact the Slider behavior has been removed

# 3.5.2

## Changelog
- Change elysium-slider js registration from async to static so the Slider should work as expected from Shopware 6.6.7 onwards

# 3.5.1

## Changelog
- With the value `0`, the maximum limit (width/height) in device-dependent settings can be reset.
- Manually build the latest JS files for the composer package
- Translations in the administration have been corrected

# 3.5.0

## Changelog
- **Change to a Mobile First approach for device-dependent settings.** These settings are now optional and inherit the value from the smaller device view (Mobile First approach). For example, if a setting is only configured in the mobile view, it will be applied to tablet and desktop views as well. This applies to the settings of the slides and the Shopping Experience element slider and banner.
- **Adjustment and optimization of the admin UI.** The admin UI for the Elysium components (slides, slider, and banner settings) has been revised.  
The icon in device-dependent inputs can now be clicked to switch between views.  
The input fields in all settings have been made more compact and clearer to enable more effective editing.
- A lazy loading option has been added to the banner element.
- Equalize different slide heights in Slider element.
- Add outline button variants to slide linking settings
- Add button size option to slide linking settings
- Improve Slider loading behavior to reduce Cumulative Layout Shift an Slide popup effect

# 3.4.1

## Changelog
- A bug in the Slide settings (display) "Display elements one below the other" has been fixed. The option can now be selected correctly again and works as expected.
- CSS styles were added to the focus image element to prevent overlap with the container.

# 3.4.0

## Changelog
- The product image of a slide can now be hidden for the linking type 'Product'
- A maximum height can now be set in the CMS banner element

# 3.3.0

## Changelog
- Change: Due to a changes in the State Manager as of Shopware 6.6.4, errors occurred when inserting Elysium blocks in the Shopping Experiences Layout Editor. This has been adjusted and the insertion of blocks should work as expected.

# 3.2.1

## Changelog
- Change: The SQL syntax of database migration 1707906587 has been changed to support older MySQL and MariaDB versions. **IMPORTANT NOTE** Starting with version 4, the database versions recommended by Shopware are supported without exception

# 3.2.0

## Changelog
- Improvement: The information in which Elysium Slide a medium is used is now displayed in the media management. When deleting a linked medium, a corresponding message appears
- Improvement: Role permissions have been added in the Elysium Slides module
- Improvement: In the slide settings, the option **Stretch image to full width** has been added to the image element
- Change: The **Auto playback interval** setting in the CMS slider now has a minimum value of 200 instead of 3000
- Change: The focus image is now displayed in automatic width by default instead of full width
- Bugfix: HTML tags i, u, b, strong, br and span are now displayed as expected in the frontend
- Bugfix: Correct CSS class names in CMS blocks. This results in styling fixes
- Bugfix: Correct the aspect ratio. When the content of the slide exceeds the aspect ratio, the height of the slide adjusts to fit the content. This means that the content is no longer cut off.
- Bugfix: Text snippets in the administration have been corrected

# 3.1.1

## Changelog
- Bugfix: The UI icons in the administration have been adjusted. These are also displayed correctly again from Shopware 6.6.2.

# 3.1.0

## Changelog
- Improvement: When saving a slide, the cache of all Shopping Experience layouts that have an Elysium element assigned to them is now invalidated. This means that changes to the slide are immediately visible in the storefront without having to delete the cache
- Bugfix: Prevent breaking overflow in cms two-col block 

# 3.0.1

## Changelog
- Bugfix: The slide description is now saved as expected
- Bugfix: Correction of text snippets in the administration

# 3.0.0

## Update Notes
This update provides compatibility with Shopware 6.6. Plugin support changes with this version. Version 3 contains feature enhancements and bug fixes. Version 2 receives bug fixes only. Version 1 is no longer supported and will not receive further updates.

All code within the administration has been adapted. We have minimized the code and focused on improving performance and user experience. 

## Changelog
- Improvement: Update and customization of the administration components
- Improvement: The JavaScript code of the sliders in the storefront is now loaded dynamically

# 2.1.0

## Changelog

- Feature: A post update conversion of the slide and slider settings has been added. When updating from version 1.5 to 2.1, slide and slider settings are automatically applied. **Note**: Data from versions lower than 1.5 will **not be converted**. We also strongly recommend **creating a database backup** before the update
- Bugfix: Errors in the slide template have been fixed and the general styling has been optimized

# 2.0.0

## Important note
The **2.0 update contains critical changes**. Please test the update from version 1.x to 2.0 in a staging environment to avoid permanent data loss.
This release contains deep structural changes. These changes were unavoidable to ensure efficient and future-proof development of the Elysium extension.

## Update Notes

### Banner element for Shopping Expierence layout was added  
Slides can now be displayed individually in a Banner element. Two additional Block elements have also been added to the Shopping Expierence layout. These can be found in the new block category **Elysium Slider and Banner**.  

The **Elysium Banner** block is designed to display a single banner.  
The **Elysium Block - 2 Columns** is designed to display two banner elements. However, other Shopping Expierence elements can also be used in this block. This block offers extended display options for smartphone, tablet, and desktop views, which can be accessed in the sidebar of the Shopping Experiences layout designer.  

### Extending the configuration of Elysium Slides
The configuration of Elysium Slides has been restructured and expanded. New display options have been added, and a focus image can now be used. The focus image is displayed next to the content area detached from the Slide cover.  

The Slide cover images have also been improved, with different images available for smartphone, tablet, or desktop view.  
It is now possible to link a product in addition to an individual link. The product's name, description, and image are automatically displayed. However, the Slide can overwrite this information by inserting the slide headline or focus image for example.

### Consistent settings for smartphone, tablet and desktop view
The settings for slides, slider, and banner elements now have a unified configuration for smartphone, tablet, and desktop views. Each device icon represents a specific view. By clicking on a device icon, the configuration can be specially optimized for this view. Device-dependent settings are indicated below each option.  
Users can also adjust device sizes, determining which view is used based on screen width. To set device sizes, go to **Settings → Extensions → Elysium Slider**.

### Improvement of slide templates and styles
The template structure and CSS styles of slides have been revised and organized more logically. If you are using your own templates, please check them for appropriate changes.

## Changelog
- Feature: Banner element for Shopping Expierence layout was added
- Feature: Shopping Expierence block 'Elysium Banner' was added
- Feature: Shopping Expierence blockk 'Elysium Block — 2 Columns' was added
- Feature: Device-dependent settings have been added to the 'Slider' and 'Banner' elements
- Feature: Device-dependent settings have been added to the slide settings
- Feature: Slides can now be copied
- Feature: Slides can be added a 'focus image'
- Feature: Various slide cover images can be added for the smartphone, tablet and desktop view
- Feature: A large number of slide settings are now device-dependent
- Improvement: Slide settings have been greatly expanded
- Improvement: Optimization of slide cover thumbnails in the frontend (improvement of the Lighthouse performance rating)
- Improvement: Deleting a slide is now also possible on the detail page
- Changes: The Elysium Experience blocks can now be found in the block category 'Elysium Slider and Banner'
- Changes: The slide detail page has been restructured. This mainly affects the code quality. The form for media has been moved to a separate tab. The additional field settings can now be found in the "Advanced" tab
- Changes: Slide templates and styles have been restructured

# 1.5.6 

## Changelog
- Bugfix: An error in the slide selection of the shopping experience slider element has been fixed. With empy slide headlines, no slides could be selected and the slide selection was not displayed. Now the entire slide selection should work as expected even if slide headlines are empty.

# 1.5.5 

## Changelog
- Feature: It is now possible to display several slides per page. Previously, the view was limited to one slide per page. The **Slide behavior** settings can be found under **Sizes** in the Shopping Experiences Slider element. You can specify how many slides should be displayed per page.

# 1.5.4 

## Changelog
- Bugfix: An error in the slide selection of the shopping experience slider element has been fixed. With different languages, no slides could be selected and the slide selection was not displayed. Now the entire slide selection should work as expected in every selected language.

# 1.5.3 

## Changelog
- Feature: The inner container width of the content can now be set in the CMS Slider element. Possible options are "Page content width" or "Full width".

# 1.5.2 

## Changelog
- Fix: Translations in the admin have been corrected
- Improvement: The display of the slider has been optimized. In the slider settings there is now the possibility to configure the padding
- Improvement: The slide selection in the admin has been optimized. The drag and drop function of the slides is now better recognizable

# 1.5.1

## Changelog
- Fixed a bug where the slider was displayed incorrectly

# 1.5.0

## Update Notes

**Changes and extension of the shopping worlds slider element settings**  
Besides bug fixes, this update relates to the settings of the shopping worlds slider element.  We have made an adjustment to the admin interface and options has been added.

**Important note**
These adjustments also result in changes to the data structure of the shopping worlds slider element. **[Please read our update notes](https://elysium-slider.blurcreative.de/documentation/update-notes#version-1-5-0)** for version 1.5.0 before updating the extension.

## Changelog
- Feature: It is now possible to assign a slider heading
- Feature: New settings has been added for the shopping worlds slider element
- Change: The interface of the configuration of the shopping worlds slider element has been adjusted

# 1.4.5

## Changelog
- Bugfix: A code dump in the template was removed

# 1.4.4

## Changelog
- Bugfix: ignore case sensitive on file extension of slide-cover media

# 1.4.3

## Changelog
- Chore: Version compatibility to Shopware 6.5.0

# 1.4.2

## Changelog
- Bugfix: The display of the slide cover background images now works again as expected

# 1.4.1

## Changelog
- Chore: The slider-overlay option is now inactive by default

# 1.4.0

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

# 1.3.1

## Changelog
- Feature: Slide headline accepts br, i, u, b, strong and span HTML tags (#50)
- Bugfix: correct title attribute in url overlay template (#51 - thanks to Alexander Pankow)
- Bugfix: set text-indent to absolute value in url overlay template (#51 - thanks to Alexander Pankow)

# 1.3.0

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