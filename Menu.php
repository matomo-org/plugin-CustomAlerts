<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\CustomAlerts;

use Piwik\Menu\MenuUser;
use Piwik\Piwik;

class Menu extends \Piwik\Plugin\Menu
{
    public function configureUserMenu(MenuUser $menu)
    {
        $menu->add(
            'CoreAdminHome_MenuManage',
            'CustomAlerts_Alerts',
            array('module' => 'CustomAlerts', 'action' => 'index'),
            !Piwik::isUserIsAnonymous(),
            $order = 9);
    }

}
