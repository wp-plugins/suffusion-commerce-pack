=== Suffusion Commerce Pack ===
Contributors: sayontan
Donate link: http://aquoid.com/news/plugins/suffusion-commerce-pack/
Tags: ecommerce, suffusion, jigoshop, prospress
Requires at least: WP 3.1
Tested up to: WP 3.3.2
Stable tag: trunk

A plugin that provides template support for common e-commerce plugins in the <a href='http://wordpress.org/extend/themes/suffusion'>Suffusion</a> theme.

== Description ==

Suffusion Commerce Pack aims to provide support for common e-commerce plugins in the Suffusion theme. The purpose of this plugin
is to deliver any template and stylesheet modifications that are needed to get the e-commerce plugins to play nicely with Suffusion.

Currently the following e-commerce plugins are supported:

*	<a href='http://wordpress.org/extend/plugins/jigoshop/'>Jigoshop</a>
*	<a href='http://wordpress.org/extend/plugins/prospress/'>Prospress</a>

Support for more plugins will be added in the near future. The ones on my radar at this point are WooCommerce and WP-ecommerce.

== Installation ==

You can install the plugin through the WordPress installer under <strong>Plugins &rarr; Add New</strong> by searching for it,
or by uploading the file downloaded from here. Alternatively you can download the file from here, unzip it and move the unzipped
contents to the <code>wp-content/plugins</code> folder of your WordPress installation. You will then be able to activate the plugin.

== Screenshots ==

1.	The settings page for the plugin can be accessed via <em>Appearance &rarr; Suffusion Commerce Pack</em>.
2.	The settings page tells you if you have a supported plugin and if you need to do anything specific for a plugin.

== Frequently Asked Questions ==

= Are there any other plugins that you will support apart from the ones listed? =

Yes, I do plan to support the more common and popular plugins so that they work seamlessly with Suffusion.

= But I want my plugin to be supported for Suffusion immediately! =

There are instructions provided on the settings page for the plugin about how to get any template working with Suffusion.
These should help you port any plugin over. Feel free to share your code with me and I can get it added to the core.

= Do I need a child theme? =

This depends on your e-commerce plugin. Some plugins like Jigoshop or WooCommerce (which is a Jigoshop fork) offer hooks. Where possible
the Suffusion Commerce Pack makes use of these hooks, eliminating the need for special template files. However some other plugins such
as Prospress offer templates that need to be copied over for customization. In such cases you are better off running the Suffusion Commerce
Pack against a child theme. The plugin provides you with appropriate advice either way. If you need a child theme it tells you to create one,
otherwise it goes about its task silently.

= Are there any known issues? =

The plugin requires a teensy bit of JavaScript in the back-end. For this it uses JQuery. If there is another plugin that includes
JQuery improperly, then there might be issues getting the plugin to work. This isn't a problem with the Commerce Pack, rather it
is a problem with the plugin that is improperly including JQuery.

== Changelog ==

= 1.01 =

*	Prospress had a compatibility issue with WP 3.3.1. This has been addressed.

= 1.00 =

*	New version created.
*	Support provided for Jigoshop and Prospress

== Upgrade Notice ==

No upgrade notices at this point.