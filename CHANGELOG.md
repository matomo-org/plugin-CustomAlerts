## Changelog

* 6.0.1 - Alerts on the Goals Overview reports that carried `idGoal=0` are moved to the ecommerce order report where every site of the alert has ecommerce enabled, otherwise to the all-goals report. An alert covering sites both with and without ecommerce is therefore evaluated on all-goals figures for every site, including those that were evaluated on ecommerce order figures before. Triggered alerts keep the report they were triggered for. The move depends only on the sites' ecommerce setting, not on whether the Ecommerce plugin is active during the update; while the plugin is inactive, an alert on the ecommerce order report is not evaluated until the plugin is activated again. Those reports can no longer be selected, and the API rejects them
* 6.0.0 - 2026-08-09 - Compatibility with Matomo 6
* 5.3.3 - 2026-07-27 - Added strict comparison check
* 5.3.2 - 2026-06-29 - Added code to improve the access check before sending an alert
* 5.3.1 - 2026-06-08 - Added restrict access check for MultiSites.getAll report for non superusers
* 5.3.0 - 2026-05-11 - Added alert description and helptexts
* 5.2.6 - 2026-04-27 - Updated API documentation
* 5.2.5 - 2026-03-30 - Added escaping for report_matched value
* 5.2.4 - 2026-03-02 - Updated API documentation
* 5.2.3 - 2026-02-05 - Alerts now get deleted when a user's site access is revoked
* 5.2.2 - 2026-01-19 - Added tooltips in add/edit alerts, manage alerts & in the inline text for the delivery method 
* 5.2.1 - 2025-12-08 - Fixes default value showing up as empty entry for alert_mediums
* 5.2.0 - 2025-11-24 - Added ability to send alerts to a Microsoft Teams channel
* 5.1.0 - 2025-09-29 Refactored UI to support different mediums to alert and changes to alert via Slack
* 5.0.7 - 2025-07-07 Textual changes
* 5.0.6 - 2024-09-23 Added check if reports used by alert are done archiving and retry if not archived
* 5.0.5 Combined all the actions into one column
* 5.0.4 Added plugin category for Marketplace
* 5.0.3 Added inline help to mention events filter FAQ
* 5.0.2 Fixed alert conditions not reloading on site change
* 5.0.1 Compatibility with Matomo 5.0.0-b4
* 5.0.0 Compatibility with Matomo 5.0
* 4.1.0 Migrate AngularJS to Vue
* 4.0.4 Fixed autocomplete for country to trigger alerts
* 4.0.3 Grouped alert condition select in UI
* 4.0.2 Improve report selector when defining a custom alert
* 4.0.0 Compatibility with Matomo 4.0
* 3.0.6: Do not pass trigger=archivephp to API method calls.
* 3.0.1: Email alerts: beautiful email design 
* 3.0.0: Compatibility with Piwik 3.0
* 0.1.22: Improved the look of some form elements
* 0.1.21: Compatible with Piwik 2.15.0, fixed: alert number format can be wrong when using percentages 
* 0.1.0: First beta
