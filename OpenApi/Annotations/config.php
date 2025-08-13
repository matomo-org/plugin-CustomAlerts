<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

return [
    'methodsRequiringPost' => [
        'editAlert',
    ],
    'paramRefsByMethod' => [
    ],
    'successResponseByMethod' => [
        'getValuesForAlertInPast' => '#/components/responses/GenericArray',
        'getAlert' => '#/components/responses/Alert',
        'getAlerts' => '#/components/responses/AlertList',
        'addAlert' => '#/components/responses/GenericInteger',
        'editAlert' => '#/components/responses/GenericBoolean',
        'deleteAlert' => '#/components/responses/GenericSuccess',
        'getTriggeredAlerts' => '#/components/responses/AlertList',
    ],
];
