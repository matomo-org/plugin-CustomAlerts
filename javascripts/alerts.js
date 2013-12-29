(function($) {

    var reportValuesAutoComplete = null;

    function updateMetrics() {

        reportValuesAutoComplete = null;

        var idSites = "";
        $("#idSites :selected").each(function(i,selected) {
            idSites = idSites + "&idSites[]=" + $(selected).val();
        });

        $.ajax({
            type: "GET",
            url: piwik.piwik_url,
            data: 'module=API&method=API.getReportMetadata'
                + '&idSite=' + piwik.idSite
                + '&period=' + $(".period").val()
                + '&date=' + piwik.currentDateString
                + '&token_auth=' + piwik.token_auth
                + '&format=JSON' + idSites,
            dataType: "json",
            success: function(data) {
                updateForm(data);
            }
        });
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

        var report = $('#report').find('option:selected').val();
        report = report.split('.');

        var idSites = "";
        $("#idSites :selected").each(function(i,selected) {
            idSites = idSites + "&idSites[]=" + $(selected).val();
        });

        $.ajax({
            type: "GET",
            url: piwik.piwik_url,
            data: 'module=API&method=API.getProcessedReport'
                + '&idSite=' + piwik.idSite
                + '&apiModule=' + report[0]
                + '&apiAction=' + report[1]
                + '&showColumns=' + metric
                + '&period=month'
                + '&date=yesterday'
                + '&token_auth=' + piwik.token_auth
                + '&format=JSON' + idSites,
            dataType: "json",
            success: function(data) {
                if (data && data.reportData) {
                    reportValuesAutoComplete = data.reportData;
                    sendFeedback(data.reportData);
                } else {
                    sendFeedback([]);
                }
            },
            error: function () {
                sendFeedback([]);
            }
        });
    }

    function updateForm(data) {

        currentGroup = $('.reports').val();
        options = "";
        for(var i = 0; i < data.length; i++)
        {
            value = data[i].module + '.' + data[i].action;
            if(currentGroup == undefined) {
                options += '<option selected="selected" value="' + value + '">' + data[i].name + '</option>';
                currentGroup = value;
            }
            else {
                options += '<option value="' + value + '">' + data[i].name + '</option>';
            }

            if(value == currentGroup)
            {
                metrics = data[i].metrics;

                mOptions = "";
                for(var metric in metrics)
                {
                    mOptions += '<option value="' + metric + '">' + metrics[metric] + '</option>';
                }
                $('.metrics').html(mOptions);

                if(data[i].dimension != undefined)
                {
                    $('#reportInfo').text("("+ data[i].dimension + ")");
                    $('.reportCondition').removeAttr('disabled');
                    $('.reportValue').removeAttr('disabled');
                    $('td.reportConditionField').show();
                    $('td.reportValueField').show();
                }
                else
                {
                    $('#reportInfo').text("");
                    $('.reportCondition').attr('disabled', 'disabled');
                    $('.reportValue').attr('disabled', 'disabled');
                    $('td.reportConditionField').hide();
                    $('td.reportValueField').hide();
                }
            }
        }
        $('.reports').html(options);
        $('.reports').val(currentGroup);
    }

    function initSitesDropdown() {
        $(".alerts #idSites").dropdownchecklist({ width: 150, maxDropHeight: 200, textFormatFunction: function(options) {
            var selectedOptions = options.filter(":selected");
            var countOfSelected = selectedOptions.size();
            var size = options.size();
            switch(countOfSelected) {
                case 0: return "0 other Websites";
                case 1: return selectedOptions.text();
                case size: return "all other Websites";
                default: return countOfSelected + " Websites";
            }
        }});
    }

    $(document).ready(function() {

        initSitesDropdown();

        $('.alerts .period').change(function() {
            updateMetrics();
        });

        $('.alerts .reports').change(function() {
            updateMetrics();
        })

        $('.alerts #idSites').change(function() {
            updateMetrics();
        });

        if ($('.alerts #idSites')) {
            updateMetrics();
        }

        $('.alerts #reportValue').autocomplete({
            source: getValuesForReportAndMetric,
            minLength: 1,
            delay: 300
        });

    });

})($);