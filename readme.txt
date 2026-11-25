=== Fand Vendor Store Blocks for WCFM ===
Contributors: fandevelop
Donate link: https://fan-develop.fr
Tags: wcfm, woocommerce, blocks, marketplace, vendor
Requires at least: 7.1
Tested up to: 7.1
Requires PHP: 8.2
Stable tag: 1.0.2
Requires Plugins: woocommerce, wc-frontend-manager
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Fully customize your WCFM vendor store pages using native, modular Gutenberg blocks.

== Description ==

**Fand Vendor Store Blocks for WCFM** transforms the customization of vendor storefronts on WCFM Marketplace by replacing rigid templates with native, flexible, and fully styleable Gutenberg blocks.

Designed to integrate naturally with the WordPress block editor (Gutenberg / FSE), this plugin allows marketplace administrators to build custom layouts for their vendors' stores without writing a single line of code.

= Included Blocks (18 blocks) =

**Store Header & Identity**
- **Store Banner (`fand-storefront/store-banner`)** : Displays the store cover image (desktop and mobile) with automatic fallback to the default WCFM banner and inner content support.
- **Vendor Logo (`fand-storefront/store-logo`)** : Displays the vendor avatar/logo with a customizable pixel size.
- **Store Name (`fand-storefront/store-name`)** : Displays the store's commercial name as an `<h1>` heading.
- **Vendor Badges (`fand-storefront/store-badges`)** : Highlights verification badges and distinctions awarded to the vendor.
- **Rating & Reviews (`fand-storefront/store-rating`)** : Displays the vendor's average star rating when reviews are enabled in WCFM.
- **Store Actions (`fand-storefront/store-actions`)** : Displays native WCFM "Enquiry" and "Follow me" action buttons.

**Contact & Presentation**
- **Store Description (`fand-storefront/store-description`)** : Displays the detailed store presentation written by the vendor.
- **Store Address (`fand-storefront/store-address`)** : Displays the vendor's postal address preceded by a customizable icon.
- **Store Phone (`fand-storefront/store-phone`)** : Displays the clickable phone number (`tel:`) with a customizable icon.
- **Store Email (`fand-storefront/store-email`)** : Displays the spam-protected email address (`antispambot`) with a customizable icon.
- **Social Links (`fand-storefront/store-social`)** : Displays links and icons to the vendor's social profiles.

**Navigation & Catalog**
- **Store Tabs (`fand-storefront/store-tabs`)** : Displays the WCFM tab navigation bar (Products, About, Policies, Reviews, Followers, Articles…).
- **Active Tab Content (`fand-storefront/store-content`)** : Dynamically displays the content corresponding to the tab selected by the visitor.
- **Store Products (`fand-storefront/store-products`)** : Displays the vendor's product grid.

**Sidebar Widgets**
- **Store Search (`fand-storefront/store-search`)** : Displays an internal search engine filtering only the vendor's products (with a customizable title).
- **Store Categories (`fand-storefront/store-category`)** : Displays the vendor's product categories as a compact dropdown list (with a customizable title).
- **Store Hours (`fand-storefront/store-hours`)** : Displays the vendor's weekly opening hours in a clear, compact format.
- **Store Location (`fand-storefront/store-location`)** : Displays the store's geographical map via the WCFM widget (with a customizable title).

= Key Features =

- **Native Gutenberg Controls** : Full support for native WordPress settings (colors, backgrounds, spacings, side-by-side borders, and independent corner radiuses).
- **WCFM Privacy Compliance** : Each block automatically checks permissions and hiding rules defined by the administrator and vendor in WCFM.
- **Automatic Context Detection** : Blocks automatically detect the vendor displayed on the current page and are extensible via add-ons (e.g., event or branch pages).
- **Server-Side Rendering (PHP)** : Optimal performance and full compatibility with Full Site Editing (FSE) themes and page builders.

WCFM and WooCommerce are trademarks of their respective owners. This plugin is an independent add-on and is not affiliated with, endorsed by or sponsored by them.

== Installation ==

1. Make sure **WooCommerce** and **WCFM - WooCommerce Frontend Manager** are installed and activated.
2. Upload the `fand-vendor-store-blocks-for-wcfm` folder to the `/wp-content/plugins/` directory of your WordPress site, or install the `.zip` archive via the **Plugins > Add New** screen.
3. Activate the extension from the WordPress **Plugins** menu.
4. Open your store page template in the Gutenberg editor and insert blocks from the **Fand Vendor Store (WCFM)** category.
5. Go to **Settings > Reading** and select your template page under **Store template page (Fand Vendor Store Blocks)** (and optionally for branch or event pages).

== Frequently Asked Questions ==

= Does this plugin require WCFM Marketplace? =
Yes. **Fand Vendor Store Blocks for WCFM** is built specifically to work with WooCommerce and WCFM (`wc-frontend-manager`).

= Are the blocks compatible with FSE (Full Site Editing) themes? =
Yes. All blocks use the native Gutenberg APIs (`useBlockProps`, `get_block_wrapper_attributes()`) and inherit the color palettes and typography of your theme.

= How do I style the borders and rounded corners of a block? =
Select the block in the editor, open the **Style** tab in the right sidebar, then use the native **Borders** panel to adjust each side and each corner separately.

= Is the plugin translated? =
The plugin is written in English and is translation-ready. French is available; other translations can be contributed at translate.wordpress.org.

== Screenshots ==

1. Building a WCFM store page in the Gutenberg editor with the modular blocks.
2. Customizing native styles (colors, borders, spacing) in the sidebar.

== Changelog ==

= 1.0.2 =
- Added url adress

= 1.0.1 =
- Added page settings

= 1.0.0 =
- Initial release with 18 modular WCFM store blocks and full native Gutenberg support.