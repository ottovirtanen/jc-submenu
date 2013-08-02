=== JC Submenu ===
Contributors: jcollings
Donate link: 
Tags: submenu, menu, dynamic, custom post type, taxonomy, child pages
Requires at least: 3.0.1
Tested up to: 3.6
Stable tag: 0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

JC Submenu plugin allows you to automatically populate your navigation menus with custom post_types, taxonomies, or child pages.

== Description ==

JC Submenu plugin allows you to automatically populate your navigation menus with custom post_types, taxonomies, or child pages. An easy to use plugin created to be a lightweight menu extension.

Also output a selected section of your dynamic menu through our advanced submenu widget.

== Installation ==

1. First grab a copy from [wordpress.org](http://downloads.wordpress.org/plugin/jc-submenu.zip).
1. Extract the plugin to your wordpress plugins folder.
1. Activate the plugin from your wordpress administration area (under the plugins section).
1. You should thee should now be able to use JC Submenu Plugin.
1. Optional - If your theme already has a custom walker specified for outputing your menu, JC Submenu will not automatically override it, to do this locate the file your theme outputs the menu (usually header.php) and look for "wp\_nav\_menu()", and pass an extra argument so it looks similar:
		 
`wp_nav_menu(array('walker' => new JC_Submenu_Nav_Walker()));`

== Frequently asked questions ==


== Screenshots ==

1. JC Submenu, Post population options
2. JC Submenu, Taxonomy population options
3. JC Submenu, Page children population options
4. JC Submenu, Advanced Submenu Widget Options

== Changelog ==
**0.2**

*   Interface update
*   Custom post type population can now be filtered by a Taxonomy
*   Javascript update
*   Compatability with wordpress 3.6


== Upgrade notice ==
