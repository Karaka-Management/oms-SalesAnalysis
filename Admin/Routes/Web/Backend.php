<?php declare(strict_types=1);

use Modules\SalesAnalysis\Controller\BackendController;
use Modules\SalesAnalysis\Models\PermissionCategory;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^.*/sales/analysis(\?.*|$)$' => [
        [
            'dest'       => '\Modules\SalesAnalysis\Controller\BackendController:viewDashboard',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::DASHBOARD,
            ],
        ],
    ],

    '^.*/sales/analysis/bill(\?.*|$)$' => [
        [
            'dest'       => '\Modules\SalesAnalysis\Controller\BackendController:viewBillAnalysis',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DASHBOARD,
            ],
        ],
    ],
    '^.*/sales/analysis/rep(\?.*|$)$' => [
        [
            'dest'       => '\Modules\SalesAnalysis\Controller\BackendController:viewSalesRepAnalysis',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DASHBOARD,
            ],
        ],
    ],
    '^.*/sales/analysis/region(\?.*|$)$' => [
        [
            'dest'       => '\Modules\SalesAnalysis\Controller\BackendController:viewRegionAnalysis',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DASHBOARD,
            ],
        ],
    ],
    '^.*/sales/analysis/client(\?.*|$)$' => [
        [
            'dest'       => '\Modules\SalesAnalysis\Controller\BackendController:viewClientAnalysis',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DASHBOARD,
            ],
        ],
    ],
    '^.*/sales/analysis/item(\?.*|$)$' => [
        [
            'dest'       => '\Modules\SalesAnalysis\Controller\BackendController:viewItemSalesAnalysis',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DASHBOARD,
            ],
        ],
    ],
];
