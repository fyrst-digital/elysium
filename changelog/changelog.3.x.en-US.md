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

# Version 3.1.1

## Changelog
- Bugfix: The UI icons in the administration have been adjusted. These are also displayed correctly again from Shopware 6.6.2.

# Version 3.1.0

## Changelog
- Improvement: When saving a slide, the cache of all Shopping Experience layouts that have an Elysium element assigned to them is now invalidated. This means that changes to the slide are immediately visible in the storefront without having to delete the cache
- Bugfix: Prevent breaking overflow in cms two-col block 

# Version 3.0.1

## Changelog
- Bugfix: The slide description is now saved as expected
- Bugfix: Correction of text snippets in the administration

# Version 3.0.0

## Update Notes
This update provides compatibility with Shopware 6.6. Plugin support changes with this version. Version 3 contains feature enhancements and bug fixes. Version 2 receives bug fixes only. Version 1 is no longer supported and will not receive further updates.

All code within the administration has been adapted. We have minimized the code and focused on improving performance and user experience. 

## Changelog
- Improvement: Update and customization of the administration components
- Improvement: The JavaScript code of the sliders in the storefront is now loaded dynamically
