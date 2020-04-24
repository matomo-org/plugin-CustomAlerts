/*!
 * Matomo - free/libre analytics platform
 *
 * Screenshot integration tests.
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

describe("CustomAlerts", function () {
    this.timeout(0);
    this.fixture = "Piwik\\Plugins\\CustomAlerts\\tests\\Fixtures\\CustomAlerts";

    var generalParams = 'idSite=1&period=year&date=2012-08-09';

    it('should load the triggered custom alerts list correctly', async function () {
        await page.goto("?" + generalParams + "&module=CustomAlerts&action=historyTriggeredAlerts&idSite=1&period=day&date=yesterday");
        var elem = await page.$('.pageWrap');
        expect(await elem.screenshot()).to.matchImage('list_triggered');
    });

    it('should load the custom alerts list correctly', async function () {
        await page.goto("?" + generalParams + "&module=CustomAlerts&action=index&idSite=1&period=day&date=yesterday");
        var elem = await page.$('.pageWrap');
        expect(await elem.screenshot()).to.matchImage('list');
    });

    it('should load custom alerts edit screen', async function () {
        await page.click('tbody tr:first-child td.edit a');
        await page.waitForNetworkIdle();
        await page.waitFor(350); // wait for animation
        var elem = await page.$('.pageWrap');
        expect(await elem.screenshot()).to.matchImage('edit');
    });

    it('should save changed alert', async function () {
        // only check if name was changed in list, no need to make a screenshot
        await page.type('#alertName', ' changed');
        await page.click('[piwik-save-button]');
        await page.waitForNetworkIdle();
        await page.waitForFunction('$(".pageWrap td:contains(\'Test Alert 1 changed\')").length > 0');
    });

    it('should show delete dialog', async function () {
        await page.click('tbody tr:first-child td.delete button');
        await page.waitFor(350); // wait for animation
        var elem = await page.jQuery('.modal.open');
        expect(await elem.screenshot()).to.matchImage('delete');
    });

    it('should deleted alert', async function () {
        // only check if name isn't in list, no need to make a screenshot
        await (await page.jQuery('.modal.open .modal-action:contains("Yes")')).click();
        await page.waitForNetworkIdle();
        await page.waitForFunction('$(".pageWrap td:contains(\'Test Alert 1 changed\')").length == 0');
    });
});