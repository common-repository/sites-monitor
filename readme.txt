=== Sites Monitor ===
Author: Verdant Studio
Author URI: https://www.verdant.studio
Contributors: verdantstudio
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 6.0
Requires PHP: 7.4
Stable tag: 1.7.4
Tags: manage multiple sites, maintenance, dashboard, performance, monitor
Tested up to: 6.6

Effortlessly monitor your websites from your own WordPress installation. Keep an eye on updates, site health, WP versions, and more.

== Description ==

Monitor your WordPress websites from a single WordPress installation and keep track of plugin and WordPress version updates, storage, users, maintainers, clients, important notes and so on. You are completely in charge of your data.

== How it works ==

Configure a WordPress website as the receiving party (the monitor) and have your public websites synchronize metrics towards it with an interval of your own choosing. Read our [Getting Started guide](https://www.verdant.studio/documentation/sites-monitor/getting-started/) for more information.

== Is this plugin for me? ==

This plugin is for you if you prefer to update your WordPress installations directly and want to keep access to your sites restricted.
Sites Monitor helps you organize your websites, communicate with your clients, and track your sites visually, all without requiring access to any of your sites.

== Free Features ==

* <strong>Watch WP version:</strong> view the currently installed and latest available WordPress version
* <strong>Watch plugin updates:</strong> quickly see which plugins are out of date and what their latest version is
* <strong>Watch site health:</strong> see the general score of the WP Site health
* <strong>Watch directory sizes:</strong> see the directory sizes of a site, database, uploads and so on
* <strong>Watch user count:</strong> see how many users a website has
* <strong>Sort and filter:</strong> sort and filter the list of sites by name, version, plugin status and site health
* <strong>Search:</strong> lots of sites? no problem! use the search to narrow results
* <strong>Insights:</strong> scan your website using multiple popular services for insights
* <strong>Toggle features:</strong> decide what information to display on the general overview
* <strong>Privacy focused:</strong> be in charge of your website's data, there is no sharing with any third party
* <strong>Unlimited sites!</strong> connect as many sites as you want to the data receiving (monitor) site

== Premium Features ==

* <strong>Clients:</strong> add clients/organizations to a site for easy recognition
* <strong>Maintainers:</strong> add maintainers to a site so that your team knows who is responsible for maintaining the site
* <strong>Notes:</strong> keep track of things by adding notes to your sites
* <strong>Send e-mail notifications to clients:</strong> e.g. when you've updated their site(s)
* <strong>Premium support:</strong> contact us for assistance

== Translation Ready ==

Speak another language? You can help make Sites Monitor available in your language! Join our [translation project](https://translate.wordpress.org/projects/wp-plugins/sites-monitor/) on WordPress.org and contribute to translating the plugin for the global community.

== Documentation ==

You can find the [documentation](https://www.verdant.studio/documentation/sites-monitor/) on our site.

== Installation ==

1. Upload `sites-monitor` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Follow the [Getting Started guide](https://www.verdant.studio/documentation/sites-monitor/getting-started/).

== Frequently Asked Questions ==

= Where can I find the documentation? =

You can find the documentation at: [verdant.studio/documentation/sites-monitor](https://www.verdant.studio/documentation/sites-monitor/)

= Does this plugin affect performance? =

Sites Monitor only uses a few API's and sends a minimal amount of data to the site which acts as the monitor. The monitor site itself does not need to fetch or crawl data. You can also configure at what interval the data is synchronized.

= My question is not listed here =

You can find our complete FAQ at: [verdant.studio/documentation/sites-monitor/faq](https://www.verdant.studio/documentation/sites-monitor/faq/)

== Screenshots ==

1. List all sites in the Sites Monitor
2. View detailed information about a site
3. Sites Monitor premium adds even more functionality
4. Turn features on and off in the general overview

== Changelog ==

= 1.7.4: Oct 27, 2024 =

* Change: update dependencies
* Fix: only enable gutenberg blocks when site is of type monitor

= 1.7.3: Sep 25, 2024 =

* Change: update dependencies

= 1.7.2: Aug 21, 2024 =

* Fix: improve type check before sending data

= 1.7.1: Jul 18, 2024 =

* Fix: consistency of buttons on the detail view
* Fix: consistency of input fields on the detail view
* Fix: apply fixes of Freemius latest sdk
* Change: health text to site health

= 1.7.0: Jun 27, 2024 =

* Add: scan your website using multiple popular services for insights
* Change: remove list items from error tooltip for better styling

= 1.6.2: Jun 22, 2024 =

* Fix: return correct status code when email sending fails due to missing data
* Fix: under some circumstances certain variables were not set

= 1.6.1: Jun 21, 2024 =

* Fix: omit certain dist files

= 1.6.0: Jun 21, 2024 =

* Change: styling of plugins panel
* Change: styling of error components both on the front and admin
* Change: replace phpdoc params with php types

= 1.5.0: May 16, 2024 =

* Add: search is now a free and default feature
* Change: move scripts to viewScript and make sure assets are loaded on demand
* Change: remove project translations and use translate.wordpress.org instead
* Change: enforce blocks to be added only once per page

= 1.4.0: Apr 18, 2024 =

* Add: a getting started widget to the admin settings
* Add: a cron status widget to the admin settings when type is site
* Change: update site links to verdant.studio (new site)
* Change: update npm deps, blocks to v3 and improve webpack build

= Earlier versions =

For the changelog of earlier versions, please refer to [the changelog on verdant.studio](https://www.verdant.studio/documentation/sites-monitor/changelog/).
