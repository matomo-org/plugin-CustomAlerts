/*!
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

interface Alert {
  idalert: string|number;
  name: string;
  login: string;
  period: string;
  report: string;
  report_condition: string;
  report_matched: string;
  metric: string;
  metric_condition: string;
  metric_matched: string;
  compared_to: number|string;
  email_me: number|boolean;
  additional_emails: string[];
  phone_numbers: string[];
  id_sites: (string|number)[];
  reportName: string;
  siteName: string;
}

export { Alert };
