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

use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;
use phpOMS\DataStorage\Database\Query\Builder;
use phpOMS\Stdlib\Base\SmartDateTime;

/**
 * Permision state enum.
 *
 * @package Modules\SalesAnalysis\Models
 * @license OMS License 1.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
class GeneralMapper extends DataMapperFactory
{
    public static function monthlySalesProfit(
        \DateTime $startCurrent,
        \DateTime $endCurrent,
        \DateTime $startComparison,
        \DateTime $endComparison,
        int $businessStart = 1
    ) {
        $endCurrentIndex = SmartDateTime::calculateMonthIndex((int) $endCurrent->format('m'), $businessStart);

        $query = new Builder(self::$db);
        $query->raw(
            'SELECT
                YEAR(billing_bill_performance_date) as salesyear,
                MONTH(billing_bill_performance_date) as salesmonth,
                SUM(billing_bill_netsales) as netsales,
                SUM(billing_bill_netprofit) as netprofit
            FROM billing_bill
            WHERE
                billing_bill_performance_date >= \'' . $startComparison->format('Y-m-d') . '\'
                AND billing_bill_performance_date <= \'' . $endCurrent->format('Y-m-d') . '\'
            GROUP BY
                YEAR(billing_bill_performance_date),
                MONTH(billing_bill_performance_date)
            ORDER BY
                YEAR(billing_bill_performance_date) ASC,
                MONTH(billing_bill_performance_date) ASC'
        );

        $results = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);

        $oldIndex = 1;
        $period   = 1;

        $monthlySales = [];
        for ($i = 1; $i < 3; ++$i) {
            $monthlySales[$i] = \array_fill(0, 12, [
                'net_sales' => null,
                'net_profit' => null,
            ]);
        }

        $mtdA = ['net_sales' => 0, 'net_profit' => 0];
        $mtdPY = ['net_sales' => 0, 'net_profit' => 0];

        $ytdA = ['net_sales' => 0, 'net_profit' => 0];
        $ytdPY = ['net_sales' => 0, 'net_profit' => 0];

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
            $monthlySales[$period][$monthIndex - 1] = [
                'net_sales' => (int) $result['netsales'],
                'net_profit' => (int) $result['netprofit'],
            ];

            if ($monthIndex === $endCurrentIndex) {
                if ($period === 1) {
                    $mtdPY = $monthlySales[$period][$monthIndex - 1];
                } else {
                    $mtdA = $monthlySales[$period][$monthIndex - 1];
                }
            }

            if ($monthIndex <= $endCurrentIndex) {
                if ($period === 1) {
                    $ytdPY['net_sales']  += $monthlySales[$period][$monthIndex - 1]['net_sales'];
                    $ytdPY['net_profit'] += $monthlySales[$period][$monthIndex - 1]['net_profit'];
                } else {
                    $ytdA['net_sales']  += $monthlySales[$period][$monthIndex - 1]['net_sales'];
                    $ytdA['net_profit'] += $monthlySales[$period][$monthIndex - 1]['net_profit'];
                }
            }
        }

        return [
            $mtdA, $mtdPY,
            $ytdA, $ytdPY,
            $monthlySales
        ];
    }

    public static function annualSalesProfit(
        SmartDateTime $historyStart,
        \DateTime $endCurrent,
        int $businessStart = 1
    ) : array {

        $query = new Builder(self::$db);
        $query->raw(
            'SELECT
                YEAR(billing_bill_performance_date) as salesyear,
                MONTH(billing_bill_performance_date) as salesmonth,
                SUM(billing_bill_netsales) as netsales,
                SUM(billing_bill_netprofit) as netprofit
            FROM billing_bill
            WHERE
                billing_bill_performance_date >= \'' . $historyStart->format('Y-m-d') . '\'
                AND billing_bill_performance_date <= \'' . $endCurrent->format('Y-m-d') . '\'
            GROUP BY
                YEAR(billing_bill_performance_date),
                MONTH(billing_bill_performance_date)
            ORDER BY
                YEAR(billing_bill_performance_date) ASC,
                MONTH(billing_bill_performance_date) ASC'
        );

        $results = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);

        $annualSales = [];
        for ($i = 1; $i < 11; ++$i) {
            $annualSales[$i] = [
                'net_sales' => null,
                'net_profit' => null,
                'year' => $historyStart->format('Y'),
            ];

            $historyStart->smartModify(1);
        }

        $historyStart->smartModify(-10);

        $oldIndex = 1;
        // @todo: this calculation doesn't consider the start of the fiscal year
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

            // indexed according to the fiscal year
            $annualSales[$period]['net_sales']  ??= 0;
            $annualSales[$period]['net_profit'] ??= 0;

            $annualSales[$period]['net_sales'] += (int) $result['netsales'];
            $annualSales[$period]['net_profit'] += (int) $result['netprofit'];
        }

        return $annualSales;
    }
}