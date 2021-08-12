/*!
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
(function () {
    angular.module('piwikApp').controller('ManagecustomalertsController', ManagecustomalertsController);

    ManagecustomalertsController.$inject = ['piwik', 'piwikApi', '$timeout'];

    function ManagecustomalertsController(piwik, piwikApi, $timeout) {
        // remember to keep controller very simple. Create a service/factory (model) if needed

        var self = this;
        this.isLoading = false;
        this.isLoadingReport = false;
        this.showReportConditionField = false;
        this.reportOptions = [];
        this.metricOptions = [];

        this.metricDescription = '%';
        this.hasReportDimension = false;
        this.isComparable = false;

        this.actualReportMetadata = null;

        var reportValuesAutoComplete = null;

        this.changeReport = function () {
            this.isLoadingReport = true;

            reportValuesAutoComplete = null;

            piwikApi.fetch({
                method: 'API.getReportMetadata',
                date: piwik.currentDateString,
                period: self.alert.period,
                idSites: [this.alert.site.id],
                filter_limit: '-1'
            }).then(function (data) {
                renderForm(data);
                self.isLoadingReport = false;
            }, function () {
                self.isLoadingReport = false;
            });
        };

        function isBlockedReportApiMethod(apiMethodUniqueId) {
            return 'MultiSites_getOne' == apiMethodUniqueId || 'MultiSites_getAll' == apiMethodUniqueId;
        }

        function updateMetrics(metrics) {
            self.metricOptions = [];
            if (metrics) {
                for (var metric in metrics) {
                    if (!self.alert.metric || !metrics[self.alert.metric]) {
                        self.alert.metric = metric;
                    }

                    self.metricOptions.push({key: metric, value: metrics[metric]});
                }
            }
        }

        function renderForm(data) {

            var options = [];

            self.actualReportMetadata = null;

            for (var i = 0; i < data.length; i++) {
                var reportMetadata = data[i];
                var reportApiMethod = reportMetadata.uniqueId;

                if (isBlockedReportApiMethod(reportApiMethod)) {
                    continue;
                }

                if (!self.alert.report) {
                    self.alert.report = reportApiMethod;
                }

                options.push({key: reportApiMethod, value: reportMetadata.name, group: reportMetadata.category});

                if (reportApiMethod == self.alert.report) {
                    updateMetrics(reportMetadata.metrics);

                    self.actualReportMetadata = reportMetadata;

                    if (reportMetadata.dimension) {
                        self.hasReportDimension = true;
                        $('.reportInfo').text(data[i].dimension);
                    } else {
                        self.hasReportDimension = false;
                    }
                }
            }

            self.reportOptions = options;
            self.changeMetricCondition();
        }

        function getApiParameters() {

            var apiParameters = {};
            apiParameters.format = 'json';
            apiParameters.name = self.alert.name;
            apiParameters.metric = self.alert.metric;
            apiParameters.metricCondition = self.alert.metricCondition;
            apiParameters.metricValue = self.alert.metricValue;
            apiParameters.emailMe = self.alert.emailMe ? 1 : 0;
            apiParameters.additionalEmails = (self.alert.additionalEmails && self.alert.additionalEmails.length) ? self.alert.additionalEmails : [''];
            apiParameters.phoneNumbers = self.report && self.report.phoneNumbers ? self.report && self.report.phoneNumbers : [''];
            apiParameters.reportUniqueId = self.alert.report;
            apiParameters.reportCondition = self.alert.reportCondition;
            apiParameters.reportValue = self.alert.reportValue;
            apiParameters.idSites = [self.alert.site.id];
            apiParameters.comparedTo = self.alert.comparedTo[self.alert.period];

            return apiParameters;
        };

        function isValidAlert(alert) {

            if (!$.isNumeric(alert.metricValue)) {
                var UI = require('piwik/UI');
                var notification = new UI.Notification();
                var options = {id: 'CustomAlertsMetricValueError', context: 'error', type: 'toast'};

                notification.show(_pk_translate('CustomAlerts_InvalidMetricValue'), options);
                notification.scrollToNotification();
                $('#metricValue').addClass('invalid');
                return false;
            }

            $('#metricValue').removeClass('invalid');
            return true;
        }

        this.changeMetricCondition = function () {
            var condition = this.alert.metricCondition;
            var metric = this.alert.metric;

            var isPercentageCondition = condition && 0 === condition.indexOf('percentage_');
            var isPercentageMetric = metric && -1 !== metric.indexOf('_rate');
            var isSecondsMetric = metric && -1 !== metric.indexOf('_time_');
            this.isComparable = condition && -1 !== condition.indexOf('_more_than');

            $('[name="metricValue"]').attr('title', '');

            if (isPercentageCondition || isPercentageMetric) {
                $('.metricValueDescription').text('%');
            } else if (isSecondsMetric) {
                $('.metricValueDescription').text('s');
            } else {
                $('.metricValueDescription').text(_pk_translate('General_Value'));
            }
        }

        function sendApiRequest(method, POSTparams) {
            self.isLoading = true;

            var period = self.alert.period;

            piwikApi.post({period: period, method: method}, POSTparams).then(function () {
                piwik.helper.redirect({module: 'CustomAlerts', action: 'index'});
                self.isLoading = false;
            }, function () {
                self.isLoading = false;
            });
        }

        this.createAlert = function () {
            var apiParameters = getApiParameters();

            if (!isValidAlert(apiParameters)) {
                return false;
            }

            sendApiRequest('CustomAlerts.addAlert', apiParameters);
        };

        this.updateAlert = function (idAlert) {
            var apiParameters = getApiParameters();
            apiParameters.idAlert = idAlert;

            if (!isValidAlert(apiParameters)) {
                return false;
            }

            sendApiRequest('CustomAlerts.editAlert', apiParameters);
        };

        this.deleteAlert = function (idAlert) {

            function onDelete() {
                piwikApi.fetch({
                    method: 'CustomAlerts.deleteAlert',
                    idAlert: idAlert
                }).then(function () {
                    piwik.helper.redirect();
                });
            }

            piwikHelper.modalConfirm('#confirm', {yes: onDelete});
        };

        function getValuesForReportAndMetric(request, response) {

            var metric = self.alert.metric;

            function sendFeedback(values) {
                var matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term), "i");
                response($.grep(values, function (value) {
                    if (!value) return false;

                    value = value.label || value.value || value[metric] || value;
                    return matcher.test(value);
                }));
            }

            if (angular.isArray(reportValuesAutoComplete)) {
                sendFeedback(reportValuesAutoComplete);
                return;
            }

            reportValuesAutoComplete = [];

            var report = self.actualReportMetadata;

            if (!report) {
                return;
            }

            var apiModule = report.module;
            var apiAction = report.action;

            if (!metric || !apiModule || !apiAction) {
                sendFeedback(reportValuesAutoComplete);
            }

            piwikApi.fetch({
                method: 'API.getProcessedReport',
                date: 'yesterday',
                period: 'month',
                disable_queued_filters: 1,
                flat: 1,
                filter_limit: -1,
                showColumns: metric,
                apiModule: apiModule,
                apiAction: apiAction,
                idSite: self.alert.site.id,
                format: 'JSON'
            }).then(function (data) {
                if (data && data.reportData) {
                    reportValuesAutoComplete = data.reportData;
                    sendFeedback(data.reportData);
                } else {
                    sendFeedback([]);
                }
            }, function () {
                sendFeedback([]);
            });
        }

        $timeout(function () {
            $('.alerts #reportValue').autocomplete({
                source: getValuesForReportAndMetric,
                minLength: 1,
                delay: 300
            });
        }, 1000);
    }
})();
