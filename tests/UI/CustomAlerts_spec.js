/*!
 * Piwik - free/libre analytics platform
 *
 * Screenshot integration tests.
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

describe("CustomAlerts", function () {
    this.timeout(0);

    var generalParams = 'idSite=1&period=year&date=2012-08-09';

    it('should load the custom alerts list correctly', function (done) {
        expect.screenshot('list').to.be.captureSelector('.pageWrap', function (page) {
            page.load("?" + generalParams + "&module=CustomAlerts&action=index&idSite=1&period=day&date=yesterday&tests_hide_piwik_version=1");
        }, done);
    });

    it('should load the triggered custom alerts list correctly', function (done) {
        expect.screenshot('list_triggered').to.be.captureSelector('.pageWrap', function (page) {
            page.load("?" + generalParams + "&module=CustomAlerts&action=historyTriggeredAlerts&idSite=1&period=day&date=yesterday&tests_hide_piwik_version=1");
        }, done);
    });

});