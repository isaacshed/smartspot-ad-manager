=== SmartSpot Ad Manager ===
Contributors: isaacshed
Donate link: https://smartspotad.isaacauta.com/donate
Tags: ads, advertising, ad manager, banner, monetization
Requires at least: 5.0
Tested up to: 6.9
Requires PHP: 7.2
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

The simplest way to manage and display ads on your WordPress site. Works perfectly with Elementor, Gutenberg, and all page builders.

== External Services ==

This plugin uses Google AdSense to display ads.
Service: Google AdSense
Usage: Displaying ads on your website.
Data Sent: IP address and browser information may be sent to Google.
Privacy Policy: https://policies.google.com/privacy
Terms of Service: https://www.google.com/adsense/new/localized-terms

== Description ==

**SmartSpot Ad Manager** is the easiest way to manage advertising on your WordPress website. Display image ads anywhere on your site with precision URL targeting and device-specific controls.

Perfect for bloggers, content creators, and website owners who want to monetize their site without the complexity of traditional ad management plugins.

### Key Features

* **Easy Ad Management** - Create and manage ads from a simple, intuitive interface
* **URL Targeting** - Display ads on specific pages, posts, or custom post types
* **Image Ads** - Upload banner images and set click-through URLs
* **Multiple Positions** - Before content, after content, sidebar, and more
* **Shortcode Support** - Place ads anywhere with `[thesiadm_ads position="header"]`
* **Widget Support** - Add ads to any widget area
* **Elementor Compatible** - Drag and drop ad widget for Elementor page builder
* **Gutenberg Ready** - Native block editor support
* **Responsive Design** - Ads look great on all devices
* **Priority Control** - Set which ads show first when multiple ads match
* **No Coding Required** - User-friendly interface for non-technical users

### Display Your Ads

Multiple ways to display ads:

1. **Shortcode**: `[thesiadm_ads position="before-content"]`
2. **Widget**: Drag the SmartSpot Ad Manager widget to any sidebar
3. **Elementor**: Use the Ad Manager widget in Elementor
4. **Gutenberg**: Add the Ad block in the block editor
5. **Template Tag**: `<?php thesiadm_display_ads('header'); ?>`

### URL Targeting Options

Control exactly where your ads appear:

* **Exact Match** - Show on specific pages only
* **Contains** - Show on pages containing a URL pattern
* **Starts With** - Show on all pages in a section

Examples:
* `/` - Homepage only
* `/blog/` - Blog section
* `/products/` - Product pages
* `/contact-us/` - Contact page

### 💎 Pro Features (Coming Soon)

Upgrade to Pro for advanced features:

* **Custom Code Ads** - Google AdSense, HTML, JavaScript support
* **Device Targeting** - Show different ads on desktop, mobile, tablet
* **All Ad Positions** - Header, footer, and custom positions
* **A/B Testing** - Test different ads to optimize performance
* **Advanced Scheduling** - Schedule ads for specific dates and times - coming soon
* **Click Tracking** - Track ad impressions and clicks - coming soon
* **Geo-Targeting** - Show ads based on visitor location - coming soon
* **Priority Support** - Get help when you need it

[Learn more about Pro →](https://smartspotad.isaacauta.com/pro)

### Works With All Page Builders

* Elementor
* Gutenberg
* Classic Editor
* Beaver Builder
* Divi Builder
* WPBakery
* And more!

### Perfect For

* Bloggers monetizing their content
* Affiliate marketers
* Website owners selling ad space
* Publishers with multiple advertisers
* Anyone wanting simple ad management

### Developer Friendly

SmartSpot Ad Manager includes hooks and filters for developers:
* `thesiadm_display_ads()` - Template function
* `thesiadm_get_ads()` - Returns ad HTML
* `thesiadm_has_ads()` - Check if ads exist
* Custom filters for extending functionality

### Support

Need help? Check out our:

* [Documentation](https://smartspotad.isaacauta.com/docs)
* [FAQ](https://smartspotad.isaacauta.com/faq)
* [Support Forum](https://wordpress.org/support/plugin/smartspot-ad-manager/)

== Installation ==

### Automatic Installation

1. Log in to your WordPress admin panel
2. Go to Plugins → Add New
3. Search for "SmartSpot Ad Manager"
4. Click "Install Now" and then "Activate"

### Manual Installation

1. Download the plugin ZIP file
2. Go to Plugins → Add New → Upload Plugin
3. Choose the ZIP file and click "Install Now"
4. Activate the plugin

### Getting Started

1. Go to **Ad Manager** in your WordPress admin menu
2. Click **Add New Ad**
3. Enter a title for your ad
4. Upload a Featured Image (this will be your ad banner)
5. Enter the URL you want the ad to link to
6. Select where you want the ad to appear (position)
7. Enter target URLs where the ad should display
8. Click **Publish**

Your ad is now live! Visit your site to see it in action.

== Frequently Asked Questions ==

= How do I display ads on my site? =

There are several ways:

1. **Shortcode**: Add `[thesiadm_ads position="before-content"]` to any post, page, or widget
2. **Widget**: Go to Appearance → Widgets and add the SmartSpot Ad Manager widget
3. **Elementor**: Drag the Ad Manager widget onto your page
4. **Code**: Use `<?php thesiadm_display_ads('header'); ?>` in your theme files

= Can I show different ads on different pages? =

Yes! Use the URL Targeting feature. When creating an ad, enter the specific URLs where you want it to appear. For example, enter `/blog/` to show ads only on blog pages.

= What ad sizes are supported? =

SmartSpot Ad Manager works with any image size. Common ad sizes include:

* 728×90 (Leaderboard)
* 300×250 (Medium Rectangle)
* 160×600 (Wide Skyscraper)
* 300×600 (Half Page)
* 320×50 (Mobile Banner)

= Can I use Google AdSense? =

Google AdSense and custom code ads are available in the Pro version. [Upgrade to Pro](https://smartspotad.isaacauta.com/pro) to unlock this feature.

= Does it work with Elementor? =

Yes! SmartSpot Ad Manager includes a native Elementor widget. Just search for "Ad Manager" in the Elementor widget panel and drag it onto your page.

= Can I target mobile vs desktop users? =

Device targeting (showing different ads on mobile, tablet, and desktop) is a Pro feature. [Learn more](https://smartspotad.isaacauta.com/pro).

= Will ads slow down my site? =

No! SmartSpot Ad Manager is lightweight and optimized for performance. Ads are loaded efficiently and won't impact your site speed.

= Can I track ad clicks? =

Basic impression tracking is built-in (works with Google Analytics). Advanced click tracking and analytics wiil be available in the v2 Pro version.

= Is it GDPR compliant? =

SmartSpot Ad Manager doesn't collect any personal data from your visitors. However, if you're using third-party ad networks (like Google AdSense in Pro), you're responsible for GDPR compliance.

= How do I upgrade to Pro? =

Visit https://smartspotad.isaacauta.com/pro to purchase the Pro version. After purchase, install the Pro plugin alongside the free version to unlock all features.

= Does it work with custom post types? =

Yes! SmartSpot Ad Manager works with all post types including custom post types created by other plugins or themes.

== Screenshots ==

1. **Ad Manager Dashboard** - Clean, simple interface for managing all your ads
2. **Create New Ad** - Easy-to-use form for creating ads with URL targeting
3. **URL Targeting** - Precise control over where ads appear
4. **Elementor Widget** - Native Elementor integration for drag-and-drop ad placement
5. **Ad Preview** - See how your ad looks before publishing
6. **Multiple Positions** - Choose from various ad positions or create custom ones
7. **Admin List View** - Overview of all your ads with position and targeting info

== Changelog ==

= 1.0.0 - 2025-01-30 =
* Initial release
* Image ad support
* URL targeting with exact, contains, and starts-with matching
* Multiple ad positions (before content, after content, sidebar)
* Shortcode support
* Widget support
* Elementor widget integration
* Gutenberg block support
* Priority control for multiple ads
* Responsive design
* Template tag functions for developers

== Upgrade Notice ==

= 1.0.0 =
Initial release of SmartSpot Ad Manager. The easiest way to manage ads on WordPress!

== Privacy Policy ==

SmartSpot Ad Manager does not collect, store, or transmit any personal data from your website visitors. The plugin only stores ad configuration data in your WordPress database.

If you use third-party ad services (available in Pro version), those services may collect data according to their own privacy policies. You are responsible for compliance with privacy regulations like GDPR when using third-party ad services.

== Credits ==

SmartSpot Ad Manager is developed and maintained by Isaac Shed.
https://isaacauta.com

Special thanks to the WordPress community for their continuous support and feedback.


== Contribute ==

SmartSpot Ad Manager is open source! Contribute on GitHub:
https://github.com/isaacshed/smartspot-ad-manager
