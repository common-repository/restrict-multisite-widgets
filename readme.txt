=== Restrict Multisite Widgets ===
Contributors: kawauso
Donate link: http://adamharley.co.uk/buy-me-a-coffee/
Tags: multisite, widgets, restrict
Requires at least: 3.0
Tested up to: 3.2
Stable tag: 1.1.4

Allows network admins to restrict which widgets are available on sites, similar to themes.

== Description ==

A quick adaptation of the theme restriction code for widgets. Can only restrict on a site-wide basis, though it will only affect those sites on which it's activated. Does not restrict super admins. Plugins <strong>must</strong> be active on the main site to be controlled by this plugin (this is an issue with WordPress' plugin structure).

See also: <a href="http://wordpress.org/extend/plugins/restrict-multisite-plugins/">Restrict Multisite Plugins</a>

== Installation ==

1. Upload `restrict-multisite-widgets.php` to `/wp-content/plugins/` directory
2. Network Activate the plugin through the 'Plugins' menu OR Activate the plugin through the 'Plugins' menu of every WordPress site you wish to restrict
3. Select widgets to make available under the 'Widget Restrictions' section in the 'Themes' menu in the Network Admin or 'Widgets' section in the 'Super Admin' menu

== Frequently Asked Questions ==

= Can I enable widgets only for individual sites? =

No. You can as a Network Admin however activate widgets for individual sites without restrictions.

= Will this plugin affect widgets already activated by site admins? =

Yes. If a widget is activated by a site admin and disabled by this plugin, it will be disabled immediately.

= Why doesn't this plugin display widgets I've activated on a single site? / Why does this plugin display widgets I've activated on my main site? =

Due to the way WordPress MultiSite is written, it isn't possible for the plugin to reliably detect what widgets are available on sites other than the main site.

== Screenshots ==

It looks much the same as the 'Themes' section in the 'Super Admin' menu in WordPress 3.0.

== Changelog ==

= 1.1.4 =
* Fixed bug with removing single instance widgets introduced in 1.1.3 (thanks israelwebdev)

= 1.1.3 =
* Activation state check for menus to support single blog activation
* Fixed single instance widget detection (PHP isset() quirk)
* Moved to native single instance widget removal

= 1.1.2 =
* Changed class loading to admin-only to avoid unintentional frontend restrictions (thanks dwieeb)

= 1.1.1 =
* Improved support for older plugin widgets
* Standardised capability used to 'manage_network_themes'
* Changed plugin class back to dynamic callbacks

= 1.1 =
* Added support for Network Admin
* Moved page to Network Admin -> Themes -> Widget Restrictions under WordPress 3.1 and higher
* Changed plugin class to static callbacks

= 1.0 =
* First public release

== Upgrade Notice ==

= 1.1.4 =
Fixes bug in removing single instance widgets introduced in 1.1.3.

= 1.1.3 =
Support for single blog activation (menu item under Site Admin for single blogs). Fixed single instance widget detection.

= 1.1.2 =
Stops restricted widgets from not appearing for non-super admins.

= 1.1.1 =
Better support for older plugin widgets. Standardised user capability used to 'manage_network_themes'.

= 1.1 =
Support for WordPress 3.1. Page is now under Network Admin -> Themes -> Widget Restrictions in WordPress 3.1 and higher.