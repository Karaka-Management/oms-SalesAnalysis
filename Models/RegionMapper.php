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
use phpOMS\Localization\ISO3166TwoEnum;
use phpOMS\Stdlib\Base\SmartDateTime;

/**
 * Permission category enum.
 *
 * @package Modules\SalesAnalysis\Models
 * @license OMS License 1.0
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @todo the periods are wrong if they are disjunct (e.g. A vs PPY)
 * solution: functions need a clear start-end time and then called twice for A vs PY comparison
 */
class RegionMapper extends DataMapperFactory
{
    public static function monthlySalesProfit(
        \DateTime $start,
        \DateTime $end,
        int $businessStart = 1
    ) {
        $endCurrentIndex = SmartDateTime::calculateMonthIndex((int) $end->format('m'), $businessStart);

        $query = new Builder(self::$db);
        $query->raw(
            'SELECT
                address_country,
                YEAR(billing_bill_performance_date) as salesyear,
                MONTH(billing_bill_performance_date) as salesmonth,
                SUM(billing_bill_netsales) as netsales,
                SUM(billing_bill_netprofit) as netprofit
            FROM billing_bill
            LEFT JOIN clientmgmt_client
                ON clientmgmt_client_id = billing_bill_client
            LEFT JOIN address
                ON clientmgmt_client_address = address_id
            WHERE
                billing_bill_type = ' . BillTransferType::SALES . '
                AND billing_bill_performance_date >= \'' . $start->format('Y-m-d') . '\'
                AND billing_bill_performance_date <= \'' . $end->format('Y-m-d') . '\'
            GROUP BY
                address_country,
                YEAR(billing_bill_performance_date),
                MONTH(billing_bill_performance_date)
            ORDER BY
                YEAR(billing_bill_performance_date) ASC,
                MONTH(billing_bill_performance_date) ASC,
                address_country'
        );

        $results = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);

        $monthlySales = [];

        $mtd = [];
        $ytd = [];

        foreach ($results as $result) {
            $monthIndex = SmartDateTime::calculateMonthIndex((int) $result['salesmonth'], $businessStart);

            if (!isset($monthlySales[$result['address_country']])) {
                $monthlySales[$result['address_country']] = [];

                $mtdA[$result['address_country']]  = ['net_sales' => 0, 'net_profit' => 0];
                $mtdPY[$result['address_country']] = ['net_sales' => 0, 'net_profit' => 0];

                $ytdA[$result['address_country']]  = ['net_sales' => 0, 'net_profit' => 0];
                $ytdPY[$result['address_country']] = ['net_sales' => 0, 'net_profit' => 0];

                for ($i = 1; $i < 3; ++$i) {
                    $monthlySales[$result['address_country']][$i] = \array_fill(1, 12, [
                        'net_sales'  => null,
                        'net_profit' => null,
                    ]);
                }
            }

            // indexed according to the fiscal year
            $monthlySales[$result['address_country']][$monthIndex] = [
                'net_sales'  => (int) $result['netsales'],
                'net_profit' => (int) $result['netprofit'],
            ];

            if ($monthIndex === $endCurrentIndex) {
                $mtd[$result['address_country']] = $monthlySales[$result['address_country']][$monthIndex];
            }

            if ($monthIndex <= $endCurrentIndex) {
                $ytd[$result['address_country']]['net_sales']  += $monthlySales[$result['address_country']][$monthIndex]['net_sales'];
                $ytd[$result['address_country']]['net_profit'] += $monthlySales[$result['address_country']][$monthIndex]['net_profit'];
            }
        }

        return [$mtd, $ytd, $monthlySales];
    }

    public static function annualCustomerCountry(
        SmartDateTime $historyStart,
        \DateTime $endCurrent,
        int $businessStart = 1
    ) : array {
        $query = new Builder(self::$db);
        $query->raw(
            'SELECT
                address_country,
                YEAR(billing_bill_performance_date) as salesyear,
                MONTH(billing_bill_performance_date) as salesmonth,
                COUNT(billing_bill_netsales) as client_count
            FROM billing_bill
            LEFT JOIN clientmgmt_client
                ON clientmgmt_client_id = billing_bill_client
            LEFT JOIN address
                ON clientmgmt_client_address = address_id
            WHERE
                billing_bill_type = ' . BillTransferType::SALES . '
                AND billing_bill_performance_date >= \'' . $historyStart->format('Y-m-d') . '\'
                AND billing_bill_performance_date <= \'' . $endCurrent->format('Y-m-d') . '\'
            GROUP BY
                address_country,
                YEAR(billing_bill_performance_date),
                MONTH(billing_bill_performance_date)
            ORDER BY
                YEAR(billing_bill_performance_date) ASC,
                MONTH(billing_bill_performance_date) ASC,
                address_country'
        );

        $results = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);

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

            if (!isset($annualCustomer[$result['address_country']])) {
                for ($i = 1; $i < 11; ++$i) {
                    $annualCustomer[$result['address_country']][$i] = [
                        'client_count' => 0,
                    ];

                    $historyStart->smartModify(1);
                }

                $historyStart->smartModify(-10);
            }

            // indexed according to the fiscal year
            $annualCustomer[$result['address_country']][$period]['client_count'] += (int) $result['client_count'];
        }

        return $annualCustomer;
    }

    public static function mtdYtdClientCountry(
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
                address_country,
                YEAR(billing_bill_performance_date) as salesyear,
                MONTH(billing_bill_performance_date) as salesmonth,
                COUNT(billing_bill_netsales) as client_count
            FROM billing_bill
            LEFT JOIN clientmgmt_client
                ON clientmgmt_client_id = billing_bill_client
            LEFT JOIN address
                ON clientmgmt_client_address = address_id
            WHERE
                billing_bill_type = ' . BillTransferType::SALES . '
                AND billing_bill_performance_date >= \'' . $startComparison->format('Y-m-d') . '\'
                AND billing_bill_performance_date <= \'' . $endCurrent->format('Y-m-d') . '\'
            GROUP BY
                address_country,
                YEAR(billing_bill_performance_date),
                MONTH(billing_bill_performance_date)
            ORDER BY
                YEAR(billing_bill_performance_date) ASC,
                MONTH(billing_bill_performance_date) ASC,
                address_country ASC'
        );

        $results = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);

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
                    $mtdPYClientCountry[$result['address_country']] = $temp;
                } else {
                    $mtdAClientCountry[$result['address_country']] = $temp;
                }
            }

            if ($monthIndex <= $endCurrentIndex) {
                if (!isset($ytdPYClientCountry[$result['address_country']])) {
                    $ytdPYClientCountry[$result['address_country']] = [
                        'client_count' => 0,
                    ];

                    $ytdAClientCountry[$result['address_country']] = [
                        'client_count' => 0,
                    ];
                }

                if ($period === 1) {
                    $ytdPYClientCountry[$result['address_country']]['client_count'] += $temp['client_count'];
                } else {
                    $ytdAClientCountry[$result['address_country']]['client_count'] += $temp['client_count'];
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
    public static function salesProfitCountry(
        SmartDateTime $historyStart,
        SmartDateTime $historyEnd,
        \DateTime $currentStart,
        \DateTime $currentEnd
    ) : array {
        $query = new Builder(self::$db);
        $query->raw(
            'SELECT
                address_country,
                billing_bill_performance_date,
                SUM(billing_bill_netsales) as netsales,
                SUM(billing_bill_netprofit) as netprofit
            FROM billing_bill
            LEFT JOIN clientmgmt_client
                ON clientmgmt_client_id = billing_bill_client
            LEFT JOIN address
                ON clientmgmt_client_address = address_id
            WHERE
                billing_bill_type = ' . BillTransferType::SALES . '
                AND billing_bill_performance_date >= \'' . $historyStart->format('Y-m-d') . '\'
                AND billing_bill_performance_date <= \'' . $currentEnd->format('Y-m-d') . '\'
            GROUP BY
                address_country, billing_bill_performance_date
            ORDER BY
                billing_bill_performance_date ASC,
                address_country'
        );

        $results = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);

        $sales  = [];
        $period = 0;

        foreach ($results as $result) {
            $date = new \DateTime($result['billing_bill_performance_date']);
            if ($date->getTimestamp() <= $historyEnd->getTimestamp()) {
                $period = 0;
            } elseif ($date->getTimestamp() >= $currentStart->getTimestamp()) {
                $period = 1;
            } else {
                continue;
            }

            if (!isset($sales[$result['address_country']])) {
                for ($i = 1; $i < 11; ++$i) {
                    $sales[$result['address_country']][$i] = [
                        'net_sales'  => 0,
                        'net_profit' => 0,
                        'year'       => $period === 0 ? 'PY' : 'A',
                    ];
                }
            }

            $sales[$result['address_country']][$period]['net_sales']  += (int) $result['netsales'];
            $sales[$result['address_country']][$period]['net_profit'] += (int) $result['netprofit'];
        }

        return $sales;
    }

    public static function mtdYtdCountry(
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
                address_country,
                YEAR(billing_bill_performance_date) as salesyear,
                MONTH(billing_bill_performance_date) as salesmonth,
                SUM(billing_bill_netsales) as netsales,
                SUM(billing_bill_netprofit) as netprofit
            FROM billing_bill
            LEFT JOIN clientmgmt_client
                ON clientmgmt_client_id = billing_bill_client
            LEFT JOIN address
                ON clientmgmt_client_address = address_id
            WHERE
                billing_bill_type = ' . BillTransferType::SALES . '
                AND billing_bill_performance_date >= \'' . $startComparison->format('Y-m-d') . '\'
                AND billing_bill_performance_date <= \'' . $endCurrent->format('Y-m-d') . '\'
            GROUP BY
                address_country,
                YEAR(billing_bill_performance_date),
                MONTH(billing_bill_performance_date)
            ORDER BY
                YEAR(billing_bill_performance_date) ASC,
                MONTH(billing_bill_performance_date) ASC,
                address_country ASC'
        );

        $results = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);

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
                    $mtdPYClientCountry[$result['address_country']] = $temp;
                } else {
                    $mtdAClientCountry[$result['address_country']] = $temp;
                }
            }

            if ($monthIndex <= $endCurrentIndex) {
                if (!isset($ytdPYClientCountry[$result['address_country']])) {
                    $ytdPYClientCountry[$result['address_country']] = [
                        'net_sales'  => 0,
                        'net_profit' => 0,
                    ];

                    $ytdAClientCountry[$result['address_country']] = [
                        'net_sales'  => 0,
                        'net_profit' => 0,
                    ];
                }

                if ($period === 1) {
                    $ytdPYClientCountry[$result['address_country']]['net_sales']  += $temp['net_sales'];
                    $ytdPYClientCountry[$result['address_country']]['net_profit'] += $temp['net_profit'];
                } else {
                    $ytdAClientCountry[$result['address_country']]['net_sales']  += $temp['net_sales'];
                    $ytdAClientCountry[$result['address_country']]['net_profit'] += $temp['net_profit'];
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

    public static function countryToRegion(array $countries, array $region, array $columns) : array
    {
        $tempStruct = [];
        foreach ($columns as $column) {
            $tempStruct[$column] = 0;
        }

        $regions = ['Other' => $tempStruct];

        foreach ($region as $r) {
            $definitions[$r] = ($temp = ISO3166TwoEnum::getRegion($r)) === [] ? [$r] : $temp;
            $regions[$r]     = $tempStruct;
        }

        foreach ($countries as $country => $data) {
            $found = false;
            foreach ($definitions as $r => $c) {
                if (\in_array($country, $c)) {
                    foreach ($columns as $column) {
                        $regions[$r][$column] += $data[$column];
                    }

                    $found = true;
                }
            }

            if (!$found) {
                foreach ($columns as $column) {
                    $regions['Other'][$column] += $data[$column];
                }
            }
        }

        return $regions;
    }

    public static function countryIntervalToRegion(array $countries, array $region, array $columns) : array
    {
        if (empty($countries)) {
            return [];
        }

        $count = \count(\reset($countries));

        $tempStruct = [];
        foreach ($columns as $column) {
            $tempStruct[$column] = 0;
        }

        $regions = [
            'Other' => \array_fill(1, $count, $tempStruct),
        ];

        foreach ($region as $r) {
            $definitions[$r] = ($temp = ISO3166TwoEnum::getRegion($r)) === [] ? [$r] : $temp;
            $regions[$r]     = \array_fill(1, $count, $tempStruct);
        }

        foreach ($countries as $country => $data) {
            $found = false;
            foreach ($definitions as $r => $c) {
                if (\in_array($country, $c)) {
                    foreach ($data as $idx => $value) {
                        foreach ($columns as $column) {
                            $regions[$r][$idx][$column] += $value[$column];
                        }
                    }

                    $found = true;
                }
            }

            if (!$found) {
                foreach ($data as $idx => $value) {
                    foreach ($columns as $column) {
                        $regions['Other'][$idx][$column] += $value[$column];
                    }
                }
            }
        }

        return $regions;
    }
}
