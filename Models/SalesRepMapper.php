<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   Modules\SalesAnalysis\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\SalesAnalysis\Models;

use Modules\Billing\Models\BillTransferType;
use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;
use phpOMS\DataStorage\Database\Query\Builder;
use phpOMS\Stdlib\Base\SmartDateTime;

/**
 * Permission category enum.
 *
 * @package Modules\SalesAnalysis\Models
 * @license OMS License 1.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
class SalesRepMapper extends DataMapperFactory
{
    /**
     * @todo Re-implement, still in use?
     */
    public static function monthlySalesProfit(
        \DateTime $start,
        \DateTime $end,
        int $businessStart = 1
    ) : array
    {
        $endCurrentIndex = SmartDateTime::calculateMonthIndex((int) $end->format('m'), $businessStart);

        $query = new Builder(self::$db);
        $query->raw(
            'SELECT
                billing_bill_rep,
                YEAR(billing_bill_performance_date) as salesyear,
                MONTH(billing_bill_performance_date) as salesmonth,
                SUM(billing_bill_netsales * billing_type_sign) as netsales,
                SUM(billing_bill_netprofit * billing_type_sign) as netprofit
            FROM billing_bill
            LEFT JOIN billing_type
                ON billing_bill_type = billing_type_id
            LEFT JOIN clientmgmt_client
                ON clientmgmt_client_id = billing_bill_client
            LEFT JOIN address
                ON clientmgmt_client_address = address_id
            WHERE
                billing_type_transfer_type = ' . BillTransferType::SALES . '
                AND billing_type_accounting = 1
                AND billing_bill_performance_date >= \'' . $start->format('Y-m-d') . '\'
                AND billing_bill_performance_date <= \'' . $end->format('Y-m-d') . '\'
            GROUP BY
                billing_bill_rep,
                YEAR(billing_bill_performance_date),
                MONTH(billing_bill_performance_date)
            ORDER BY
                YEAR(billing_bill_performance_date) ASC,
                MONTH(billing_bill_performance_date) ASC,
                billing_bill_rep'
        );

        $results = $query->execute()?->fetchAll(\PDO::FETCH_ASSOC) ?? [];

        $monthlySales = [];

        $mtd = [];
        $ytd = [];

        foreach ($results as $result) {
            $monthIndex = SmartDateTime::calculateMonthIndex((int) $result['salesmonth'], $businessStart);

            if (!isset($monthlySales[$result['billing_bill_rep']])) {
                $monthlySales[$result['billing_bill_rep']] = [];

                $mtdA[$result['billing_bill_rep']]  = ['net_sales' => 0, 'net_profit' => 0];
                $mtdPY[$result['billing_bill_rep']] = ['net_sales' => 0, 'net_profit' => 0];

                $ytdA[$result['billing_bill_rep']]  = ['net_sales' => 0, 'net_profit' => 0];
                $ytdPY[$result['billing_bill_rep']] = ['net_sales' => 0, 'net_profit' => 0];

                for ($i = 1; $i < 3; ++$i) {
                    $monthlySales[$result['billing_bill_rep']][$i] = \array_fill(1, 12, [
                        'net_sales'  => null,
                        'net_profit' => null,
                    ]);
                }
            }

            // indexed according to the fiscal year
            $monthlySales[$result['billing_bill_rep']][$monthIndex] = [
                'net_sales'  => (int) $result['netsales'],
                'net_profit' => (int) $result['netprofit'],
            ];

            if ($monthIndex === $endCurrentIndex) {
                $mtd[$result['billing_bill_rep']] = $monthlySales[$result['billing_bill_rep']][$monthIndex];
            }

            if ($monthIndex <= $endCurrentIndex) {
                $ytd[$result['billing_bill_rep']]['net_sales']  += $monthlySales[$result['billing_bill_rep']][$monthIndex]['net_sales'];
                $ytd[$result['billing_bill_rep']]['net_profit'] += $monthlySales[$result['billing_bill_rep']][$monthIndex]['net_profit'];
            }
        }

        return [$mtd, $ytd, $monthlySales];
    }

    /**
     * @todo Re-implement, still in use?
     */
    public static function annualCustomerRep(
        SmartDateTime $historyStart,
        \DateTime $endCurrent,
        int $businessStart = 1
    ) : array {
        $query = new Builder(self::$db);
        $query->raw(
            'SELECT
                billing_bill_rep,
                YEAR(billing_bill_performance_date) as salesyear,
                MONTH(billing_bill_performance_date) as salesmonth,
                COUNT(billing_bill_netsales) as client_count
            FROM billing_bill
            LEFT JOIN billing_type
                ON billing_bill_type = billing_type_id
            LEFT JOIN clientmgmt_client
                ON clientmgmt_client_id = billing_bill_client
            LEFT JOIN address
                ON clientmgmt_client_address = address_id
            WHERE
                billing_type_transfer_type = ' . BillTransferType::SALES . '
                AND billing_type_accounting = 1
                AND billing_bill_performance_date >= \'' . $historyStart->format('Y-m-d') . '\'
                AND billing_bill_performance_date <= \'' . $endCurrent->format('Y-m-d') . '\'
            GROUP BY
                billing_bill_rep,
                YEAR(billing_bill_performance_date),
                MONTH(billing_bill_performance_date)
            ORDER BY
                YEAR(billing_bill_performance_date) ASC,
                MONTH(billing_bill_performance_date) ASC,
                billing_bill_rep'
        );

        $results = $query->execute()?->fetchAll(\PDO::FETCH_ASSOC) ?? [];

        $annualCustomer = [];

        $oldIndex = 1;
        // @todo this calculation doesn't consider the start of the fiscal year
        $period = (int) (((((int) $results[0]['salesyear']) - ((int) $historyStart->format('Y'))) * 12
            - ((int) $results[0]['salesmonth']) + ((int) $historyStart->format('m'))) / 12 + 1);

        foreach ($results as $result) {
            $monthIndex = SmartDateTime::calculateMonthIndex((int) $result['salesmonth'], $businessStart);
            if ($monthIndex < $oldIndex) {
                $oldIndex = $monthIndex;

                ++$period;
            }

            if ($period > 10) {
                break;
            }

            $oldIndex = $monthIndex;

            if (!isset($annualCustomer[$result['billing_bill_rep']])) {
                for ($i = 1; $i < 11; ++$i) {
                    $annualCustomer[$result['billing_bill_rep']][$i] = [
                        'client_count' => 0,
                    ];

                    $historyStart->smartModify(1);
                }

                $historyStart->smartModify(-10);
            }

            // indexed according to the fiscal year
            $annualCustomer[$result['billing_bill_rep']][$period]['client_count'] += (int) $result['client_count'];
        }

        return $annualCustomer;
    }

    /**
     * @todo Re-implement, still in use?
     */
    public static function mtdYtdClientRep(
        \DateTime $startCurrent,
        \DateTime $endCurrent,
        \DateTime $startComparison,
        \DateTime $endComparison,
        int $businessStart = 1,
    ) : array {
        // @todo this cannot be correct since the same customer may buy something in two month (distinct is required over an actual period)
        $endCurrentIndex = SmartDateTime::calculateMonthIndex((int) $endCurrent->format('m'), $businessStart);

        $query = new Builder(self::$db);
        $query->raw(
            'SELECT
                billing_bill_rep,
                YEAR(billing_bill_performance_date) as salesyear,
                MONTH(billing_bill_performance_date) as salesmonth,
                COUNT(billing_bill_netsales) as client_count
            FROM billing_bill
            LEFT JOIN billing_type
                ON billing_bill_type = billing_type_id
            LEFT JOIN clientmgmt_client
                ON clientmgmt_client_id = billing_bill_client
            LEFT JOIN address
                ON clientmgmt_client_address = address_id
            WHERE
                billing_type_transfer_type = ' . BillTransferType::SALES . '
                AND billing_type_accounting = 1
                AND billing_bill_performance_date >= \'' . $startComparison->format('Y-m-d') . '\'
                AND billing_bill_performance_date <= \'' . $endCurrent->format('Y-m-d') . '\'
            GROUP BY
                billing_bill_rep,
                YEAR(billing_bill_performance_date),
                MONTH(billing_bill_performance_date)
            ORDER BY
                YEAR(billing_bill_performance_date) ASC,
                MONTH(billing_bill_performance_date) ASC,
                billing_bill_rep ASC'
        );

        $results = $query->execute()?->fetchAll(\PDO::FETCH_ASSOC) ?? [];

        $oldIndex = 1;
        $period   = 1;

        $mtdAClientCountry  = [];
        $mtdPYClientCountry = [];

        $ytdAClientCountry  = [];
        $ytdPYClientCountry = [];

        foreach ($results as $result) {
            $monthIndex = SmartDateTime::calculateMonthIndex((int) $result['salesmonth'], $businessStart);
            if ($monthIndex < $oldIndex) {
                $oldIndex = $monthIndex;

                ++$period;
            }

            if ($period > 2) {
                break;
            }

            $oldIndex = $monthIndex;

            // indexed according to the fiscal year
            $temp = [
                'client_count' => (int) $result['client_count'],
            ];

            if ($temp['client_count'] === 0) {
                continue;
            }

            if ($monthIndex === $endCurrentIndex) {
                if ($period === 1) {
                    $mtdPYClientCountry[$result['billing_bill_rep']] = $temp;
                } else {
                    $mtdAClientCountry[$result['billing_bill_rep']] = $temp;
                }
            }

            if ($monthIndex <= $endCurrentIndex) {
                if (!isset($ytdPYClientCountry[$result['billing_bill_rep']])) {
                    $ytdPYClientCountry[$result['billing_bill_rep']] = [
                        'client_count' => 0,
                    ];

                    $ytdAClientCountry[$result['billing_bill_rep']] = [
                        'client_count' => 0,
                    ];
                }

                if ($period === 1) {
                    $ytdPYClientCountry[$result['billing_bill_rep']]['client_count'] += $temp['client_count'];
                } else {
                    $ytdAClientCountry[$result['billing_bill_rep']]['client_count'] += $temp['client_count'];
                }
            }
        }

        return [
            $mtdPYClientCountry,
            $mtdAClientCountry,
            $ytdPYClientCountry,
            $ytdAClientCountry,
        ];
    }

    // @todo remove businessStart, that should be baked into the historyStart
    // Explanation: in the past I had to compare periods which weren't even business years!!!
    /**
     * @todo Re-implement, still in use?
     */
    public static function salesProfitRep(
        \DateTime $historyStart,
        \DateTime $historyEnd,
        \DateTime $currentStart,
        \DateTime $currentEnd
    ) : array {
        $query = new Builder(self::$db);
        $query->raw(
            'SELECT
                billing_bill_rep,
                YEAR(billing_bill_performance_date) as salesyear,
                MONTH(billing_bill_performance_date) as salesmonth,
                SUM(billing_bill_netsales * billing_type_sign) as netsales,
                SUM(billing_bill_netprofit * billing_type_sign) as netprofit
            FROM billing_bill
            LEFT JOIN billing_type
                ON billing_bill_type = billing_type_id
            LEFT JOIN clientmgmt_client
                ON clientmgmt_client_id = billing_bill_client
            LEFT JOIN address
                ON clientmgmt_client_address = address_id
            WHERE
                billing_type_transfer_type = ' . BillTransferType::SALES . '
                AND billing_type_accounting = 1
                AND billing_bill_performance_date >= \'' . $historyStart->format('Y-m-d') . '\'
                AND billing_bill_performance_date <= \'' . $currentEnd->format('Y-m-d') . '\'
            GROUP BY
                billing_bill_rep,
                YEAR(billing_bill_performance_date),
                MONTH(billing_bill_performance_date)
            ORDER BY
                YEAR(billing_bill_performance_date) ASC,
                MONTH(billing_bill_performance_date) ASC,
                billing_bill_rep ASC'
        );

        $results = $query->execute()?->fetchAll(\PDO::FETCH_ASSOC) ?? [];

        $sales  = [];
        $period = 0;

        foreach ($results as $result) {
            // @todo Handle fiscal year
            $period = $result['salesyear'] - ((int) $historyStart->format('Y')) + 1;
            if (!isset($sales[$result['billing_bill_rep']])) {
                for ($i = 1; $i < 11; ++$i) {
                    $sales[$result['billing_bill_rep']][$i] = [
                        'net_sales'  => 0,
                        'net_profit' => 0,
                        'year'       => $period === 0 ? 'PY' : 'A',
                    ];
                }
            }

            $sales[$result['billing_bill_rep']][$period]['net_sales']  += (int) $result['netsales'];
            $sales[$result['billing_bill_rep']][$period]['net_profit'] += (int) $result['netprofit'];
        }

        return $sales;
    }

    /**
     * @todo Re-implement, still in use?
     */
    public static function mtdYtdRep(
        \DateTime $startCurrent,
        \DateTime $endCurrent,
        \DateTime $startComparison,
        \DateTime $endComparison,
        int $businessStart = 1,
    ) : array {
        $endCurrentIndex = SmartDateTime::calculateMonthIndex((int) $endCurrent->format('m'), $businessStart);

        $query = new Builder(self::$db);
        $query->raw(
            'SELECT
                billing_bill_rep,
                YEAR(billing_bill_performance_date) as salesyear,
                MONTH(billing_bill_performance_date) as salesmonth,
                SUM(billing_bill_netsales * billing_type_sign) as netsales,
                SUM(billing_bill_netprofit * billing_type_sign) as netprofit
            FROM billing_bill
            LEFT JOIN billing_type
                ON billing_bill_type = billing_type_id
            LEFT JOIN clientmgmt_client
                ON clientmgmt_client_id = billing_bill_client
            LEFT JOIN address
                ON clientmgmt_client_address = address_id
            WHERE
                billing_type_transfer_type = ' . BillTransferType::SALES . '
                AND billing_type_accounting = 1
                AND billing_bill_performance_date >= \'' . $startComparison->format('Y-m-d') . '\'
                AND billing_bill_performance_date <= \'' . $endCurrent->format('Y-m-d') . '\'
            GROUP BY
                billing_bill_rep,
                YEAR(billing_bill_performance_date),
                MONTH(billing_bill_performance_date)
            ORDER BY
                YEAR(billing_bill_performance_date) ASC,
                MONTH(billing_bill_performance_date) ASC,
                billing_bill_rep ASC'
        );

        $results = $query->execute()?->fetchAll(\PDO::FETCH_ASSOC) ?? [];

        $oldIndex = 1;
        $period   = 1;

        $mtdAClientCountry  = [];
        $mtdPYClientCountry = [];

        $ytdAClientCountry  = [];
        $ytdPYClientCountry = [];

        foreach ($results as $result) {
            $monthIndex = SmartDateTime::calculateMonthIndex((int) $result['salesmonth'], $businessStart);
            if ($monthIndex < $oldIndex) {
                $oldIndex = $monthIndex;

                ++$period;
            }

            if ($period > 2) {
                break;
            }

            $oldIndex = $monthIndex;

            // indexed according to the fiscal year
            $temp = [
                'net_sales'  => (int) $result['netsales'],
                'net_profit' => (int) $result['netprofit'],
            ];

            if (($temp['net_sales'] === 0 && $temp['net_profit'] === 0)) {
                continue;
            }

            if ($monthIndex === $endCurrentIndex) {
                if ($period === 1) {
                    $mtdPYClientCountry[$result['billing_bill_rep']] = $temp;
                } else {
                    $mtdAClientCountry[$result['billing_bill_rep']] = $temp;
                }
            }

            if ($monthIndex <= $endCurrentIndex) {
                if (!isset($ytdPYClientCountry[$result['billing_bill_rep']])) {
                    $ytdPYClientCountry[$result['billing_bill_rep']] = [
                        'net_sales'  => 0,
                        'net_profit' => 0,
                    ];

                    $ytdAClientCountry[$result['billing_bill_rep']] = [
                        'net_sales'  => 0,
                        'net_profit' => 0,
                    ];
                }

                if ($period === 1) {
                    $ytdPYClientCountry[$result['billing_bill_rep']]['net_sales']  += $temp['net_sales'];
                    $ytdPYClientCountry[$result['billing_bill_rep']]['net_profit'] += $temp['net_profit'];
                } else {
                    $ytdAClientCountry[$result['billing_bill_rep']]['net_sales']  += $temp['net_sales'];
                    $ytdAClientCountry[$result['billing_bill_rep']]['net_profit'] += $temp['net_profit'];
                }
            }
        }

        return [
            $mtdPYClientCountry,
            $mtdAClientCountry,
            $ytdPYClientCountry,
            $ytdAClientCountry,
        ];
    }
}
