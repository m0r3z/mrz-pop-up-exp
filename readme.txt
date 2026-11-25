=== MRZ Pop-up Exp ===
Contributors: m0r3z
Tags: popup, modal, overlay, announcement, exit intent
Requires at least: 6.3
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

A very simple popup: HTML text, optional image on the left, and basic display rules.

== Description ==

MRZ Pop-up Exp adds simple popups to your site. There is one layout: your HTML text, with or without an image on the left. The styling is left to your theme.

= Features =

* HTML text with shortcodes, and an optional image from the media library.
* Popup width, image width, overlay color and opacity.
* Optional start and end dates.
* Display on the homepage, on all pages, on all but the homepage, on specific posts or pages, or nowhere.
* Optionally hidden on mobile (under 768 px).
* Eight triggers: immediately, after some seconds, after scrolling some pixels, at half of the page, at the bottom of the page, after some minutes on the site, after some pages viewed in 90 days, or on exit intent.
* Frequency: never again, not again during the session, not again for 7, 30 or 90 days, or always.
* Open a popup with a link: `<a href="#mrzpop-ID">…</a>`.
* Preview of the last saved version from the admin.
* Accessible: dialog role, keyboard focus kept in the popup, closed with the Escape key, the close button or a click on the overlay.
* Dates, frequency and triggers are checked in the browser, so they work on cached pages.

= Privacy / external calls =

MRZ Pop-up Exp makes no external HTTP calls, loads no remote resources, sets no cookies and collects no telemetry.

To apply the frequency and the triggers, it stores the following in the visitor's browser (localStorage and sessionStorage). Nothing is sent to your server:

* the date a popup was last seen, and whether one of its links was clicked;
* the number of pages viewed per day over the last 90 days, only if a published popup uses this trigger;
* the time spent on the site in the current tab, only if a published popup uses this trigger;
* after a form is submitted in a popup, a temporary marker to reopen this popup with its confirmation or its errors.

You may want to mention it in your privacy policy.

= Source code and contributions =

Development happens publicly on GitHub: https://github.com/m0r3z/mrz-pop-up-exp

== Installation ==

1. Upload the plugin to `/wp-content/plugins/mrz-pop-up-exp` and activate it.
2. Open the "MRZ Pop-up" menu and add a popup.
3. Write its text, choose an image if needed, and set its appearance and display rules.
4. Save, check it with "Preview popup", then publish.

== Frequently Asked Questions ==

= Can I put a form in a popup? =

Yes, with the shortcode of your form plugin. Raw forms, scripts, styles and iframes are removed from the HTML text.

= Why does exit intent not work on phones? =

Exit intent needs a mouse pointer leaving the page. It does not exist on touch screens.

= What does "session" mean? =

The browser tab. A popup seen in a tab can open again in a new one.

= The popup link does nothing =

The popup must be published, within its dates, and loaded on the page: the same page rules apply to the link.

= How do I style the popup? =

With your theme's CSS. The CSS variables `--mrzpop-background`, `--mrzpop-padding`, `--mrzpop-gap`, `--mrzpop-duration` and `--mrzpop-z-index` are available.

= What happens when I delete the plugin? =

All popups and their settings are deleted.

== Changelog ==

= 1.0.0 =
* First version.
