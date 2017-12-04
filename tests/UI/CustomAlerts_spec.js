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
    this.fixture = "Piwik\\Plugins\\CustomAlerts\\tests\\Fixtures\\CustomAlerts";

    var generalParams = 'idSite=1&period=year&date=2012-08-09';

    it('should load the triggered custom alerts list correctly', function (done) {
        expect.screenshot('list_triggered').to.be.captureSelector('.pageWrap', function (page) {
            page.load("?" + generalParams + "&module=CustomAlerts&action=historyTriggeredAlerts&idSite=1&period=day&date=yesterday");
        }, done);
    });

    it('should load the custom alerts list correctly', function (done) {
        expect.screenshot('list').to.be.captureSelector('.pageWrap', function (page) {
            page.load("?" + generalParams + "&module=CustomAlerts&action=index&idSite=1&period=day&date=yesterday");
        }, done);
    });

    it('should load custom alerts edit screen', function (done) {
        expect.screenshot('edit').to.be.captureSelector('.pageWrap', function (page) {
            page.click('tbody tr:first-child td.edit a', 1000);
        }, done);
    });

    it('should save changed alert', function (done) {
        // only check if name was changed in list, no need to make a screenshot
        expect.current_page.contains(".pageWrap td:contains('Test Alert 1 changed')", function (page) {
            page.sendKeys('#alertName', ' changed');
            page.click('[piwik-save-button]', 1000);
        }, done);
    });

    it('should show delete dialog', function (done) {
        expect.screenshot('delete').to.be.captureSelector('.modal.open', function (page) {
            page.click('tbody tr:first-child td.delete button');
        }, done);
    });

    it('should deleted alert', function (done) {
        // only check if name isn't in list, no need to make a screenshot
        expect.current_page.not.contains(".pageWrap td:contains('Test Alert 1 changed')", function (page) {
            page.click('.modal.open .modal-action:contains("Yes")', 1000);
        }, done);
    });
});