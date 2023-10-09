<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   Modules\SalesAnalysis
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\SalesAnalysis\Controller;

use Modules\Organization\Models\UnitMapper;
use Modules\SalesAnalysis\Models\ClientMapper;
use Modules\SalesAnalysis\Models\GeneralMapper;
use Modules\SalesAnalysis\Models\ItemMapper;
use Modules\SalesAnalysis\Models\RegionMapper;
use phpOMS\Asset\AssetType;
use phpOMS\Contract\RenderableInterface;
use phpOMS\DataStorage\Database\Query\Builder;
use phpOMS\Localization\ISO3166CharEnum;
use phpOMS\Localization\ISO3166NameEnum;
use phpOMS\Localization\ISO3166TwoEnum;
use phpOMS\Localization\RegionEnum;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Stdlib\Base\SmartDateTime;
use phpOMS\Views\View;

/**
 * Sales class.
 *
 * @package Modules\SalesAnalysis
 * @license OMS License 1.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class BackendController extends Controller
{
    /**
     * Method which shows the sales dashboard
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface Response can be rendered
     *
     * @since 1.0.0
     */
    public function viewDashboard(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $head  = $response->data['Content']->head;
        $nonce = $this->app->appSettings->getOption('script-nonce');

        $head->addAsset(AssetType::CSS, 'Resources/chartjs/chart.css');
        $head->addAsset(AssetType::JSLATE, 'Resources/chartjs/chart.js', ['nonce' => $nonce]);
        $head->addAsset(AssetType::JSLATE, 'Modules/SalesAnalysis/Controller/Controller.js', ['nonce' => $nonce, 'type' => 'module']);

        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/SalesAnalysis/Theme/Backend/analysis-overview-dashboard');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1005401001, $request, $response);

        // @todo: limit bill type (invoice/credit note) (customers only)
        // @todo: limit bill status

        $businessStart   = 1;
        $startOfYear     = SmartDateTime::createFromDateTime(SmartDateTime::startOfYear($businessStart));
        $startCurrent    = $request->getDataDateTime('startcurrent') ?? clone $startOfYear;
        $endCurrent      = $request->getDataDateTime('endcurrent') ?? SmartDateTime::endOfMonth();
        $endCurrentIndex = SmartDateTime::calculateMonthIndex((int) $endCurrent->format('m'), $businessStart);
        $startComparison = $request->getDataDateTime('startcomparison') ?? SmartDateTime::createFromDateTime($startCurrent)->createModify(-1);
        $endComparison   = $request->getDataDateTime('endcomparison') ?? SmartDateTime::createFromDateTime(SmartDateTime::endOfYear($businessStart))->smartModify(-1);

        $view->data['startCurrent']    = $startCurrent;
        $view->data['endCurrent']      = $endCurrent;
        $view->data['startComparison'] = $startComparison;
        $view->data['endComparison']   = $endComparison;

        [
            $view->data['mtdA'],
            $view->data['mtdPY'],
            $view->data['ytdA'],
            $view->data['ytdPY'],
            $view->data['monthlySales']
        ] = GeneralMapper::monthlySalesProfit(
            $startCurrent,
            $endCurrent,
            $startComparison,
            $endComparison,
            $businessStart
        );

        $historyStart              = $startOfYear->createModify(-9);
        $view->data['annualSales'] = GeneralMapper::annualSalesProfit($historyStart, $endCurrent, $businessStart);

        [
            $view->data['mtdAItemAttribute'],
            $view->data['mtdPYItemAttribute'],
            $view->data['ytdAItemAttribute'],
            $view->data['ytdPYItemAttribute']
        ] = ItemMapper::mtdYtdItemAttribute(
            $startCurrent,
            $endCurrent,
            $startComparison,
            $endComparison,
            $businessStart,
            $request->header->l11n->language
        );

        [
            $view->data['mtdAClientAttribute'],
            $view->data['mtdPYClientAttribute'],
            $view->data['ytdAClientAttribute'],
            $view->data['ytdPYClientAttribute']
        ] = ClientMapper::mtdYtdClientAttribute(
            $startCurrent,
            $endCurrent,
            $startComparison,
            $endComparison,
            $businessStart,
            $request->header->l11n->language
        );

        [
            $view->data['mtdAClientCountry'],
            $view->data['mtdPYClientCountry'],
            $view->data['ytdAClientCountry'],
            $view->data['ytdPYClientCountry']
        ] = RegionMapper::mtdYtdCountry(
            $startCurrent,
            $endCurrent,
            $startComparison,
            $endComparison,
            $businessStart
        );

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewRegionAnalysis(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $head  = $response->data['Content']->head;
        $nonce = $this->app->appSettings->getOption('script-nonce');

        $head->addAsset(AssetType::CSS, 'Resources/chartjs/chart.css');
        $head->addAsset(AssetType::JSLATE, 'Resources/chartjs/chart.js', ['nonce' => $nonce]);
        $head->addAsset(AssetType::JSLATE, 'Resources/chartjs/plugins/chartjs-chart-geo.js', ['nonce' => $nonce]);
        $head->addAsset(AssetType::JSLATE, 'Modules/SalesAnalysis/Controller/Controller.js', ['nonce' => $nonce, 'type' => 'module']);

        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/SalesAnalysis/Theme/Backend/analysis-region');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1005401001, $request, $response);

        $businessStart   = 1;
        $startOfYear     = SmartDateTime::createFromDateTime(SmartDateTime::startOfYear($businessStart));
        $startCurrent    = $request->getDataDateTime('startcurrent') ?? clone $startOfYear;
        $endCurrent      = $request->getDataDateTime('endcurrent') ?? SmartDateTime::endOfMonth();
        $endCurrentIndex = SmartDateTime::calculateMonthIndex((int) $endCurrent->format('m'), $businessStart);
        $startComparison = $request->getDataDateTime('startcomparison') ?? SmartDateTime::createFromDateTime($startCurrent)->createModify(-1);
        $endComparison   = $request->getDataDateTime('endcomparison') ?? SmartDateTime::createFromDateTime(SmartDateTime::endOfYear($businessStart))->smartModify(-1);
        $historyStart    = $startOfYear->createModify(-9);

        $view->data['startCurrent']    = $startCurrent;
        $view->data['endCurrent']      = $endCurrent;
        $view->data['startComparison'] = $startComparison;
        $view->data['endComparison']   = $endComparison;
        $view->data['historyStart']    = $historyStart;

        $domestic = UnitMapper::get()
            ->with('mainAddress')
            ->where('id', $this->app->unitId)
            ->execute();

        $view->data['domestic'] = $domestic->mainAddress->country === ISO3166TwoEnum::_XXX ? 'US' : $domestic->mainAddress->country;

        [
            $mtdCurrent,
            $ytdCurrent,
            $monthlyCurrent
        ] = RegionMapper::monthlySalesProfit(
            $startCurrent,
            $endCurrent,
            $businessStart
        );

        $view->data['monthlyDomesticExportCurrent'] = RegionMapper::countryIntervalToRegion(
            $monthlyCurrent,
            [$view->data['domestic']],
            ['net_sales', 'net_profit']
        );

        [
            $mtdPY,
            $ytdPY,
            $monthlyPY
        ] = RegionMapper::monthlySalesProfit(
            $startComparison,
            $endComparison,
            $businessStart
        );

        $view->data['monthlyDomesticExportPY'] = RegionMapper::countryIntervalToRegion(
            $monthlyPY,
            [$view->data['domestic']],
            ['net_sales', 'net_profit']
        );

        [
            $view->data['mtdPYClientCountry'],
            $view->data['mtdAClientCountry'],
            $view->data['ytdPYClientCountry'],
            $view->data['ytdAClientCountry'],
        ] = RegionMapper::mtdYtdCountry(
            $startCurrent,
            $endCurrent,
            $startComparison,
            $endComparison,
            $businessStart
        );

        $annualCountrySales = RegionMapper::annualSalesProfitCountry(clone $historyStart, $endCurrent);

        $view->data['ytdADomesticExport'] = RegionMapper::countryToRegion(
            $view->data['ytdAClientCountry'],
            [$view->data['domestic']],
            ['net_sales', 'net_profit']
        );

        $view->data['annualDomesticExport'] = RegionMapper::countryIntervalToRegion(
            $annualCountrySales,
            [$view->data['domestic']],
            ['net_sales', 'net_profit']
        );

        [
            $view->data['mtdPYClientCountryCount'],
            $view->data['mtdAClientCountryCount'],
            $view->data['ytdPYClientCountryCount'],
            $view->data['ytdAClientCountryCount'],
        ] = RegionMapper::mtdYtdClientCountry(
            $startCurrent,
            $endCurrent,
            $startComparison,
            $endComparison,
            $businessStart
        );

        $annualCountryCount = RegionMapper::annualCustomerCountry(clone $historyStart, $endCurrent);

        $view->data['ytdADomesticExportCount'] = RegionMapper::countryToRegion(
            $view->data['ytdAClientCountryCount'],
            [$view->data['domestic']],
            ['client_count']
        );

        $view->data['annualDomesticExportCount'] = RegionMapper::countryIntervalToRegion(
            $annualCountryCount,
            [$view->data['domestic']],
            ['client_count']
        );

        ///

        $view->data['ytdAContinent'] = RegionMapper::countryToRegion(
            $view->data['ytdAClientCountry'],
            ISO3166NameEnum::getSubregions('continents'),
            ['net_sales', 'net_profit']
        );

        $view->data['annualContinent'] = RegionMapper::countryIntervalToRegion(
            $annualCountrySales,
            ISO3166NameEnum::getSubregions('continents'),
            ['net_sales', 'net_profit']
        );

        $view->data['ytdAContinentCount'] = RegionMapper::countryToRegion(
            $view->data['ytdAClientCountryCount'],
            ISO3166NameEnum::getSubregions('continents'),
            ['client_count']
        );

        $view->data['annualContinentCount'] = RegionMapper::countryIntervalToRegion(
            $annualCountryCount,
            ISO3166NameEnum::getSubregions('continents'),
            ['client_count']
        );

        ///

        $view->data['ytdARegions'] = RegionMapper::countryToRegion(
            $view->data['ytdAClientCountry'],
            RegionEnum::getConstants(),
            ['net_sales', 'net_profit']
        );

        $view->data['ytdPYRegions'] = RegionMapper::countryToRegion(
            $view->data['ytdPYClientCountry'],
            RegionEnum::getConstants(),
            ['net_sales', 'net_profit']
        );

        $view->data['mtdARegions'] = RegionMapper::countryToRegion(
            $view->data['mtdAClientCountry'],
            RegionEnum::getConstants(),
            ['net_sales', 'net_profit']
        );

        $view->data['mtdPYRegions'] = RegionMapper::countryToRegion(
            $view->data['mtdPYClientCountry'],
            RegionEnum::getConstants(),
            ['net_sales', 'net_profit']
        );

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewBillAnalysis(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $head  = $response->data['Content']->head;
        $nonce = $this->app->appSettings->getOption('script-nonce');

        $head->addAsset(AssetType::CSS, 'Resources/chartjs/chart.css');
        $head->addAsset(AssetType::JSLATE, 'Resources/chartjs/chart.js', ['nonce' => $nonce]);
        $head->addAsset(AssetType::JSLATE, 'Modules/SalesAnalysis/Controller/Controller.js', ['nonce' => $nonce, 'type' => 'module']);

        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/SalesAnalysis/Theme/Backend/analysis-bill');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1005401001, $request, $response);

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewSalesRepAnalysis(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $head  = $response->data['Content']->head;
        $nonce = $this->app->appSettings->getOption('script-nonce');

        $head->addAsset(AssetType::CSS, 'Resources/chartjs/chart.css');
        $head->addAsset(AssetType::JSLATE, 'Resources/chartjs/chart.js', ['nonce' => $nonce]);
        $head->addAsset(AssetType::JSLATE, 'Modules/SalesAnalysis/Controller/Controller.js', ['nonce' => $nonce, 'type' => 'module']);

        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/SalesAnalysis/Theme/Backend/analysis-rep');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1005401001, $request, $response);

        /////
        $currentCustomerRegion = [
            'Europe'  => (int) (\mt_rand(200, 400) / 4),
            'America' => (int) (\mt_rand(200, 400) / 4),
            'Asia'    => (int) (\mt_rand(200, 400) / 4),
            'Africa'  => (int) (\mt_rand(200, 400) / 4),
            'CIS'     => (int) (\mt_rand(200, 400) / 4),
            'Other'   => (int) (\mt_rand(200, 400) / 4),
        ];

        $view->data['currentCustomerRegion'] = $currentCustomerRegion;

        $annualCustomerRegion = [];
        for ($i = 1; $i < 11; ++$i) {
            $annualCustomerRegion[] = [
                'year'    => 2020 - 10 + $i,
                'Europe'  => $a = (int) (\mt_rand(200, 400) / 4),
                'America' => $b = (int) (\mt_rand(200, 400) / 4),
                'Asia'    => $c = (int) (\mt_rand(200, 400) / 4),
                'Africa'  => $d = (int) (\mt_rand(200, 400) / 4),
                'CIS'     => $e = (int) (\mt_rand(200, 400) / 4),
                'Other'   => $f = (int) (\mt_rand(200, 400) / 4),
                'Total'   => $a + $b + $c + $d + $e + $f,
            ];
        }

        $view->data['annualCustomerRegion'] = $annualCustomerRegion;

         /////
        $currentCustomersRep = [];
        for ($i = 1; $i < 13; ++$i) {
            $currentCustomersRep['Rep ' . $i] = [
                'customers' => (int) (\mt_rand(200, 400) / 12),
            ];
        }

        \uasort($currentCustomersRep, function($a, $b) {
            return $b['customers'] <=> $a['customers'];
        });

        $view->data['currentCustomersRep'] = $currentCustomersRep;

        $annualCustomersRep = [];
        for ($i = 1; $i < 13; ++$i) {
            $annualCustomersRep['Rep ' . $i] = [];

            for ($j = 1; $j < 11; ++$j) {
                $annualCustomersRep['Rep ' . $i][] = [
                    'customers' => (int) (\mt_rand(200, 400) / 12),
                    'year'      => 2020 - 10 + $j,
                ];
            }
        }

        $view->data['annualCustomersRep'] = $annualCustomersRep;

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewClientAnalysis(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $head  = $response->data['Content']->head;
        $nonce = $this->app->appSettings->getOption('script-nonce');

        $head->addAsset(AssetType::CSS, 'Resources/chartjs/chart.css');
        $head->addAsset(AssetType::JSLATE, 'Resources/chartjs/chart.js', ['nonce' => $nonce]);
        $head->addAsset(AssetType::JSLATE, 'Resources/chartjs/plugins/chartjs-chart-geo.js', ['nonce' => $nonce]);
        $head->addAsset(AssetType::JSLATE, 'Modules/SalesAnalysis/Controller/Controller.js', ['nonce' => $nonce, 'type' => 'module']);

        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/SalesAnalysis/Theme/Backend/analysis-client');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1005401001, $request, $response);

        $monthlySalesCosts = [];
        for ($i = 1; $i < 13; ++$i) {
            $monthlySalesCosts[] = [
                'net_sales' => $sales = \mt_rand(1200000000, 2000000000),
                'net_costs' => (int) ($sales * \mt_rand(25, 55) / 100),
                'year'      => 2020,
                'month'     => $i,
            ];
        }

        $view->data['monthlySalesCosts'] = $monthlySalesCosts;

        /////
        $monthlySalesCustomer = [];
        for ($i = 1; $i < 13; ++$i) {
            $monthlySalesCustomer[] = [
                'net_sales' => $sales = \mt_rand(1200000000, 2000000000),
                'customers' => \mt_rand(200, 400),
                'year'      => 2020,
                'month'     => $i,
            ];
        }

        $view->data['monthlySalesCustomer'] = $monthlySalesCustomer;

        $annualSalesCustomer = [];
        for ($i = 1; $i < 11; ++$i) {
            $annualSalesCustomer[] = [
                'net_sales' => $sales = \mt_rand(1200000000, 2000000000) * 12,
                'customers' => \mt_rand(200, 400) * 6,
                'year'      => 2020 - 10 + $i,
            ];
        }

        $view->data['annualSalesCustomer'] = $annualSalesCustomer;

        /////
        $monthlyCustomerRetention = [];
        for ($i = 1; $i < 10; ++$i) {
            $monthlyCustomerRetention[] = [
                'customers' => \mt_rand(200, 400),
                'year'      => \date('y') - 9 + $i,
            ];
        }

        $view->data['monthlyCustomerRetention'] = $monthlyCustomerRetention;

        /////
        $currentCustomerRegion = [
            'Europe'  => (int) (\mt_rand(200, 400) / 4),
            'America' => (int) (\mt_rand(200, 400) / 4),
            'Asia'    => (int) (\mt_rand(200, 400) / 4),
            'Africa'  => (int) (\mt_rand(200, 400) / 4),
            'CIS'     => (int) (\mt_rand(200, 400) / 4),
            'Other'   => (int) (\mt_rand(200, 400) / 4),
        ];

        $view->data['currentCustomerRegion'] = $currentCustomerRegion;

        $annualCustomerRegion = [];
        for ($i = 1; $i < 11; ++$i) {
            $annualCustomerRegion[] = [
                'year'    => 2020 - 10 + $i,
                'Europe'  => $a = (int) (\mt_rand(200, 400) / 4),
                'America' => $b = (int) (\mt_rand(200, 400) / 4),
                'Asia'    => $c = (int) (\mt_rand(200, 400) / 4),
                'Africa'  => $d = (int) (\mt_rand(200, 400) / 4),
                'CIS'     => $e = (int) (\mt_rand(200, 400) / 4),
                'Other'   => $f = (int) (\mt_rand(200, 400) / 4),
                'Total'   => $a + $b + $c + $d + $e + $f,
            ];
        }

        $view->data['annualCustomerRegion'] = $annualCustomerRegion;

        /////
        $currentCustomersRep = [];
        for ($i = 1; $i < 13; ++$i) {
            $currentCustomersRep['Rep ' . $i] = [
                'customers' => (int) (\mt_rand(200, 400) / 12),
            ];
        }

        \uasort($currentCustomersRep, function($a, $b) {
            return $b['customers'] <=> $a['customers'];
        });

        $view->data['currentCustomersRep'] = $currentCustomersRep;

        $annualCustomersRep = [];
        for ($i = 1; $i < 13; ++$i) {
            $annualCustomersRep['Rep ' . $i] = [];

            for ($j = 1; $j < 11; ++$j) {
                $annualCustomersRep['Rep ' . $i][] = [
                    'customers' => (int) (\mt_rand(200, 400) / 12),
                    'year'      => 2020 - 10 + $j,
                ];
            }
        }

        $view->data['annualCustomersRep'] = $annualCustomersRep;

        /////
        $currentCustomersCountry = [];
        for ($i = 1; $i < 51; ++$i) {
            $country                                           = (string) ISO3166NameEnum::getRandom();
            $currentCustomersCountry[\substr($country, 0, 20)] = [
                'customers' => (int) (\mt_rand(200, 400) / 12),
            ];
        }

        \uasort($currentCustomersCountry, function($a, $b) {
            return $b['customers'] <=> $a['customers'];
        });

        $view->data['currentCustomersCountry'] = $currentCustomersCountry;

        $annualCustomersCountry = [];
        for ($i = 1; $i < 51; ++$i) {
            $countryCode                                          = ISO3166CharEnum::getRandom();
            $countryName                                          = (string) ISO3166NameEnum::getByName('_' . $countryCode);
            $annualCustomersCountry[\substr($countryName, 0, 20)] = [];

            for ($j = 1; $j < 11; ++$j) {
                $annualCustomersCountry[\substr($countryName, 0, 20)][] = [
                    'customers' => (int) (\mt_rand(200, 400) / 12),
                    'year'      => 2020 - 10 + $j,
                    'name'      => $countryName,
                    'code'      => $countryCode,
                ];
            }
        }

        $view->data['annualCustomersCountry'] = $annualCustomersCountry;

        /////
        $customerGroups = [];
        for ($i = 1; $i < 7; ++$i) {
            $customerGroups['Group ' . $i] = [
                'customers' => (int) (\mt_rand(200, 400) / 12),
            ];
        }

        $view->data['customerGroups'] = $customerGroups;

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemSalesAnalysis(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $head  = $response->data['Content']->head;
        $nonce = $this->app->appSettings->getOption('script-nonce');

        $head->addAsset(AssetType::CSS, 'Resources/chartjs/chart.css');
        $head->addAsset(AssetType::JSLATE, 'Resources/chartjs/chart.js', ['nonce' => $nonce]);
        $head->addAsset(AssetType::JSLATE, 'Modules/SalesAnalysis/Controller/Controller.js', ['nonce' => $nonce, 'type' => 'module']);

        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/SalesAnalysis/Theme/Backend/analysis-item');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1005401001, $request, $response);

        $businessStart   = 1;
        $startOfYear     = SmartDateTime::createFromDateTime(SmartDateTime::startOfYear($businessStart));
        $startCurrent    = $request->getDataDateTime('startcurrent') ?? clone $startOfYear;
        $endCurrent      = $request->getDataDateTime('endcurrent') ?? SmartDateTime::endOfMonth();
        $endCurrentIndex = SmartDateTime::calculateMonthIndex((int) $endCurrent->format('m'), $businessStart);
        $startComparison = $request->getDataDateTime('startcomparison') ?? SmartDateTime::createFromDateTime($startCurrent)->createModify(-1);
        $endComparison   = $request->getDataDateTime('endcomparison') ?? SmartDateTime::createFromDateTime(SmartDateTime::endOfYear($businessStart))->smartModify(-1);

        $view->data['startCurrent']    = $startCurrent;
        $view->data['endCurrent']      = $endCurrent;
        $view->data['startComparison'] = $startComparison;
        $view->data['endComparison']   = $endComparison;

        [
            $view->data['mtdAItemAttribute'],
            $view->data['mtdPYItemAttribute'],
            $view->data['ytdAItemAttribute'],
            $view->data['ytdPYItemAttribute']
        ] = ItemMapper::mtdYtdItemAttribute(
            $startCurrent,
            $endCurrent,
            $startComparison,
            $endComparison,
            $businessStart,
            $request->header->l11n->language
        );

        return $view;
    }
}
