=== Delight Cookie Banner ===
Contributors: PC Delight
Donate link: https://pcdelight.ch
Tags: cookies, gdpr, privacy, banner, woocommerce
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A minimal, multilingual cookie notice for WordPress. GDPR-friendly, lightweight, and compatible with all themes and WooCommerce.

== Description ==

Delight Cookie Banner displays a clean and minimal cookie notice on your WordPress site.  
It provides an easy way to inform visitors that your website uses cookies — without adding complex consent management or tracking logic.  
The plugin does **not** automatically block cookies or scripts. Its purpose is to show a clear, user-friendly notification and remember whether the visitor closed it.

You can customize all colors, texts, and positions directly in your WordPress settings — and instantly preview every change thanks to the built-in **live preview** in the admin area.  
All adjustments are visual and intuitive, so you always see exactly what your visitors will see.

This field applies only to the default language. Other translations must be changed using a translation tool (e.g. Loco Translate or Polylang).  
Language names are displayed automatically in a user-friendly format (e.g. Deutsch, Français, Italiano, English).

### Features
- Simple cookie notice for GDPR, TTDSG, and CCPA awareness  
- Built-in live preview for instant visual feedback  
- Custom colors and hover effects for both buttons  
- Smooth fade-in and fade-out animation  
- Banner position at the top or bottom of the page  
- “Accept” and “Reject” buttons (for visual preference only)  
- Reject Button can be shown or hidden via settings  
- Shortcodes for reopening or resetting the banner  
- Optional automatic footer link  
- Uses a small functional cookie to remember the visitor’s choice (`dcb_consent`)  
- Works with any WordPress theme and WooCommerce  
- Multilingual ready (English, German, French, Italian)  
- No jQuery, no tracking, no external dependencies  

### How it works
When a visitor loads your site for the first time, a simple notice appears informing them that the site uses cookies.  
The visitor can dismiss the banner by clicking “Accept” or “Reject.”  
This choice is stored in a small functional cookie (`dcb_consent`) so the banner doesn’t reappear on every page load.  
No tracking, analytics, or marketing cookies are used or blocked by the plugin itself.

### Admin options
Under **Settings → Delight Cookie Banner**, you can customize:
- Background and text colors  
- Button and hover colors  
- Banner position (top or bottom)  
- Text for the banner message and buttons  
- Privacy policy page selection  
- Automatic footer link for “Change cookie settings”  
- Live preview of all design and text changes  
- Option to show or hide the Reject button  

### Shortcodes
- `[dcb_open label="Change cookie settings"]` shows the banner again so users can review the message.  
- `[dcb_reset label="Reset cookie settings"]` deletes the stored consent and displays the banner again.

Shortcodes can be used anywhere – in posts, widgets, or page builder elements.  
You can also use the CSS classes `.dcb-open` and `.dcb-reset` on your own links or buttons.

== Installation ==

1. Upload the folder `delight-cookie-banner` to the `/wp-content/plugins/` directory.  
2. Activate the plugin through the “Plugins” menu in WordPress.  
3. Open **Settings → Delight Cookie Banner** to configure your options and use the live preview.

== Frequently Asked Questions ==

= Does this plugin block cookies or scripts? =  
No. Delight Cookie Banner is a notice tool only. It informs visitors that cookies are used but does not manage or block third-party scripts.

= Does this plugin set cookies? =  
Yes, it sets one small functional cookie named `dcb_consent` to remember whether the visitor closed the banner.

= Is it GDPR and CCPA compliant? =  
The plugin helps website owners meet transparency requirements by displaying a clear notice about cookie usage.  
However, full legal compliance depends on how your website handles cookies and third-party scripts.

= Can I use it with WooCommerce? =  
Yes, it integrates seamlessly with WooCommerce and any WordPress theme.

= How can I add the notice link manually? =  
You can insert `[dcb_open]` anywhere – for example, in the footer or a text widget.

= Can I reset the notice? =  
Yes. Add `[dcb_reset]` to any page or post. It clears the saved consent and displays the banner again.

== Screenshots ==
1. Simple cookie notice on the front end  
2. Admin settings screen with live preview and color controls  
3. Example of the footer link and shortcodes  

== Changelog ==

= 1.1.0 =
- Added `[dcb_reset]` shortcode to clear consent  
- Added smooth fade-in and fade-out animation  
- Improved multilingual usability and automatic language labels  
- General accessibility and styling improvements  
- Minor fixes and admin interface refinements

= 1.0.0 =
- Initial release

== Upgrade Notice ==
Version 1.1.0 introduces live preview, hover color customization, reset functionality, and improved multilingual support.  
Updating is recommended for all users.

== License ==
This plugin is licensed under the GPLv2 or later.
