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
