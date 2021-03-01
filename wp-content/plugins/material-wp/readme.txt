=== Material WP ===
Contributors: aanduque
Requires at least: 4.2.2
Tested up to: 5.2.2
Requires PHP: 5.6
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Completely transform your admin interface with the Google's Material Design styles.

== Description ==

Material WP

Completely transform your admin interface with the Google's Material Design styles.

== Installation ==

1. Upload 'material-wp' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==

Version 1.0.6 - 12/07/2019

* Improved: Removed "Apply to users" when in network mode to prevent performance hiccups;
* Improved: Added an option to exclude Super Admins on the role settings;

Version 1.0.5 - 30/06/2019

* Fixed: Updater not working as WP Ultimo add-on;

Version 1.0.4 - 20/06/2019

* Fixed: Small issues with the interim login modal window on the admin;
* Fixed: Removed unecessary queries that were being performed on every page load;
* Fixed: Incompatibility with WordPress 5.2.2 on the Screen Meta links;

Version 1.0.3 - 17/05/2019

* Fixed: Broken RTL support and option to have the menu on the right side of the screen;

Version 1.0.2 - 15/05/2019

* Fixed: Issue with WP Ultimo - Plugin and Theme Manager;
* Fixed: Made this compatible with Gutenberg again, after the changes on WP 5.1;

Version 1.0.1 - 04/02/2019

* Fixed: Small incomatibility with Elementor PRO;

Version 1.0.0 - 21/01/2019

* Fixed: No longer adds dynamic styles to front if the user is not logged in;
* Fixed: Auto-updater uses the new Envato API to validate licenses;
* Added: Gutenberg/Block Editor Support!

Version 0.0.52 - 08/10/2018

* Fixed: Incompatibilities with Admin Columns Pro;

Version 0.0.51 - 01/10/2018

* Fixed: Astra incompatibilities;
* Fixed: Custom CSS on login page not appearing in some cases;

Version 0.0.50 - 09/08/2018

* Fixed: Menu bugs on long screens;

Version 0.0.49
 
* Fixed: Tick all in post and plugin admin pages not working;
* Fixed: Issue with RTL layouts;
* Fixed: Issue with the Zephyr theme;
* Fixed: Customizer bug;

Versio 0.0.48 - 08/08/2018

* Fixed: Incompatibility with Askimet on activation;
* Fixed: Yoast top-bar icon misalignment;
* Fixed: Small issues with the styling of the search results bar and the file editor header;
* Improved: Compatibility issues with ConvertPRO, SEOPress and Zero BS CRM;
* Added: Color control for the background of the admin pages;

Versio 0.0.47

* Fixed: Fatal error from last release =/

Version 0.0.46

* Fixed: Issues with generating and loading the dynamic styles;

Version 0.0.45

* Fixed: Color Pickers not working with WordPress 4.9;
* Fixed: Badge styles (update and comment counts) being broken;
* Fixed: Issues with the blacklist arrays throwing fatal errors on activation;
* Fixed: Styles breaking when new sites are created in the network;
* Improved: Dynamic CSS file is now generated at the uploads folder to prevent permission issues;
* Added: Basic Gutenberg Support;

Version 0.0.44

* Added: Option to remove the Cog Icon from the TopBar;

Version 0.0.43

* Fixed: Issue with styles;
* Fixed: PHP Warning being thrown due to the newly added roles function;

Version 0.0.42

* Fixed: Minor issue with button "Upload Theme".

Version 0.0.41

* Added: Admins can now select roles to apply the theme;
* Added: Admins can now select users to apply the theme;
* Fixes: Small bug and compatibility issues;

Version 0.0.40

* Fixes: Small bug and compatibility issues;

Version 0.0.39

* Fixes: Small bug and compatibility issues;
* Added: Blacklist feature under the functionalities table;

Version 0.0.38

* Fixed: Small problem with Mailster admin menus

Version 0.0.37

* Fixed: Incompatibility with Thrive Leads
* Fixed: Expand button only appeared in a few pages in the admin
* Fixed: Reestructure of some CSS directives to allow better support of custom fonts

Version 0.0.36

* Fixed: Compatible with WP 4.7
* Fixed: some typos in the settings page
* Fixed: menu label now has no hight when empty
* Added: Loco Translator XML config file to allow translations using the plugin
* Fixed: HTML code being displayed when used with "Toolset Types" plugin
* Fixed: Some wraps fixed + alignment issues
* Fixed: Problem with Visual Composer frontend editor
* Fixed: New strings translated for the Brazilian pPortuguese language

Version 0.0.35

* Fixed: Exporter not working on Multisite
* Fixed: Incompatibilities with Booked Appointments

Version 0.0.34

* Fixed: incompatibility with Ninja Forms

Version 0.0.33

* New Feature: Option to hide the avatar Link
* New Feature: Option to change the link of the avatar block

Version 0.0.32

* Better compatibility with ConvertPlugin

Version 0.0.31

* New Feature: Expand Button

Version 0.0.30

* Fixed: minor bugs with the tooltip in the RTL version.
* Changed our name from 732 to the rebranded NextPress.

Version 0.0.29

* Fixed: Styles gettings added to the frontend, now it is not.
* Fixed: Update notices displayed in a weird way in the nav-menus admin page.

Version 0.0.28

* Fixed: Importer not recompiling after importing settings.
* Fixed: Folded menu bugs.
* New Feature: Option to set the url of the logo on the admin bar.

Version 0.0.27

* Fixed: Disable admin-bar on the frontend was bugging the admin bar on the backend.

Version 0.0.26

* New Feature: Option to re-add the user card on the WP Toolbar.
* New Feature: Icon and Font-size option added to the WP Toolbar.
* Improvement: Plugin now compiles whenever a new blog is added to a network.
* New Feature: Plugin now detects possible permission errors and notify users to fix them.
* Fix on Parallax Block height and bug.

Version 0.0.25

* Fixed: Support to WordPress 4.5 Coleman
* Fixed: Solid color not displaying in mobile views

Version 0.0.24

* Fixed: Imcompatibility with Desktop Server by ServerPress
* Fixed: Path problem causing issues in some ajax calls

Version 0.0.23

* Fixed: imcompatibility in the menu of Iron Music
* Fixed: Issue with other plugins that change the admin bar
* Added: Ripples of Material Design in all possible elements
* Fixed: Issues with Font Awesome not being loaded
* Fixed: Dynamic styles are no longer loaded in the frontend

Version 0.0.22

* New Feature: the possibility of not enqueueing the styles to the login page.
* Bugfix: Now the compiler runs after blog creation as well

Version 0.0.21

* New Feature: The menu editor now lets you hide the items you don't want to see, with just one click!

Version 0.0.20

* Improvements in the Dynamic styles compile process
  * Fixed: bug of multiple blogs in network using Material WP
* Fixed: incompatibility with WP-Client.

Version 0.0.19

* HUGE PERFORMANCE IMPROVEMENTS:
  * Now Material WP caches the dynamic CSS styles to reduce the load times. Performance was enhanced by a factor of 3.
* New Feature: Hide Help and Screen Options Tab

Version 0.0.18

* RTL Support Added

Version 0.0.17

* Fixed: Modal dropboxes bug
* New Feature: Hook added to allow developers to change the background image of the parallax block
* Fix in the internationalization code: It now works
* pt_BR added. If you want to contribute with the translation of Material WP, send me an email

Version 0.0.16

* Fixed: little style incompatibility with MailPoet
* Fixed: Admin bar height ajusted in the frontend
* New Feature: Remove the opacity trasition in the parallax block
  * Note: This may require you to re-select the color you want to use in the parallax block.
* New Feature: Display only a color block in parallax block

Version 0.0.15

* Fixed: Bug in margins in the themes.php when only one theme is installed
* New default logo for the plugin

Version 0.0.14

* Fixed: Some corrections on the news brought by WordPress 4.4
* Fixed: Text color on button primary changed

Version 0.0.13

* Fixed: Custom height of adminbar leaking to the frontend even when frontend styles were disabled.
* Fixed: Removed a unecessary piece of code that was causing bugs with OptionsTree and Admin Menu Editor Pro.

Version 0.0.12

* Fixed: Missing points in the encapsulated process of Titan Framework.
* Fixed: WordPress Social Login plugin, Tabs being hidden.
* Fixed: Incompatibilities with FormCraft plugin.
* Fixed: Incompatibilities with Bookly

Version 0.0.11

* Fixed: little incompatibility with WP Admin Menu Manager
* Fixed: problems with Custom Sidebars from WPMUDEV
* Fixed: problems with Real Media Library

Version 0.0.10

* Fixed: removed shortcut PHP definition (<?php instead of <?) in one of the files to prevent fatal errors in environments where the PHP don't support it.

Version 0.0.9

* LONG WAITED CHANGE: MULTISITE SUPPORT
  * Now when "Network Active" Material displays the options menu only in the network admin, letting you choose global settings that will be applied to every blog in the network.
* New: Ability to change the link in the logo in the login page.
* New: Ability to display or hide the "Back to Blog" link in the login.

Version 0.0.8

* HUGE UPDATE: The block system used in the theme was replaced from .wrap (which is recomended by WordPress but devs often don't use - what was causing a number of plugin incompatibilities) to #wpbody-content. With that change, Material WP is now virtually compatible with all WordPress plugins. (But if you spot something spooky, just send us a message as always).
* New: Change the height of the admin bar as well as its subitems
* Encapsulated Titan Framework to avoid conflict with other plugins and theme using it

Version 0.0.7

* New: Ability to disable menu editing
* New: Change or Hide the menu label ("Main Menu" text over the admin menu)
* New: Ability to use the default admin bar in the frontend
* New: Ability to disable random color in the admin menu icons and setting your own
* New: Our "Happy Buyers Club" Newsletter Link Added
* New: POT files added for plugin translation
* New: Option to position the admin menu on the right
* New Compatibility: WP Clone by WP Academy
* New Compatibility: Google Analytics Dashboard for WP
* New Compatibility: CiviCRM
* New Compatibility: SkyStatus Plugin
* Fixed: Admin Menu Pro Icon Changer

Version 0.0.6

* Fixed: Customizer Bug
* Fixed some compatibility issues with plugins (Now 100% Compatible):
  * Visual Composer and Visual Composer Fullscreen
  * OptinLinks
  * UserPro
  * UberMenu
  * Premium SEO Pack
  * Ultimate Tweaker
  * UpdraftPlus Backup/Restore
  * Layered Popups
  * Admin Menu Editor Pro
  * WordFence
  * ZenCache
  * WP Media Folder
  * Easy Social Share Buttons for WordPress
  * Flow-Flow — Social Streams Plugin
  * Askimet
  * All-in-One WP Migration
  * NestedPages
  * CQPIM WordPress Project Management Plugin

Version 0.0.5

* Fixed: autoupdates displaying update notice without any updates available.
* 100% Compatibility List: MyMail added.
* Admin menu default width is now 280px.

Version 0.0.4

* Active Admin Menu now stays open.
* Fix: Titan Framework leaking custom CSS to the frontend.

Version 0.0.3

* New Customization options added, such as:
  * Custom CSS field (with SCSS support!);
  * Sidemenu width;
  * Parallax block height;
* Update of options framework used
* Fix in the styles of the "inactivity" login modal
* Basic import and Export features using JSON (improvements will be mande in this to allow for media import from external sources and much more)
* Material WP now uses luminosity tests to determine with text color to use on buttons and toolbars based on the colors choose by the user!
* Hooks in our framework to allow us to display errors when they occur

Version 0.0.2

* Only loads styes in frontend when logged (wpadminbar is shown).
* Removed some extra CSS enqueued to the frontend that could cause conflicts with frontend themes.
* Custom logo src url fix.
* Fix in some custom icons not showing up.

Version 0.0.1 - Initial Release on CodeCanyon
