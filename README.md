# Elysium

A powerful extension that helps you create stunning sliders, banners, and page layouts for your Shopware store.

## What is Elysium?

Elysium transforms how you design your online store. Instead of wrestling with complex code or hiring developers, you get intuitive tools to build beautiful, responsive content that looks great on any device. Whether you need eye-catching promotional banners, smooth-sliding product carousels, or flexible page layouts - this extension makes it possible right from Shopware's Shopping Experience editor.

## Key Features

- **Dynamic Sliders** - Create smooth, animated carousels with custom slides, navigation controls, and automatic playback
- **Banner Displays** - Showcase hero images, promotions, or announcements with single-slide displays optimized for impact
- **Flexible Layouts** - Build custom page sections with drag-and-drop column widths and visual row controls
- **Slide Builder** - Design reusable slides with headlines, descriptions, and call-to-action buttons - all in one place
- **Product Linking** - Connect slides directly to your products and automatically display their images, names, and details
- **Video Support** - Use videos as slide backgrounds to create engaging, modern experiences
- **Mobile-First Design** - Your content automatically adapts to look perfect on phones, tablets, and desktop computers
- **SEO Optimized** - Built with search engines in mind, helping your content get discovered
- **Accessible** - Designed to work well with screen readers and assistive technologies

## Getting Started

Once installed, Elysium adds new elements to your Shopping Experience editor. Simply create a new shopping experience page or edit an existing one, then drag the Elysium Slider, Elysium Banner, or Elysium Section elements onto your page. Configure your content using the intuitive settings panel - no coding required.

## Installation

### Manual 
Go to [Release Page](https://gitlab.com/BlurCreative/Shopware/Plugins/BlurElysiumSlider/-/releases) download the source code and place the folder inside the `.zip` archive in your `custom/plugins/` folder.  
Be sure the folder has the name `BlurElysiumSlider` so the full path would be `custom/plugins/BlurElysiumSlider`

### Composer
```
composer require blurcreative/elysium-slider
```
**Note:** Build assets are **NOT included in the Composer package**. After installation you must build the assets:
```bash
bin/build-js.sh
```
This requires the `jq` package on your system.

### Composer via Shopware store repository (Recommended for production enviroments)
Follow the Shopware documentation to install from the official shopware store:
https://developer.shopware.com/docs/guides/hosting/installation-updates/extension-managment.html#installing-extensions-with-composer

## Requirements

Shopware 6.7

## Support

For documentation and support inquiries, please contact [your support channel].