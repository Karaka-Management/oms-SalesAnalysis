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
 * Permission category enum.
 *
 * @package Modules\SalesAnalysis\Models
 * @license OMS License 1.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
class ClientMapper extends DataMapperFactory
{
    public static function mtdYtdClientAttribute(
        \DateTime $startCurrent,
        \DateTime $endCurrent,
        \DateTime $startComparison,
        \DateTime $endComparison,
        int $businessStart = 1,
        string $language = 'en'
    ) {
        $endCurrentIndex = SmartDateTime::calculateMonthIndex((int) $endCurrent->format('m'), $businessStart);

        // @todo this query doesn't return clients that have not segment etc. defined.
        $query = new Builder(self::$db);
        $query->raw(
            'SELECT
                clientmgmt_attr_type_name,
                clientmgmt_attr_type_l11n_title,
                clientmgmt_attr_value_id,
                clientmgmt_attr_value_l11n_title,
                YEAR(billing_bill_performance_date) as salesyear,
                MONTH(billing_bill_performance_date) as salesmonth,
                SUM(billing_bill_netsales) as netsales,
                SUM(billing_bill_netprofit) as netprofit
            FROM billing_bill
            LEFT JOIN clientmgmt_client
                ON clientmgmt_client_id = billing_bill_client
            LEFT JOIN clientmgmt_client_attr
                ON clientmgmt_client_id = clientmgmt_client_attr_client
            LEFT JOIN clientmgmt_attr_type
                ON clientmgmt_client_attr_type = clientmgmt_attr_type_id
            LEFT JOIN clientmgmt_attr_type_l11n
                ON clientmgmt_attr_type_id = clientmgmt_attr_type_l11n_type AND clientmgmt_attr_type_l11n_lang = \'' . $language . '\'
            LEFT JOIN clientmgmt_attr_value
                ON clientmgmt_client_attr_value = clientmgmt_attr_value_id
            LEFT JOIN clientmgmt_attr_value_l11n
                ON clientmgmt_attr_value_id = clientmgmt_attr_value_l11n_value AND clientmgmt_attr_value_l11n_lang = \'' . $language . '\'
            WHERE
                billing_bill_performance_date >= \'' . $startComparison->format('Y-m-d') . '\'
                AND billing_bill_performance_date <= \'' . $endCurrent->format('Y-m-d') . '\'
                AND clientmgmt_attr_type_name IN (\'segment\', \'section\', \'client_group\', \'client_type\')
            GROUP BY
                clientmgmt_attr_type_name,
                clientmgmt_attr_type_l11n_title,
                clientmgmt_attr_value_id,
                clientmgmt_attr_value_l11n_title,
                YEAR(billing_bill_performance_date),
                MONTH(billing_bill_performance_date)
            ORDER BY
                YEAR(billing_bill_performance_date) ASC,
                MONTH(billing_bill_performance_date) ASC'
        );

        $results = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);

        $oldIndex = 1;
        $period   = 1;

        $mtdAClientAttribute  = [];
        $mtdPYClientAttribute = [];

        $ytdAClientAttribute  = [];
        $ytdPYClientAttribute = [];

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
                    $mtdPYClientAttribute[$result['clientmgmt_attr_type_name']] = $temp;
                } else {
                    $mtdAClientAttribute[$result['clientmgmt_attr_type_name']] = $temp;
                }
            }

            if ($monthIndex <= $endCurrentIndex) {
                if (!isset($ytdPYClientAttribute[$result['clientmgmt_attr_type_name']])) {
                    $ytdPYClientAttribute[$result['clientmgmt_attr_type_name']] = [
                        'net_sales'  => 0,
                        'net_profit' => 0,
                        'value_l11n' => $result['clientmgmt_attr_value_l11n_title'],
                    ];

                    $ytdAClientAttribute[$result['clientmgmt_attr_type_name']] = [
                        'net_sales'  => 0,
                        'net_profit' => 0,
                        'value_l11n' => $result['clientmgmt_attr_value_l11n_title'],
                    ];
                }

                if ($period === 1) {
                    $ytdPYClientAttribute[$result['clientmgmt_attr_type_name']]['net_sales']  += $temp['net_sales'];
                    $ytdPYClientAttribute[$result['clientmgmt_attr_type_name']]['net_profit'] += $temp['net_profit'];
                } else {
                    $ytdAClientAttribute[$result['clientmgmt_attr_type_name']]['net_sales']  += $temp['net_sales'];
                    $ytdAClientAttribute[$result['clientmgmt_attr_type_name']]['net_profit'] += $temp['net_profit'];
                }
            }
        }

        return [
            $mtdAClientAttribute,
            $mtdPYClientAttribute,
            $ytdAClientAttribute,
            $ytdPYClientAttribute,
        ];
    }
}
