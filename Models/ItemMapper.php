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

use Modules\Billing\Models\BillStatus;
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
class ItemMapper extends DataMapperFactory
{
    /**
     * @todo Re-implement, still in use?
     */
    public static function mtdYtdItemAttribute(
        \DateTime $startCurrent,
        \DateTime $endCurrent,
        \DateTime $startComparison,
        \DateTime $endComparison,
        int $businessStart = 1,
        string $language = 'en'
    ) : array
    {
        $endCurrentIndex = SmartDateTime::calculateMonthIndex((int) $endCurrent->format('m'), $businessStart);

        // @todo this query doesn't return clients that have not segment etc. defined.
        $query = new Builder(self::$db);
        $query->raw(
            'SELECT
                segment, section, sales_group, product_group, product_type,
                YEAR(billing_bill_performance_date) as salesyear,
                MONTH(billing_bill_performance_date) as salesmonth,
                SUM(netsales) AS netsales,
                SUM(netprofit) AS netprofit
            FROM (
                SELECT
                    MAX(CASE WHEN itemmgmt_attr_type.itemmgmt_attr_type_name = \'segment\' THEN itemmgmt_attr_value_l11n.itemmgmt_attr_value_l11n_title END) AS segment,
                    MAX(CASE WHEN itemmgmt_attr_type.itemmgmt_attr_type_name = \'section\' THEN itemmgmt_attr_value_l11n.itemmgmt_attr_value_l11n_title END) AS section,
                    MAX(CASE WHEN itemmgmt_attr_type.itemmgmt_attr_type_name = \'sales_group\' THEN itemmgmt_attr_value_l11n.itemmgmt_attr_value_l11n_title END) AS sales_group,
                    MAX(CASE WHEN itemmgmt_attr_type.itemmgmt_attr_type_name = \'product_group\' THEN itemmgmt_attr_value_l11n.itemmgmt_attr_value_l11n_title END) AS product_group,
                    MAX(CASE WHEN itemmgmt_attr_type.itemmgmt_attr_type_name = \'product_type\' THEN itemmgmt_attr_value_l11n.itemmgmt_attr_value_l11n_title END) AS product_type,
                    billing_bill_performance_date,
                    SUM(billing_bill_element_total_netsalesprice * billing_type_sign) as netsales,
                    SUM(billing_bill_element_total_netprofit * billing_type_sign) as netprofit
                FROM billing_bill
                LEFT JOIN billing_type
                    ON billing_bill_type = billing_type_id
                LEFT JOIN billing_bill_element
                    ON billing_bill_id = billing_bill_element_bill
                LEFT JOIN itemmgmt_item
                    ON itemmgmt_item_id = billing_bill_element_item
                LEFT JOIN itemmgmt_item_attr
                    ON itemmgmt_item_id = itemmgmt_item_attr_item
                LEFT JOIN itemmgmt_attr_type
                    ON itemmgmt_item_attr_type = itemmgmt_attr_type_id
                LEFT JOIN itemmgmt_attr_type_l11n
                    ON itemmgmt_attr_type_id = itemmgmt_attr_type_l11n_type AND itemmgmt_attr_type_l11n_lang = \'' . $language . '\'
                LEFT JOIN itemmgmt_attr_value
                    ON itemmgmt_item_attr_value = itemmgmt_attr_value_id
                LEFT JOIN itemmgmt_attr_value_l11n
                    ON itemmgmt_attr_value_id = itemmgmt_attr_value_l11n_value AND itemmgmt_attr_value_l11n_lang = \'' . $language . '\'
                WHERE
                    billing_type_transfer_type = ' . BillTransferType::SALES . '
                    AND billing_type_accounting = 1
                    AND billing_bill_status = ' . BillStatus::ARCHIVED . '
                    AND billing_bill_performance_date >= \'' . $startComparison->format('Y-m-d') . '\'
                    AND billing_bill_performance_date <= \'' . $endCurrent->format('Y-m-d') . '\'
                    AND itemmgmt_attr_type_name IN (\'segment\', \'section\', \'sales_group\', \'product_group\', \'product_type\')
                GROUP BY billing_bill_performance_date
                ) AS subquery
            GROUP BY
                segment, section, sales_group, product_group, product_type,
                YEAR(billing_bill_performance_date),
                MONTH(billing_bill_performance_date)
            ORDER BY
                YEAR(billing_bill_performance_date) ASC,
                MONTH(billing_bill_performance_date) ASC;'
        );

        $results = $query->execute()?->fetchAll(\PDO::FETCH_ASSOC) ?? [];

        $oldIndex = 1;
        $period   = 1;

        $mtdAItemAttribute  = ['segment' => [], 'section' => [], 'sales_group' => [], 'product_group' => [], 'product_type' => []];
        $mtdPYItemAttribute = ['segment' => [], 'section' => [], 'sales_group' => [], 'product_group' => [], 'product_type' => []];

        $ytdAItemAttribute  = ['segment' => [], 'section' => [], 'sales_group' => [], 'product_group' => [], 'product_type' => []];
        $ytdPYItemAttribute = ['segment' => [], 'section' => [], 'sales_group' => [], 'product_group' => [], 'product_type' => []];

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
                    $mtdPYItemAttribute['segment'][$result['segment']] = $temp;
                    $mtdPYItemAttribute['section'][$result['section']] = $temp;
                    $mtdPYItemAttribute['sales_group'][$result['sales_group']] = $temp;
                    $mtdPYItemAttribute['product_group'][$result['product_group']] = $temp;
                    $mtdPYItemAttribute['product_type'][$result['product_type']] = $temp;
                } else {
                    $mtdAItemAttribute['segment'][$result['segment']] = $temp;
                    $mtdAItemAttribute['section'][$result['section']] = $temp;
                    $mtdAItemAttribute['sales_group'][$result['sales_group']] = $temp;
                    $mtdAItemAttribute['product_group'][$result['product_group']] = $temp;
                    $mtdAItemAttribute['product_type'][$result['product_type']] = $temp;
                }
            }

            if ($monthIndex <= $endCurrentIndex) {
                if (!isset($ytdPYItemAttribute['segment'][$result['segment']])) {
                    $ytdPYItemAttribute['segment'][$result['segment']] = [
                        'net_sales'  => 0,
                        'net_profit' => 0,
                        'value_l11n' => $result['segment'],
                    ];

                    $ytdAItemAttribute['segment'][$result['segment']] = [
                        'net_sales'  => 0,
                        'net_profit' => 0,
                        'value_l11n' => $result['segment'],
                    ];
                }

                if (!isset($ytdPYItemAttribute['section'][$result['section']])) {
                    $ytdPYItemAttribute['section'][$result['section']] = [
                        'net_sales'  => 0,
                        'net_profit' => 0,
                        'value_l11n' => $result['section'],
                    ];

                    $ytdAItemAttribute['section'][$result['section']] = [
                        'net_sales'  => 0,
                        'net_profit' => 0,
                        'value_l11n' => $result['section'],
                    ];
                }

                if (!isset($ytdPYItemAttribute['sales_group'][$result['sales_group']])) {
                    $ytdPYItemAttribute['sales_group'][$result['sales_group']] = [
                        'net_sales'  => 0,
                        'net_profit' => 0,
                        'value_l11n' => $result['sales_group'],
                    ];

                    $ytdAItemAttribute['sales_group'][$result['sales_group']] = [
                        'net_sales'  => 0,
                        'net_profit' => 0,
                        'value_l11n' => $result['sales_group'],
                    ];
                }

                if (!isset($ytdPYItemAttribute['product_group'][$result['product_group']])) {
                    $ytdPYItemAttribute['product_group'][$result['product_group']] = [
                        'net_sales'  => 0,
                        'net_profit' => 0,
                        'value_l11n' => $result['product_group'],
                    ];

                    $ytdAItemAttribute['product_group'][$result['product_group']] = [
                        'net_sales'  => 0,
                        'net_profit' => 0,
                        'value_l11n' => $result['product_group'],
                    ];
                }

                if (!isset($ytdPYItemAttribute['product_type'][$result['product_type']])) {
                    $ytdPYItemAttribute['product_type'][$result['product_type']] = [
                        'net_sales'  => 0,
                        'net_profit' => 0,
                        'value_l11n' => $result['product_type'],
                    ];

                    $ytdAItemAttribute['product_type'][$result['product_type']] = [
                        'net_sales'  => 0,
                        'net_profit' => 0,
                        'value_l11n' => $result['product_type'],
                    ];
                }

                if ($period === 1) {
                    $ytdPYItemAttribute['segment'][$result['segment']]['net_sales']  += $temp['net_sales'];
                    $ytdPYItemAttribute['segment'][$result['segment']]['net_profit'] += $temp['net_profit'];
                    $ytdPYItemAttribute['section'][$result['section']]['net_sales']  += $temp['net_sales'];
                    $ytdPYItemAttribute['section'][$result['section']]['net_profit'] += $temp['net_profit'];
                    $ytdPYItemAttribute['sales_group'][$result['sales_group']]['net_sales']  += $temp['net_sales'];
                    $ytdPYItemAttribute['sales_group'][$result['sales_group']]['net_profit'] += $temp['net_profit'];
                    $ytdPYItemAttribute['product_group'][$result['product_group']]['net_sales']  += $temp['net_sales'];
                    $ytdPYItemAttribute['product_group'][$result['product_group']]['net_profit'] += $temp['net_profit'];
                    $ytdPYItemAttribute['product_type'][$result['product_type']]['net_sales']  += $temp['net_sales'];
                    $ytdPYItemAttribute['product_type'][$result['product_type']]['net_profit'] += $temp['net_profit'];
                } else {
                    $ytdAItemAttribute['segment'][$result['segment']]['net_sales']  += $temp['net_sales'];
                    $ytdAItemAttribute['segment'][$result['segment']]['net_profit'] += $temp['net_profit'];
                    $ytdAItemAttribute['section'][$result['section']]['net_sales']  += $temp['net_sales'];
                    $ytdAItemAttribute['section'][$result['section']]['net_profit'] += $temp['net_profit'];
                    $ytdAItemAttribute['sales_group'][$result['sales_group']]['net_sales']  += $temp['net_sales'];
                    $ytdAItemAttribute['sales_group'][$result['sales_group']]['net_profit'] += $temp['net_profit'];
                    $ytdAItemAttribute['product_group'][$result['product_group']]['net_sales']  += $temp['net_sales'];
                    $ytdAItemAttribute['product_group'][$result['product_group']]['net_profit'] += $temp['net_profit'];
                    $ytdAItemAttribute['product_type'][$result['product_type']]['net_sales']  += $temp['net_sales'];
                    $ytdAItemAttribute['product_type'][$result['product_type']]['net_profit'] += $temp['net_profit'];
                }
            }
        }

        return [
            $mtdAItemAttribute,
            $mtdPYItemAttribute,
            $ytdAItemAttribute,
            $ytdPYItemAttribute,
        ];
    }
}
