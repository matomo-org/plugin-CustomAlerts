/*!
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

const mocks = vi.hoisted(() => ({ show: vi.fn() }));

vi.mock('CoreHome', () => ({
  NotificationsStore: { show: mocks.show },
}));

import showHttpErrorMessage from './showHttpErrorMessage';

describe('showHttpErrorMessage', () => {
  beforeEach(() => {
    mocks.show.mockReset();
    document.body.innerHTML = '<div id="loadingError" style="display: block"></div>';
  });

  it('shows the message of an error answered with a 4xx status in place of the generic error', () => {
    const message = showHttpErrorMessage({
      status: 404,
      responseJSON: { result: 'error', message: 'The alert does not exist.' },
    });

    expect(message).toBe('The alert does not exist.');
    expect(mocks.show).toHaveBeenCalledWith(expect.objectContaining({
      message: 'The alert does not exist.',
      context: 'error',
      id: 'ajaxHelper',
    }));
    expect(document.getElementById('loadingError')!.style.display).toBe('none');
  });

  it.each([
    ['a server error', { status: 500, responseJSON: { result: 'error', message: 'Oops' } }],
    ['a 4xx without an API error', { status: 404, responseJSON: undefined }],
    ['a request that got no response', { status: 0 }],
    ['an error thrown in the page', new Error('Oops')],
  ])('leaves %s to AjaxHelper', (_, error) => {
    expect(showHttpErrorMessage(error)).toBeNull();
    expect(mocks.show).not.toHaveBeenCalled();
    expect(document.getElementById('loadingError')!.style.display).toBe('block');
  });
});
