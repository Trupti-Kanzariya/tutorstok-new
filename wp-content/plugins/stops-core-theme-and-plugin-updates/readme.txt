=== Easy Updates Manager ===
Contributors: davidanderson, kidsguide, ronalfy, roary86, bigwing, webulous
Tags: updates manager, easy updates manager, disable updates manager, disable updates, update control, plugin updates, theme updates, core updates, automatic updates, multisite, logs
Requires at least: 5.1
Requires PHP: 5.6
Donate link: https://easyupdatesmanager.com
Tested up to: 6.8
Stable tag: 9.0.19
License: GPLv2 or later

Manage all your WordPress updates, including individual updates, automatic updates, logs, and loads more. This also works very well with WordPress Multisite.

== Description ==
Easy Updates Manager is a light yet powerful plugin that allows you to manage all kinds of update- both on your single site install, or in WordPress Multisite. With a huge number of settings for endless configuration, Easy Updates Manager is an obvious choice for anyone wanting to take control of their website updates.

https://vimeo.com/288535460

= Free Features Include =
<ul>
<li>Disable all updates with one click</li>
<li>Enable automatic updates with one click</li>
<li>Deeply customize your automatic update settings</li>
<li>Use logs to determine what and when something is updated</li>
<li>Hide plugin and theme updates (if custom developed)</li>
<li>Select which plugins and themes can be automatically updated</li>
<li>Disable core, plugin, theme, and translation updates</li>
<li>Force Updates to check that automatic updates are working</li>
<li>Integrate with <a href="https://updraftplus.com/">UpdraftPlus</a></li>
<li>Configure email notification settings</li>
<li>And much more…</li>
</ul>

= Go Premium =

Visit <a href="https://easyupdatesmanager.com">easyupdatesmanager.com</a> to upgrade to our premium features.

https://vimeo.com/289883791

Premium features include:
<ul>
<li>Safe mode: ensures updates are compatible with your WordPress version and your server's PHP version</li>
<li>Schedule for automatic updates: runs the updates when you want them to avoid any downtime</li>
<li>External logging - Get alerts when new updates have been logged</li>
<li>Anonymization - Control what is sent to the WordPress API</li>
<li>Delayed updates - Delay automatic updates in the case of hotfixes and frequent releases</li>
<li>Auto backups: integrates with <a href="https://updraftplus.com/">UpdraftPlus</a> and take a backup before your site is updated</li>
<li>UpdraftCentral: fully integrates with <a href="https://updraftplus.com/updraftcentral/">UpdraftCentral</a></li>
<li>Log clearing schedule: keeps your logs table under control and deletes entries according to your own schedule</li>
<li>Import/Export settings: exports your settings from one site to another for quick configuration</li>
<li>Email notification of updates: gives you weekly or monthly reports of pending updates </li>
<li>White label: disables notices and customizes what the client sees</li>
<li>Check plugins: runs a check of plugins that have been removed from the WordPress Plugin Directory</li>
<li>Webhook: integrates with third-party services to run automatic updates via cron or even Zapier</li>
<li>Export logs: exports logs for printing, or downloads a CSV or JSON file for a date range</li>
<li>Version control protection: prevent updates from occurring to plugins and themes under version control</li>
</ul>

> For more information on Easy Updates Manager Premium, <a href="https://easyupdatesmanager.com">check out our website at easyupdatesmanager.com</a>.

= Translations =

If you want to contribute to the translation, <a href="https://translate.wordpress.org/projects/wp-plugins/stops-core-theme-and-plugin-updates">please visit our translation section</a>. We appreciate all the translation help we can get.

== Screenshots ==
1. Single site install settings page location
2. Multisite install settings page location
3. General Tab with main options
4. Plugins tab with individual options allowed
5. Themes tab with individual options allowed
6. Logs feature
7. Premium logs feature
8. Advanced tab
9. Premium advanced tab


== Installation ==
<strong>Installing Easy Updates Manager in your WordPress Dashboard</strong> (recommended)

1. You can download Easy Updates Manager by going into your 'Plugins' section and selecting add a new plugin
2. Search for the plugin by typing 'Easy Updates Manager' into the search bar
3. Install and activate the plugin by pushing the 'Install' button and then the 'activate plugin' link
4. Configure the plugin by going to the 'Updates Options' section in your admin area under the Dashboard

<strong>Installing Easy Updates Manager through FTP</strong>

1. Upload the Easy Updates Manager folder to the '/wp-content/plugins/' directory (make sure it is unzipped)
2. Activate the Easy Updates Manager plugin through the 'Plugins' menu in WordPress
3. Configure the plugin by going to the 'Update Options' section in your admin area under the Dashboard

Activate the plugin after installation.  If you are on Multisite, the plugin must be network-activated.

For additional information for Easy Updates Manager check out our website at <a href="https://easyupdatesmanager.com">easyupdatesmanager.com</a>.

== Frequently Asked Questions ==
= Do you support older WordPress versions? =

Version 6.x.x should support WP versions 4.0 and up. Version 4.7.0 should support WP versions 2.3 - 4.0.

Since WordPress is constantly changing, this plugin will always strive to support the latest version of WordPress. As there are many security vulnerabilities reported, please always keep WordPress as up-to-date as possible.

Unfortunately, we do not support older versions of WordPress or the non-current release of this plugin.

= How do I change the WordPress update notification e-mail? =

Go to the General tab in our settings. You can enter a single email address, or multiple if you comma-separate them.

= Automatic Updates =

Check out our video on how the automatic updating works in WordPress.

https://vimeo.com/291084023

= How Do I Enable Logging? =

Check out our video on how logs work.

https://vimeo.com/291084061

= Does Easy Updates Manager work with third-party plugins and themes? =

Since third-party providers use custom update mechanisms, we cannot always guarantee that they will work with Easy Updates Manager. Most will, but there are a number of common mistakes that plugin authors can make.

= Additional Information and FAQ =

For additional information and FAQs for Easy Updates Manager <a href="https://easyupdatesmanager.com">check out our website</a>.

== Changelog ==

= 9.0.19 - 2024-11-12 =

* FIX: Misreporting of plugin's update statuses in notification emails, which were labeled "success" instead of the appropriate designation of "failed"
* TWEAK: Improve the strategy for using an associative array to filter option default values, aiming to safeguard existing defaults from removal and to prevent the introduction of non-relevant options
* TWEAK: The notification email should omit any "failed" update status for plugins, themes and translations if they lack the corresponding name and/or version number
* TWEAK: Improve updates-check anonymisation and randomisation, stripping out further unnecessary data
* TWEAK: Replace unnecessary calls to maybe_unserialize()

= 9.0.18 - 2024-07-10 =

* FIX: Couldn't reactivate plugins due to plugin dependency issues (Premium)
* TWEAK: Allow auto-updates event from other plugins to take place only if it's in the same exact schedule with the auto-updates scheduling feature
* TWEAK: Auto-updates should always run at the actual time the user has set it to be and prevent auto-updates event that runs outside the auto-updates scheduling feature
* TWEAK: Fixed spelling errors in the repo
* TWEAK: Force automatic updates can now be performed without the user being given full update capabilities. Prior to this, in order to successfully force the automatic updates the user had to have core, plugin and theme capabilities  
* TWEAK: Force automatic updates should be independent and should not depend on the user-defined settings which can lead to the auto-updates itself being failed
* TWEAK: Split multiple sentences into separate translation calls. 
* TWEAK: Update the composer package yahnis-elsts/plugin-update-checker for PHP 8.2 compatibility
* TWEAK: Plugin reactivations are now carried out in a specific sequence, with plugins lacking dependencies being reactivated first, followed by those with dependencies (Premium)

= 9.0.17 - 2023-08-09 =

* TWEAK: Do not show incorrect information regarding plugins which were no longer maintained by the author caused by Premium plugins which don't set all the field values seen with wordpress.org
* TWEAK: Include thorough version numbers and URL information in the auto-updates notification email

= 9.0.16 - 2023-05-22 =

* TWEAK: (Premium) New ability of the automatic update scheduling feature where now the user can individually select what days are allowed or not allowed for updates to be run on the 3, 6, 12 hours and daily schedule settings
* FIX: MPSUM_Logs::normalise_call_stack_args() would overwrite any arguments that were passed by reference (pointer variables). thus resulting in misleading or seriously wrong data

= 9.0.15 - 2023-04-13 =

* TWEAK: Improve MPSUM_Logs::normalise_call_stack_args() method to reduce the size of call stacks being stored in the database, also to prevent silent termination due to size of the call stacks that swell
* TWEAK: Get rid of PHP 8.1+'s "automatic conversion of false to array is deprecated" message when first time activating the plugin
* TWEAK: Add a user capabilities check when receiving a request through the `wp_ajax_eum_ajax` handler
* TWEAK: Prevent PHP8+ ErrorException and/or suppress PHP notice/warning for undefined stdClass::$plugin and/or stdClass::$theme variables during auto-updates (this usually happens due to the plugin or theme doesn't follow WordPress standards)
* TWEAK: Remove notices regarding WP_AUTO_UPDATE_CORE and AUTOMATIC_UPDATER_DISABLED constants from force automatic updates screen as they now won't prevent automatic updates from being run
* TWEAK: Get rid of PHP deprecated message on PHP 8.1+ when presenting admin constant notices by removing sprintf() function that shouldn't had wrapped the esc_html_e() function
* TWEAK: All updates settings should be compatible and compliant with WordPress Site Health
* TWEAK: Prevent making a nonce available to logged-in users who could not manage Easy Updates Manager (no impact as all actions (dismissal of notices) were unreachable without proper user capability)
* FIX: Wrong parameters order in PHP implode() function calls for showing a list of plugins and/or themes that are under version control
* FIX: Some fields were not cleared when switching from one auto-update schedule time to another

= 9.0.14 - 2022-10-24 =

* TWEAK: Add admin notice for DISABLE_WP_CRON constant and remove notices regarding WP_AUTO_UPDATE_CORE and AUTOMATIC_UPDATER_DISABLED constants as they now won't prevent automatic updates from being run
* FIX: Core minor updates became major when two updates-related scheduled events got triggered in the same process
* FIX: In some cases the serialisation of call stack could cause a PHP fatal error due to the output of a backtrace (debug_backtrace) containing 'Closure' (anonymous) functions
* TWEAK: Update notice class

= 9.0.13 - 2022-06-07 =

* TWEAK: Update logs are sent separately causing a huge number of emails being sent to the user (Premium)
* TWEAK: Override potential constants and filters that might be used by other plugins for disabling WordPress automatic updater
* TWEAK: Prevent other plugins from overriding the 'auto_update_core' filter when 'Manually update' or 'Auto update all minor versions' core setting is selected
* FIX: The logs option/setting got stuck in 'off' value when upgrading from old versions (prior to 8.1.0) to the recent versions
* FIX: When plugin notification emails setting is disabled but core notification setting is enabled, the user would still receive a notification email regarding plugin updates
* FIX: Redundant emails were sent when core notification setting is enabled
* TWEAK: Improve the notification settings by adding two new options for enabling/disabling theme and translation notification emails
* FIX: In any update operation (manual or automatic), translation update was automatically triggered when 'Manually update' or 'Disable auto updates' is chosen
* TWEAK: Remove 'Disable auto updates' setting as it's the same as 'Manually update'
* FIX: '0.00' version numbers were logged when upgrading plugins and/or theme, at the same time the information about core update wasn't presented in the logs table, some warnings and WordPress database errors were logged in the PHP error log file too
* FIX: Users with very limited access were recorded in the logs table
* FIX: Failure status was recorded in the log entry and the 'From' and 'To' versions are in the same version number, though the update process was successful
* FIX: Unneeded and misleading log entry when upgrading to minor versions
* FIX: Incorrect WordPress version number was logged after upgrading to a minor version
* TWEAK: Refactor Log classes to improve how log reporting is handled during updates
* TWEAK: Update seasonal notices

= 9.0.12 - 2021-12-17 =

* FEATURE: Semantic versioning feature that when enabled it will allow only patch/security release updates for plugins and/or themes
* FIX: The log table didn't get updated when upgrading from the very old table versions (1.0.0 and 1.1.3) to the current latest (1.1.5)

= 9.0.11 - 2021-12-08 =

* FIX: Change the order (priority) of auto_update_plugin/theme filters to make the 'Safe Mode' feature (Premium) work properly
* TWEAK: Improve the way errors are handled before and after a plugin activation takes place (deactivated plugins must be reactivated immediately regardless of whether or not the plugins contain a fatal error)
* TWEAK: Change use of '$' to local/function scope, to prevent conflicts

= 9.0.10 - 2021-09-28 =

* FIX: Inconsistent behaviour of the 'manually update' option of the plugin updates setting (it updated plugins to their major/minor version that shouldn't have happened)
* FIX: Problematic plugins that cause a PHP fatal error after their automatic updates were downloaded and installed don't get deactivated properly
* FIX: The email report that falsely reports successful plugin reactivations
* FIX: Make sure auto update status in the updates screen UI page always reflect the change made from the EUM updates settings page
* FIX: Unformatted string notice appears on the admin dashboard
* TWEAK: Simplify 'WordPress core updates` setting by eliminating redundant or unnecessary options whilst preserving user preferences
* TWEAK: Change the misleading checkbox text label of the WordPress core updates setting that has led to confusion
* TWEAK: Override WordPress core auto-updates settings by not showing it in the updates screen UI (wp-admin/update-core.php)

= 9.0.9 - 2021-06-12 =

* FEATURE: Add the ability to disable update notification emails completely when a plugin updates automatically
* TWEAK: Added Update URI header field to avoid accidentally being overwritten with an update of a plugin of a similar name from the WordPress.org Plugin Directory.
* TWEAK: Added stacktrace column to the logging table to log a PHP stack trace
* TWEAK: Correct misnamed array key when migrating theme options
* TWEAK: Prevent a couple of PHP undefined variable log notices upon de-installation
* TWEAK: Prevent a potential unwanted PHP debugging notice in MPSUM_Disable_Updates::http_request_args_remove_plugins_themes()
* TWEAK: Update seasonal notices

= 9.0.8 - 2021-03-08 =

* TWEAK: Correctly log 'from' version for themes when scheduled update is run.
* TWEAK: Adjust a method definition that caused a PHP notice in PHP 8
* TWEAK: Escape existing super admin usernames in SQL query to avoid code notice
* TWEAK: Adjust escaping method used for an SQL function (not believed to have any security implications)

= 9.0.7 - 2020-12-17 =

* TWEAK: Update jQuery document ready style to the one not deprecated in jQuery 3.0
* TWEAK: Bump PHP requirement to 5.6+
* TWEAK: Renamed UpdraftCentral's command classes filter
* TWEAK: extend white labelling to include safemode warning notices, webhook responses and WP 5.5's new "automatic upgrades" user-interface additions
* TWEAK: Removed MetaSlider notice in the notices collection
* TWEAK: An install was seen in which an interaction with some other component caused excessive logging
* TWEAK: Updating wording to be constant throughout EUM.
* TWEAK: Manual core update showing correct to and from versions.
* TWEAK: Updating wording to be constant throughout EUM.
* FIX: Auto-updates will trigger on managed hosts that disable version checking.

= 9.0.6 - 2020-08-10 =

* FIX: Fatal error in a template file

= 9.0.5 - 2020-08-10 =

* TWEAK: Some minor code improvements based on PHPCS analysis
* TWEAK: Updated seasonal notices
* TWEAK: Add WP 5.5 support. Since EUM's update management facilities are much more sophisticated than WP 5.5's new "automatic upgrades" user-interface additions, the new WP 5.5 options do not map simply onto existing EUM options. Thus the only way they can work together is if EUM replaces those additions with links back to the EUM controls.

= 9.0.4 - 2020-04-27 =

* FEATURE: Allow "every 3 hours" and "every 6 hours" options for the update frequency checks

= 9.0.3 - 2020-04-14 =

* TWEAK: Update class Updraftplus_Notices
* TWEAK: Update WP-Optimize notices
* TWEAK: Updater will now make checks on availability without needing login
* TWEAK: Minimum supported WP version is now 5.1. If you want to install on an older version, then please use a past release. The resources used for supporting older versions are better deployed elsewhere - the aim of EUM is to help you keep up-to-date!

= 9.0.2 - 2020-01-24 =

* FIX: Auto-backup (Premium) feature disabled whilst an issue that could cause it to continually repeat is investigated

= 9.0.1 - 2020-01-21 =

* FIX: (Premium feature) UpdraftPlus will only take one backup during the auto-update process.
* FIX: Update translations after an auto-update has completed.

= 9.0.0 - 2020-01-15 =

* FEATURE: Admin user interface has been cleaned up, providing more straightforward options.
* FEATURE: (Premium) Check for unmaintained plugins.
* TWEAK: Constants can now be used to disable the outdated browser warning (EUM_ENABLE_BROWSER_NAG), the WordPress version in the footer (EUM_ENABLE_WORDPRESS_FOOTER_VERSION), and the ratings prompt on the General screen (EUM_ENABLE_RATINGS_NAG).
* FIX: Prevent Force Updates from deactivating plugins.
* FIX: (Premium feature) UpdraftPlus will now take a backup during an auto-update
* FIX: (Premium feature) Fix cron schedules so they are run at the correct time.

= 8.2.0 - 2019-10-30 =

* FEATURE: (Premium) Safe mode now checks themes for compatibility.
* FEATURE: (Premium) Version controlled assets now show in the EUM plugins and themes tabs. 
* FIX: Delayed updates were delaying automatic updates to WordPress core when no delay was requested
* TWEAK: Divi theme can now be auto-upgraded.
* TWEAK: Bump required WP version from 4.6 to 4.7. We've not introduced anything to make it incompatible so it will likely still work; but this is the support requirement.

= 8.1.1 - 2019-10-23 =

* FIX: Fixed uninstall script error when deleting the plugin.


= 8.1.0 - 2019-10-08 =

* FEATURE: Notes section added to log to show why an automatic update failed.
* FEATURE: (Premium) Adding version control protection so that version controlled plugins or themes will not be updated.
* FIX: Fixed saving error when toggling auto-update on individual themes
* FIX: Don't wipe settings when removing the free version, if premium is installed. Or vice versa.
* FIX: Enabling/disabling admin bar was resetting General options.
* FIX: Disabling Core updates will no longer block other automatic updates.
* FIX: Translation updates are run after automatic updates have completed.
* FIX: Translation updates now show the correct label.
* FIX: (Premium) Slack logging now shows the site name from where the event came from.
* TWEAK: UI Fix: Prevent notices about EUM-Premium from appearing in the premium version of the plugin
* TWEAK: Do not allow null values to be passed to an INSERT on the version_from field in the log table
* TWEAK: Add some missing translation domains
* TWEAK: Code-styling tweak to avoid use of extract()
* TWEAK: Adding dashboard notice if automatic updates are disabled through constants.
* TWEAK: Prevent unnecessary PHP notice when controlling via UpdraftCentral
* TWEAK: Database logging is now always turned on, to aid troubleshooting. (The storage overhead is tiny, since updates are infrequent events compared with other things going on in a WP database).
* TWEAK: Update updater class to latest series (1.8)
* TWEAK: Automatic update emails are only sent once every twenty four hours.

= 8.0.5 - 2019-03-28 =

* SECURITY: Correct an erroneous nonce (intent) check. The impact of this is that a logged-in user with access to the WP dashboard was able to change some (but not all, as some were protected by further checks) options (e.g. disable unattended theme updates).
* TWEAK: Add site URL in slack message
* TWEAK: Preview the premium features in the advanced tab
* TWEAK: Lengthen the dismiss time on the "Thank you for installing" notice
* TWEAK: Change filter priorities to minimise the chance that something else over-rides EUM's settings when WP inquires about running an auto-update

= 8.0.4 - 2019-01-18 =

* FIX: Prevent a conflict between EUM Premium and UpdraftPlus Premium when backing up WordPress core
* TWEAK: No longer use WP_DEBUG constant to indicate rapid refresh of updates when in admin area; instead, use EASY_UPDATES_MANAGER_DEBUG

= 8.0.3 - 2018-12-04 =

* TWEAK: Added seasonal notices
* FEATURE: Logs are on by default
* FEATURE: Logs are now displayed by default in UpdraftCentral
* FEATURE: (Premium) White label settings can now be exported and imported

= 8.0.2 - 2018-11-02 =

* FEATURE: Can disable the admin bar in Advanced settings
* FIX: Resolving i18n issues
* FIX: Unknown command error occasionally shows up when clicking on a General option
* FIX: Plugins in rollback functionality showing re-activated when they are not
* FIX: Reset options would fail because of missing $wpdb variable
* TWEAK: Removing error logs for developers with debug on
* TWEAK: General options are grayed out if you disable updates
* TWEAK: Updating screenshots

= 8.0.1 - 2018-10-30 =

* FEATURE: Shows active versions if a plugin or theme is active
* FEATURE: (Multisite) Checks to see if any sites in the network have a plugin or theme installed
* FEATURE: Plugin can now be fully controlled by UpdraftCentral plugin
* FEATURE: (Premium) Scheduled log clearance
* FEATURE: (Premium) Automatic backup before auto update
* FEATURE: (Premium) Automatic update scheduling
* FEATURE: (Premium) Send Anonymous update request or request with random data to protect privacy
* FEATURE: (Premium) Import and Export settings
* FEATURE: (Premium) Introduce Safe Mode for PHP compatibility checks and WordPress version checks
* FEATURE: (Premium) Logs of automatic update events can be send to external channels such as slack, email, php error log and syslog
* FEATURE: (Premium) Send weekly or monthly emails of update notifications
* FEATURE: (Premium) Webhook to integrate with third-party services
* FEATURE: (Premium) Show plugins that have been removed from the WordPress Plugin Directory
* FEATURE: (Premium) Ability to whitelist the plugin
* FEATURE: (Premium) Ability to search the logs by user or asset name
* FEATURE: (Premium) Ability to export logs for a date range and print them, download a CSV, or download a JSON format
* FEATURE: (Premium) Check for PHP parse errors during automatic updates and de-activate problem problems automatically
* FEATURE: (Premium) Check for plugins de-activated during auto-update and attempt to re-activate them automatically
* FIX: Clicking more details on plugins tab properly shows a modal
* FIX: Force Updates removes a space in the updates panel if there are still updates after it runs
* FIX: Clicking any type of filter in logs shows an error message
* FIX: Add a JS polyfill to provide support for IE11
* FIX: Admin notices show up twice in the options page
* FIX: Plugin/Themes/Log tab were causing an undefined hook suffix error upon save
* FIX: Removing filters in 7.0.3 allowed updates for plugins and themes to be shown
* REFACTOR: Advanced tab settings page refactoring
* TWEAK: Tidy up UI
* TWEAK: Changes in user capability checks
* TWEAK: Mark as supporting WordPress 5.0
* TWEAK: Option to take backup before force updates
* TWEAK: Dashboard notice. Welcome notice now shows our other plugins
* TWEAK: Allow a filter to disable the updates lock option for more reliability in testing
* TWEAK: Easy Updates Manager now displays in the multisite dashboard menu
* TWEAK: Easy Updates Manager now displays in the admin bar
* TWEAK: Force Updates displays more contextual errors if automatic updates are somehow modified or disabled
* TWEAK: Enabling or disabling logs no longer requires a refresh
* TWEAK: Select General Tab if tab is invalid
* TWEAK: Advanced settings adhere to browser history
* TWEAK: Reset Options forces browser to reload for better UX in the Advanced tab
* TWEAK: Force Updates has a better description of what it does
* TWEAK: Clearing logs now clears the logs data upon success
* TWEAK: Logs can now be filtered in ascending or descending order
* TWEAK: Translations now show or hide properly if plugins, WordPress Core, or theme updates are disabled
* TWEAK: Help tab updated
* TWEAK: Email status message shows successful if they are valid emails
* TWEAK: Plugin and Theme options save via Ajax immediately with no save button necessary
* TWEAK: Adding loading animation to General tab to prevent multiple states from occurring at once

= 7.0.3 - 2018-06-26 =

* TWEAK: Disabling themes and plugins no longer disables translations.
* TWEAK: Dashboard notices about other plugins of potential interest now only show on the Easy Updates Manager pages

= 7.0.2 - 2018-06-18 =

* FIX: Removed multiple log entries and version numbers caching issue
* FIX: Fix for headers already sent warning
* TWEAK: Make all settings tabs work via ajax calls
* TWEAK: Show and hide logs tab upon enable and disable
* TWEAK: Add admin notices for insufficient php and wp versions
* TWEAK: Remove flexbox mixin
* TWEAK: Adding survey notice
* TWEAK: Auto refresh page after force updates
* TWEAK: Prevent users from excluding themselves

= 7.0.1 - 2018-03-05 =

* FIX: Enabling Log from Advanced tab does not use default options
* FIX: Plugin settings page is not accessible for excluded users
* TWEAK: Changed settings page to use admin-ajax.php instead of REST API
* TWEAK: Reduced database queries for saving settings options
* TWEAK: Removed the usage tracking code (which was always explicit opt-in)
* TWEAK: Add Welcome dashboard and notices

= 7.0.0 - 2018-01-28 =

* Admin UI overhaul based on past data. It is now very obvious which settings are enabled. Thanks for all the feedback.

For past changelogs, <a href="https://easyupdatesmanager.com/blog/">please visit our blog</a>.

== Upgrade Notice ==

* 9.0.19: Various tweaks and fixes. Improvement of update statuses in the update notification emails and updates-check anonymisation and randomisation. See changelog for full details. A recommended update for all.
