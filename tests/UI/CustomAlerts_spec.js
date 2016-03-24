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

    //index.php?module=CustomAlerts&action=index&idSite=1&period=day&date=yesterday
    var generalParams = 'idSite=1&period=day&date=yesterday',
        urlBase = 'module=CustomAlerts&action=index&' + generalParams;

    before(function () {
        testEnvironment.pluginsToLoad = ['CustomAlerts'];
        testEnvironment.save();
    });

    it('should load a simple page by its module and action and take a full screenshot', function (done) {
        var screenshotName = 'simplePage';
        var urlToTest = "?" + generalParams + "&module=CustomAlerts&action=index";

        expect.screenshot(screenshotName).to.be.captureSelector('#content', function (page) {
            page.load(urlToTest);
        }, done);
    });

    it("should correctly display new alert form", function (done) {
        expect.screenshot('newAlertForm').to.be.captureSelector('#content', function (page) {
            page.click('a.addNewAlert');
        }, done);
    });

    it("should correctly display the new alert on a list aftter filling new alert form", function (done) {
        expect.screenshot('newAlertAdded').to.be.captureSelector('#content', function (page) {
            page.sendKeys('#alertName', 'New alert');
            page.click('#site-1');
            page.click('#report_email_me');
            page.sendKeys('#metricValue', '20000');
            page.click('input.submit');
        }, done);
    });

    it("should correctly display edit alert form", function (done) {
        expect.screenshot('editAlertForm').to.be.captureSelector('#content', function (page) {
            page.click('tr:nth-child(4) td.edit>a');
        }, done);
    });

    it("should correctly display the edited alert on a list aftter filling edit alert form", function (done) {
        expect.screenshot('newAlertEdited').to.be.captureSelector('#content', function (page) {
            page.sendKeys('#alertName', 'Edited ');
            page.click('input.submit');
        }, done);
    });
});
