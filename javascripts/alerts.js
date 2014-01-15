
var CustomAlerts = (function($) {

    var reportValuesAutoComplete = null;

    function getPeriodValue()
    {
        return $("#period").val();
    }

    function updateFormValues(siteId) {
        if (!siteId || !$.isNumeric(siteId)) {
            siteId = $('[name=idSite]').val();
        }

        reportValuesAutoComplete = null;

        var ajaxRequest = new ajaxHelper();
        ajaxRequest.addParams({
            module: 'API',
            method: 'API.getReportMetadata',
            date: piwik.currentDateString,
            period: getPeriodValue(),
            idSites: [siteId],
            format: 'JSON'
        }, 'GET');
        ajaxRequest.setCallback(function(data) {
            renderForm(data);
        });
        ajaxRequest.setErrorCallback(function () {});
        ajaxRequest.send(false);
    }

    function getValuesForReportAndMetric(request, response) {

        var metric = $('#metric').find('option:selected').val();

        function sendFeedback(values)
        {
            var matcher = new RegExp( $.ui.autocomplete.escapeRegex( request.term ), "i" );
            response( $.grep( values, function( value ) {
                if (!value) return false;

                value = value.label || value.value || value[metric] || value;
                return matcher.test( value );
            }) );
        }

        if ($.isArray(reportValuesAutoComplete)) {
            sendFeedback(reportValuesAutoComplete);
            return;
        }

        reportValuesAutoComplete = [];

        var report = $('#report').find('option:selected')

        if (!report) {
            return;
        }

        var apiModule = report.attr('data-module');
        var apiAction = report.attr('data-action');

        if (!metric || !apiModule || !apiAction) {
            sendFeedback(reportValuesAutoComplete);
        }

        var ajaxRequest = new ajaxHelper();
        ajaxRequest.addParams({
            module: 'API',
            method: 'API.getProcessedReport',
            date: 'yesterday',
            period: 'month',
            showColumns: metric,
            apiModule: apiModule,
            apiAction: apiAction,
            idSite: $('[name=idSite]').val(),
            format: 'JSON'
        }, 'GET');
        ajaxRequest.setCallback(function(data) {
            if (data && data.reportData) {
                reportValuesAutoComplete = data.reportData;
                sendFeedback(data.reportData);
            } else {
                sendFeedback([]);
            }
        });
        ajaxRequest.setErrorCallback(function () {
            sendFeedback([]);
        });
        ajaxRequest.send(false);
    }

    function isBlockedReportApiMethod(apiMethodUniqueId) {
        return 'MultiSites_getOne' == apiMethodUniqueId || 'MultiSites_getAll' == apiMethodUniqueId;
    }

    function renderForm(data) {

        updateComparedTo();

        var currentApiMethod = $('#report').val();
        var options = "";

        for (var i = 0; i < data.length; i++) {
            var reportMetadata  = data[i];
            var reportApiMethod = reportMetadata.uniqueId;

            if (isBlockedReportApiMethod(reportApiMethod)) {
                continue;
            }

            var selected = '';
            if (!currentApiMethod) {
                currentApiMethod = reportApiMethod;
                selected = 'selected="selected"';
            }

            options += '<option data-module="' + reportMetadata.module + '" data-action="' + reportMetadata.action + '" ' + selected + ' value="' + reportApiMethod + '">' + reportMetadata.name + '</option>';

            if (reportApiMethod == currentApiMethod) {
                updateMetrics(reportMetadata.metrics);

                if (reportMetadata.dimension) {
                    $('#reportInfo').text(data[i].dimension);
                    $('.reportCondition').removeAttr('disabled');
                    $('.reportCondition').show();
                    $('.reportValue').removeAttr('disabled');
                    $('.reportValue').show();
                    $('.reportConditionField').show();
                    $('.reportValueField').show();
                } else {
                    $('#reportInfo').text('');
                    $('.reportCondition').attr('disabled', 'disabled');
                    $('.reportCondition').hide();
                    $('.reportValue').attr('disabled', 'disabled');
                    $('.reportValue').hide();
                    $('.reportConditionField').hide();
                    $('.reportValueField').hide();
                }
            }
        }

        $('.reports').html(options);
        $('.reports').val(currentApiMethod);

        updateReportCondition();
        updateMetricCondition();
    }

    function updateComparedTo()
    {
        var period = getPeriodValue();

        $('.comparedToField select').hide();
        $('.comparedToField select').attr('data-inactive', 'data-inactive');
        $('.comparedToField select[data-period='+ period + ']').show();
        $('.comparedToField select[data-period='+ period + ']').removeAttr('data-inactive');
    }

    function updateMetrics(metrics)
    {
        var currentMetric = $('#metric').val();
        var mOptions = "";

        for (var metric in metrics) {

            var selected = '';
            if (metric == currentMetric) {
                selected = ' selected="selected"';
            }
            
            mOptions += '<option value="' + metric + '"' + selected + '>' + metrics[metric] + '</option>';
        }

        $('.metrics').html(mOptions);
    }

    function updateReportCondition()
    {
        if ('matches_any' == $('.reportCondition').val()) {
            $('span.reportConditionField').attr('colspan', '2');
            $('span.reportValueField').hide();
        } else {
            $('span.reportConditionField').attr('colspan', '');
            $('span.reportValueField').show();
        }
    }

    function updateMetricCondition()
    {
        var condition = $('#metricCondition').val();
        var metric = $('#metric').find('option:selected').val();

        var isPercentageCondition = condition && 0 === condition.indexOf('percentage_');
        var isPercentageMetric    = metric && -1 !== metric.indexOf('_rate');
        var isSecondsMetric = metric && -1 !== metric.indexOf('_time_');

        if (isPercentageCondition || isPercentageMetric) {
            $('.metricValueDescription').show();
            $('.metricValueDescription').text('%');
        } else if (isSecondsMetric) {
            $('.metricValueDescription').show();
            $('.metricValueDescription').text('s');
        } else {
            $('.metricValueDescription').hide();
        }
    }

    function deleteAlert(alertId)
    {
        function onDelete()
        {
            var ajaxRequest = new ajaxHelper();
            ajaxRequest.addParams({
                module: 'API',
                method: 'CustomAlerts.deleteAlert',
                idAlert: alertId,
                format: 'JSON'
            }, 'GET');
            ajaxRequest.redirectOnSuccess();
            ajaxRequest.send(false);
        }

        piwikHelper.modalConfirm('#confirm', {yes: onDelete});
    }

    $(document).ready(function() {

        updateReportCondition();
        updateMetricCondition();

        $('.alerts #period').change(updateFormValues);
        $('.alerts #report').change(updateFormValues)
        $('.alerts #metric').change(updateMetricCondition)
        $('.alerts #reportCondition').change(updateReportCondition)
        $('.alerts #metricCondition').change(updateMetricCondition)

        var currentSiteId = $('[name=idSite]').val();
        $('.sites_autocomplete').bind('piwik:siteSelected', function (e, site) {
            if (site.id != currentSiteId) {
                currentSiteId = site.id;
                updateFormValues(site.id);
            }
        });

        $('.entityListContainer .deleteAlert[id]').click(function() {
            deleteAlert($(this).attr('id'));
        });

        if ($('.alerts #period').length) {
            updateFormValues();
        }

        $('.alerts #reportValue').autocomplete({
            source: getValuesForReportAndMetric,
            minLength: 1,
            delay: 300
        });

    });

    return {
        getApiParameters: function () {

            var mailSettings   = getReportParametersFunctions.email ? getReportParametersFunctions.email() : {additionalEmails: [], emailMe: false};
            var mobileSettings = getReportParametersFunctions.mobile ? getReportParametersFunctions.mobile() : {phoneNumbers: ['']};

            var idReport = $('#report_idreport').val();
            var apiParameters = {};
            apiParameters.format = 'json';
            apiParameters.name  = $('#alertName').val();
            apiParameters.metric  = $('#metric').find('option:selected').val();
            apiParameters.metricCondition  = $('#metricCondition').find('option:selected').val();
            apiParameters.metricValue = $('#metricValue').val();
            apiParameters.emailMe = mailSettings.emailMe ? 1 : 0;
            apiParameters.additionalEmails = (mailSettings.additionalEmails && mailSettings.additionalEmails.length) ? mailSettings.additionalEmails : [''];
            apiParameters.phoneNumbers = mobileSettings.phoneNumbers;
            apiParameters.reportUniqueId = $('#report').find('option:selected').val();
            apiParameters.reportCondition = $('#reportCondition').find('option:selected').val();
            apiParameters.reportValue  = $('#reportValue').val();
            apiParameters.idSites = [$('[name=idSite]').val()];
            apiParameters.comparedTo = $('[name=compared_to]:not([data-inactive])').val();

            return apiParameters;
        },

        isValidAlert: function (alert) {

            if (!$.isNumeric(alert.metricValue)) {
                var UI = require('piwik/UI');
                var notification = new UI.Notification();
                var options = {id: 'CustomAlertsMetricValueError', context: 'error', type: 'toast'};

                notification.show(_pk_translate('CustomAlerts_InvalidMetricValue'), options);
                $('#metricValue').css({backgroundColor: '#f2dede'});
                return false;
            }

            $('#metricValue').css({backgroundColor: '#ffffff'});
            return true;
        },

        sendApiRequest: function (method, POSTparams) {
            var period = getPeriodValue();

            var ajaxHandler = new ajaxHelper();
            ajaxHandler.addParams(POSTparams, 'POST');
            ajaxHandler.addParams({period: period, module: 'API', method: method}, 'GET');
            ajaxHandler.redirectOnSuccess({module: 'CustomAlerts', action: 'index'});
            ajaxHandler.send(true);
        }
    };

})($);