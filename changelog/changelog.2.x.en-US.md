# Version 2.1.0

## Changelog

- Feature: A post update conversion of the slide and slider settings has been added. When updating from version 1.5 to 2.1, slide and slider settings are automatically applied. **Note**: Data from versions lower than 1.5 will **not be converted**. We also strongly recommend **creating a database backup** before the update
- Bugfix: Errors in the slide template have been fixed and the general styling has been optimized

# Version 2.0.0

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