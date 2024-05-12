<?php declare(strict_types=1);

use Modules\SalesAnalysis\Controller\BackendController;
use Modules\SalesAnalysis\Models\PermissionCategory;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^/sales/analysis/ytd(\?.*$|$)' => [
        [
            'dest'       => '\Modules\SalesAnalysis\Controller\BackendController:viewDashboardYtd',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::DASHBOARD,
            ],
        ],
    ],
    '^/sales/analysis/mtd(\?.*$|$)' => [
        [
            'dest'       => '\Modules\SalesAnalysis\Controller\BackendController:viewDashboardMtd',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::DASHBOARD,
            ],
        ],
    ],
    '^/sales/analysis/monthly(\?.*$|$)' => [
        [
            'dest'       => '\Modules\SalesAnalysis\Controller\BackendController:viewDashboardMonthly',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::DASHBOARD,
            ],
        ],
    ],
    '^/sales/analysis/annually(\?.*$|$)' => [
        [
            'dest'       => '\Modules\SalesAnalysis\Controller\BackendController:viewDashboardAnnually',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::DASHBOARD,
            ],
        ],
    ],

    '^/sales/analysis/bill/ytd(\?.*$|$)' => [
        [
            'dest'       => '\Modules\SalesAnalysis\Controller\BackendController:viewBillAnalysisYtd',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DASHBOARD,
            ],
        ],
    ],
    '^/sales/analysis/bill/mtd(\?.*$|$)' => [
        [
            'dest'       => '\Modules\SalesAnalysis\Controller\BackendController:viewBillAnalysisMtd',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DASHBOARD,
            ],
        ],
    ],
    '^/sales/analysis/bill/monthly(\?.*$|$)' => [
        [
            'dest'       => '\Modules\SalesAnalysis\Controller\BackendController:viewBillAnalysisMonthly',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DASHBOARD,
            ],
        ],
    ],
    '^/sales/analysis/bill/annually(\?.*$|$)' => [
        [
            'dest'       => '\Modules\SalesAnalysis\Controller\BackendController:viewBillAnalysisAnnually',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DASHBOARD,
            ],
        ],
    ],
    '^/sales/analysis/rep/ytd(\?.*$|$)' => [
        [
            'dest'       => '\Modules\SalesAnalysis\Controller\BackendController:viewSalesRepAnalysisYtd',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DASHBOARD,
            ],
        ],
    ],
    '^/sales/analysis/rep/mtd(\?.*$|$)' => [
        [
            'dest'       => '\Modules\SalesAnalysis\Controller\BackendController:viewSalesRepAnalysisMtd',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DASHBOARD,
            ],
        ],
    ],
    '^/sales/analysis/rep/monthly(\?.*$|$)' => [
        [
            'dest'       => '\Modules\SalesAnalysis\Controller\BackendController:viewSalesRepAnalysisMonthly',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DASHBOARD,
            ],
        ],
    ],
    '^/sales/analysis/rep/annually(\?.*$|$)' => [
        [
            'dest'       => '\Modules\SalesAnalysis\Controller\BackendController:viewSalesRepAnalysisAnnually',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DASHBOARD,
            ],
        ],
    ],
    '^/sales/analysis/region/ytd(\?.*$|$)' => [
        [
            'dest'       => '\Modules\SalesAnalysis\Controller\BackendController:viewRegionAnalysisYtd',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DASHBOARD,
            ],
        ],
    ],
    '^/sales/analysis/region/mtd(\?.*$|$)' => [
        [
            'dest'       => '\Modules\SalesAnalysis\Controller\BackendController:viewRegionAnalysisMtd',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DASHBOARD,
            ],
        ],
    ],
    '^/sales/analysis/region/monthly(\?.*$|$)' => [
        [
            'dest'       => '\Modules\SalesAnalysis\Controller\BackendController:viewRegionAnalysisMonthly',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DASHBOARD,
            ],
        ],
    ],
    '^/sales/analysis/region/annually(\?.*$|$)' => [
        [
            'dest'       => '\Modules\SalesAnalysis\Controller\BackendController:viewRegionAnalysisAnnually',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DASHBOARD,
            ],
        ],
    ],
    '^/sales/analysis/client/ytd(\?.*$|$)' => [
        [
            'dest'       => '\Modules\SalesAnalysis\Controller\BackendController:viewClientAnalysisYtd',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DASHBOARD,
            ],
        ],
    ],
    '^/sales/analysis/client/mtd(\?.*$|$)' => [
        [
            'dest'       => '\Modules\SalesAnalysis\Controller\BackendController:viewClientAnalysisMtd',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DASHBOARD,
            ],
        ],
    ],
    '^/sales/analysis/client/monthly(\?.*$|$)' => [
        [
            'dest'       => '\Modules\SalesAnalysis\Controller\BackendController:viewClientAnalysisMonthly',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DASHBOARD,
            ],
        ],
    ],
    '^/sales/analysis/client/annually(\?.*$|$)' => [
        [
            'dest'       => '\Modules\SalesAnalysis\Controller\BackendController:viewClientAnalysisAnnually',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DASHBOARD,
            ],
        ],
    ],
    '^/sales/analysis/item/ytd(\?.*$|$)' => [
        [
            'dest'       => '\Modules\SalesAnalysis\Controller\BackendController:viewItemSalesAnalysisYtd',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DASHBOARD,
            ],
        ],
    ],
    '^/sales/analysis/item/mtd(\?.*$|$)' => [
        [
            'dest'       => '\Modules\SalesAnalysis\Controller\BackendController:viewItemSalesAnalysisMtd',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DASHBOARD,
            ],
        ],
    ],
    '^/sales/analysis/item/monthly(\?.*$|$)' => [
        [
            'dest'       => '\Modules\SalesAnalysis\Controller\BackendController:viewItemSalesAnalysisMonthly',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DASHBOARD,
            ],
        ],
    ],
    '^/sales/analysis/item/annually(\?.*$|$)' => [
        [
            'dest'       => '\Modules\SalesAnalysis\Controller\BackendController:viewItemSalesAnalysisAnnually',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DASHBOARD,
            ],
        ],
    ],
];
