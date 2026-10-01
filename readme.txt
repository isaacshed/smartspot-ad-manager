=== SmartSpot Ad Manager ===
Contributors: isaacshed
Donate link: https://smartspotad.isaacauta.com/donate
Tags: ads, advertising, ad manager, elementor, gutenberg
Requires at least: 5.0
Tested up to: 7.1
Requires PHP: 7.2
Stable tag: 2.0.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Manage and display ads on your WordPress site. Native Elementor & Gutenberg support, 7 positions, device & URL targeting.

== External Services ==

This plugin optionally supports Google AdSense for displaying custom code ads.
Service: Google AdSense
Usage: Displaying ads on your website via custom code pasted by the admin.
Data Sent: When AdSense code is rendered on the front-end, Google may receive IP address and browser information per their own policies.
Privacy Policy: https://policies.google.com/privacy
Terms of Service: https://www.google.com/adsense/new/localized-terms

No data is collected or transmitted by this plugin itself.

== Description ==

**SmartSpot Ad Manager** lets you manage advertising on your WordPress website. Display image ads or custom code ads (Google AdSense, HTML, JavaScript) anywhere on your site with URL targeting, device-specific controls, and native integrations for popular page builders.

100% free & open-source. No Pro upgrades, no paywalls, no licensing.

Suited for bloggers, content creators, and website owners who want to monetize their site using image and custom-code ads.

### Key Features

* **Easy Ad Management** - Create and manage ads from a straightforward admin interface.
* **URL Targeting** - Display ads on specific pages, posts, or custom post types with exact, contains, or starts-with matching.
* **Image Ads** - Upload banner images and set click-through URLs.
* **Custom Code Ads** - Paste Google AdSense, HTML, or JavaScript ad code directly.
* **Multiple Ad Positions** - Before content, after content, sidebar, header, footer, and two custom positions.
* **Device Targeting** - Show different ads on desktop, mobile, and tablet.
* **Automatic Injection** - Ads set to Before/After Content appear automatically on singular posts.
* **Shortcode Support** - Place ads anywhere with easy shortcodes.
* **Widget Support** - Add ads to any widget area with our enhanced widget.
* **Elementor Compatible** - Native Elementor widget with full position + single-ad modes.
* **Gutenberg Ready** - Native block with ServerSideRender preview.
* **Divi, Beaver Builder, WPBakery, SiteOrigin, Oxygen, Bricks** - Native modules for every major builder.
* **Priority Control** - Set which ads show first when multiple ads match.
* **Responsive Design** - Ads look great on all devices.
* **No Coding Required** - User-friendly interface for non-technical users.
* **GDPR Friendly** - Plugin stores zero personal visitor data.

### Display Your Ads

Multiple ways to display ads:

1. **Automatic**: Choose "Before Content" or "After Content" position and ads appear automatically.
2. **Shortcode (by Position)**: `[thesiadm_ads position="before-content"]`
3. **Shortcode (Single Ad)**: `[thesiadm_ad id="123"]`
4. **Widget**: Drag the SmartSpot Ad Manager widget to any sidebar.
5. **Elementor**: Use the SmartSpot Ad Manager widget in Elementor (position or single-ad mode).
6. **Gutenberg**: Add the "SmartSpot Ad" block in the block editor.
7. **Divi / Beaver Builder / WPBakery / SiteOrigin / Oxygen / Bricks**: Use the native module provided for each builder.
8. **Template Tag**: `<?php thesiadm_display_ads('header'); ?>` or `<?php echo thesiadm_display_single_ad(123); ?>`

### URL Targeting Options

Control exactly where your ads appear:

* **Exact Match** - Show on specific pages only
* **Contains** - Show on pages containing a URL pattern
* **Starts With** - Show on all pages in a section

Examples:
* `/` - Homepage only
* `/blog/` - Blog section (Starts With) or anywhere containing "blog" (Contains)
* `/products/` - Product pages section
* `/contact-us/` - Contact page

### Works With Every Page Builder

SmartSpot Ad Manager includes native modules and compatibility layers for:

* **Elementor** (Custom widget with alignment, margin, padding controls)
* **Gutenberg / WordPress Blocks** (Native block with server-side render preview)
* **Classic Editor** (Shortcode button coming soon; shortcodes work everywhere)
* **Divi Builder** (Custom module)
* **Beaver Builder** (Custom module)
* **WPBakery Page Builder** (vc_map elements)
* **SiteOrigin Page Builder** (Custom widget)
* **Oxygen Builder** (Custom element)
* **Bricks Builder** (Custom element)
* **Any shortcode-aware editor**

### Perfect For

* Bloggers monetizing their content
* Affiliate marketers
* Website owners selling ad space
* Publishers with multiple advertisers
* Anyone wanting simple, powerful ad management

### Developer Friendly

SmartSpot Ad Manager includes hooks, template functions, and filters:
* `thesiadm_display_ads( $position )` - Echo ad HTML for a position
* `thesiadm_get_ads( $position )` - Returns ad HTML
* `thesiadm_display_single_ad( $id )` - Returns a single ad
* `thesiadm_has_ads( $position )` - Check if ads exist for current page / position
* `thesiadm_allowed_ad_html` - Filter the HTML allowed in custom code ads
* Full shortcode system with aliases: `thesiadm_ads`, `thesiadm_ad`, `smartspot_ads`, `smartspot_ad`

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
4. For an **Image Ad**: Upload a Featured Image and set Ad Link + Open in New Tab
5. For a **Custom Code Ad**: Choose "Custom Code" ad type and paste your AdSense/HTML/JS in the Ad Code box
6. Select a Position (Before Content, After Content, Sidebar, Header, Footer, Custom…)
7. Enter target URLs where the ad should display (leave empty to show everywhere)
8. Select devices to target
9. Set priority (1 = highest)
10. Click **Publish**

Your ad is now live! If you chose Before/After Content, ads display automatically. For other positions use a shortcode, widget, or page builder module.

== Frequently Asked Questions ==

= How do I display ads on my site? =

There are several ways:

1. **Automatic**: Set the ad position to "Before Content" or "After Content" and ads inject automatically on singular posts/pages.
2. **Shortcode**: Add `[thesiadm_ads position="before-content"]` or `[thesiadm_ad id="123"]` to any post, page, or widget.
3. **Widget**: Go to Appearance → Widgets and add the SmartSpot Ad Manager widget.
4. **Elementor / Divi / Beaver Builder / WPBakery / etc.**: Drag the native SmartSpot module onto your page.
5. **Code**: Use `<?php thesiadm_display_ads('header'); ?>` in your theme files.

= Can I show different ads on different pages? =

Yes! Use the URL Targeting feature. When creating an ad, enter the specific URLs where you want it to appear. For example, enter `/blog/` (with match type "Starts With") to show ads only on blog section pages.

= What ad sizes are supported? =

SmartSpot Ad Manager works with any image size. Common ad sizes include:

* 728×90 (Leaderboard)
* 300×250 (Medium Rectangle)
* 160×600 (Wide Skyscraper)
* 300×600 (Half Page)
* 320×50 (Mobile Banner)

= Can I use Google AdSense? =

Yes! Select "Custom Code" as the Ad Type and paste your AdSense code into the Ad Code box. The plugin will load the AdSense library automatically if it's not already loaded.

= Does it work with Elementor? =

Yes! SmartSpot Ad Manager includes a native Elementor widget with two display modes: by position, or a specific single ad. Includes responsive margin, padding, and alignment controls.

= Does it work with the Block Editor (Gutenberg)? =

Yes! Use the "SmartSpot Ad" block. The block provides ServerSideRender preview in the editor and renders identically on the front-end.

= Can I target mobile vs desktop users? =

Yes! Use the Device Targeting meta box to select which devices (Desktop, Mobile, Tablet) will see each ad. This is free for everyone.

= Will ads slow down my site? =

No! SmartSpot Ad Manager is lightweight and optimized for performance. Image ads use native lazy loading and queries use `no_found_rows` to keep them fast.

= Can I track ad clicks? =

The plugin integrates with Google Analytics (gtag.js and ga.js) for impression and click event tracking. You can also use your own analytics solution via the included hooks.

= Is it GDPR compliant? =

Yes. SmartSpot Ad Manager itself does not collect, store, or transmit any personal data from your website visitors. The plugin only stores ad configuration (your content) in your local WordPress database.

If you use third-party ad networks (e.g., Google AdSense via custom code), those services may collect data according to their own privacy policies, and you are responsible for compliance with applicable regulations when using them.

= Does it work with custom post types? =

Yes! SmartSpot Ad Manager works with all post types, including custom post types created by other plugins or themes. The "Before/After Content" injection works on any singular view, and shortcodes/widgets work everywhere.

= Does it work with Divi, Beaver Builder, WPBakery, Oxygen, Bricks? =

Yes. Each popular page builder has a registered native module that ships with the plugin. The modules load only when the corresponding builder is active, so they never add overhead.

== Screenshots ==

1. **Ad Manager Dashboard** - Clean list view with position, type, device, priority, and URL targeting info at a glance.
2. **Create New Ad** - Easy-to-use metaboxes for settings, URL targeting, device targeting, ad code, and preview.
3. **URL Targeting** - Precise control over where ads appear using exact, contains, or starts-with matching.
4. **Elementor Widget** - Native Elementor integration with drag-and-drop ad placement.
5. **Gutenberg Block** - Native block editor support with position and single-ad modes.
6. **Multiple Positions** - Choose from seven ad positions or use shortcodes / template tags.
7. **Ad Preview & Shortcodes** - See your image preview plus copy-to-clipboard shortcodes.
8. **Widget** - Enhanced widget with display mode, position, and single-ad controls.

== Upgrade Notice ==

= 2.0.2 =
Fixes a remaining MissingUnslash PHPCS warning on the ad-code metabox save path.

= 2.0.1 =
Resolves all WordPress Plugin Check errors and warnings for WP.org approval (i18n comments, security sanitization, parse_url, escape output, naming conventions, readme header, slow query annotations).

= 2.0.0 =
This is a major update that makes all features free, adds extensive page builder compatibility, and fixes security + guideline issues for WordPress.org approval.

== Privacy Policy ==

SmartSpot Ad Manager does not collect, store, or transmit any personal data from your website visitors. The plugin only stores ad configuration data (titles, images, URLs, code snippets you enter) in your local WordPress database.

If you choose to include third-party ad network code in Custom Code ads (e.g., Google AdSense), those networks may collect data under their own privacy policies. You are responsible for compliance with privacy regulations when using third-party services.

== Credits ==

SmartSpot Ad Manager is developed and maintained by Isaac Shed.
https://isaacauta.com

Special thanks to the WordPress community for their continuous support and feedback.

== Contribute ==

SmartSpot Ad Manager is open source! Contribute on GitHub:
https://github.com/isaacshed/smartspot-ad-manager
