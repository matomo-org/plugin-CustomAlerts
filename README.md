# Piwik CustomAlerts Plugin 

[![Build Status](https://travis-ci.org/piwik/plugin-CustomAlerts.png?branch=master)](https://travis-ci.org/piwik/plugin-CustomAlerts) [![Coverage Status](https://coveralls.io/repos/piwik/plugin-CustomAlerts/badge.png?branch=coverage_test)](https://coveralls.io/r/piwik/plugin-CustomAlerts?branch=coverage_test) 

## Description

Create Custom Alerts + Be notified by email/SMS!

Alerts are a great way to get notified of changes on your website. Want to know if your new product hits less than 100 sales in a week or your new article attracts more than 200 visitors a day? Create alerts that make sense to you. Be notified by email or SMS when the conditions for your alerts are met. Stay on top of your website!

The Alert log will help you to better understand the success of your website. You can use it to analyze how often your website hit more than 10000 visits per day or on how many days a product was sold more than 50 times.

This plugin was [crowdfunded with the support](http://crowdfunding.piwik.org/custom-alerts-plugin/) of 37 Piwik community members!

## Installation

Install it via Piwik Marketplace

## FAQ

__What exactly is included in this feature?__

Here is the complete list of features that are included in this project:

* Define new Alert ("Big drop in purchases")
* Select a website on which the Alert is defined
* Receive an alert by email (email will contain Alert description + link to Piwik dashboard URL for the given website ID and period).
* Receive an alert by SMS (SMS will contain Alert description and numbers that triggered the Alert)
* Select the Alert period: should it be daily, weekly or monthly?
* Select the report (Websites, Keywords, Countries, general stats)
* Define Metrics (visits, page view, avg. visit duration, Goal 1 conversions, total goal conversions, etc.)
* Define the Alert: when Visits decrease 50%, when purchases are more than 50 per day, etc.

__What reports are available to the Alert system?__

You can create an alert for any available report in Piwik. Plugins can define new reports which will be automatically picked up by Alerts.

__What alert conditions are available?__

You can create alerts for the following metrics:

* Visits, Visits Evolution, Unique Visits
* Actions, Action Evolution
* Pageviews, Pageviews Evolution
* Time on page
* Bounce rate
* Goal revenue
* Downloads
* and many more..

To define the condition you can select the conditions:

* Greater than, less than
* Equal, Not Equal
* Percentage increase/decrease

## Changelog

* 0.1.22: Improved the look of some form elements
* 0.1.21: Compatible with Piwik 2.15.0, fixed: alert number format can be wrong when using percentages 
* 0.1.0: First beta

## License

GPL v3 or later

## Support

* Please direct any feedback to [hello@piwik.org](mailto:hello@piwik.org)

