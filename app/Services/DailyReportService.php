<?php

namespace App\Services;

use App\Models\DailyReportConfig;
use App\Models\TSO;
use App\Models\SaleOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class DailyReportService
{
    /**
     * Get the data needed to render the daily report PDF.
     *
     * @param DailyReportConfig $config
     * @param string $dateStr (Y-m-d format)
     * @return array
     */
    public function getReportData(DailyReportConfig $config, string $dateStr, $targetCityId = null)
    {
        $cityIds = $targetCityId ? [$targetCityId] : ($config->city_ids ?? []);
        $sections = [];

        // If no specifically selected cities, treat as "All Cities" (one block)
        if (empty($cityIds)) {
            $sections[] = $this->getCityData(null, $config, $dateStr);
        } else {
            foreach ($cityIds as $cityId) {
                $sections[] = $this->getCityData($cityId, $config, $dateStr);
            }
        }

        // Calculate Overall Summary (Grand Totals)
        $overallTotals = [
            'total_orders' => 0,
            'total_qty' => 0,
            'total_amount' => 0,
        ];

        if ($config->show_overall_sales) {
            $overallQuery = SaleOrder::whereDate('dc_date', $dateStr)
                ->where('sale_orders.status', 1);

            if (!empty($cityIds)) {
                $overallQuery->join('tso', 'tso.id', '=', 'sale_orders.tso_id')
                             ->whereIn('tso.city', $cityIds);
            }

            $overall = $overallQuery->select(
                DB::raw('COUNT(sale_orders.id) as total_orders'),
                DB::raw('SUM(sale_orders.total_pcs) as total_qty'),
                DB::raw('SUM(sale_orders.total_amount) as total_amount')
            )->first();
            
            $overallTotals = [
                'total_orders' => $overall->total_orders ?? 0,
                'total_qty' => $overall->total_qty ?? 0,
                'total_amount' => $overall->total_amount ?? 0,
            ];
        }

        return [
            'config' => $config,
            'dateStr' => $dateStr,
            'sections' => $sections,
            'overallTotals' => $overallTotals
        ];
    }

    /**
     * Fetch data for a specific city block.
     */
    private function getCityData($cityId, $config, $dateStr)
    {
        $cityName = 'All Cities';
        if ($cityId) {
            $cityModel = \App\Models\City::find($cityId);
            $cityName = $cityModel ? $cityModel->name : 'Unknown';
        }
        
        $data = [
            'cityName' => $cityName,
            'cityId' => $cityId,
            'overall' => [],
            'tsoData' => [],
            'distributorSales' => [],
            'distributorTotals' => ['orders' => 0, 'qty' => 0, 'amount' => 0],
            'distributorMtdSales' => [],
            'distributorMtdTotals' => ['orders' => 0, 'qty' => 0, 'amount' => 0],
            'productSales' => [],
            'productTotals' => ['qty' => 0],
            'topTsos' => [],
            'bottomTsos' => [],
            'topShops' => [],
            'bottomShops' => [],
            'attendanceDetails' => [],
            'zeroSaleTsos' => [],
        ];

        // Detailed Attendance (Designation-wise)
        if ($config->show_tso_attendance) {
            $attendanceQuery = DB::table('tso as t')
                ->leftJoin('attendences as a', function($join) use ($dateStr) {
                    $join->on('a.user_id', '=', 't.user_id')
                         ->whereDate('a.in', $dateStr);
                })
                ->join('designations as d', 'd.id', '=', 't.designation_id')
                ->where('t.status', 1)
                ->where('t.active', 1);

            if ($cityId) {
                $attendanceQuery->where('t.city', $cityId);
            }

            if (!empty($config->designation_ids)) {
                $attendanceQuery->whereIn('t.designation_id', $config->designation_ids);
            }

            $attendanceRecords = $attendanceQuery->select(
                't.name',
                'd.name as designation',
                'a.in as check_in',
                'a.out as check_out'
            )->orderBy('d.id')->orderBy('t.name')->get();

            $data['attendanceDetails'] = $attendanceRecords->groupBy('designation');

            // Legacy TSO Attendance counters (optional but kept for compatibility)
            $totalTsos = $attendanceRecords->count();
            $presentCount = $attendanceRecords->whereNotNull('check_in')->count();
            $data['tsoData'] = [
                'total' => $totalTsos,
                'present_count' => $presentCount,
                'absent_count' => $totalTsos - $presentCount,
            ];
        }

        // Distributor Sales
        if ($config->show_distributor_sales) {
            $query = DB::table('sale_orders as so')
                ->join('distributors as d', 'd.id', '=', 'so.distributor_id')
                ->whereDate('so.dc_date', $dateStr)
                ->where('so.status', 1);

            if ($cityId) {
                $query->join('tso', 'tso.id', '=', 'so.tso_id')
                      ->where('tso.city', $cityId);
            }

            $data['distributorSales'] = $query->select(
                    'd.distributor_name',
                    DB::raw('COUNT(so.id) as total_orders'),
                    DB::raw('SUM(so.total_pcs) as total_qty'),
                    DB::raw('SUM(so.total_amount) as total_amount')
                )
                ->groupBy('d.id', 'd.distributor_name')
                ->orderByDesc('total_amount')
                ->get();

            foreach ($data['distributorSales'] as $dist) {
                $data['distributorTotals']['orders'] += $dist->total_orders;
                $data['distributorTotals']['qty'] += $dist->total_qty;
                $data['distributorTotals']['amount'] += $dist->total_amount;
            }

            // MTD Distributor Sales (1st of month to today)
            $monthStart = Carbon::parse($dateStr)->startOfMonth()->format('Y-m-d');
            $mtdQuery = DB::table('sale_orders as so')
                ->join('distributors as d', 'd.id', '=', 'so.distributor_id')
                ->whereBetween('so.dc_date', [$monthStart, $dateStr])
                ->where('so.status', 1);

            if ($cityId) {
                $mtdQuery->join('tso as t_mtd', 't_mtd.id', '=', 'so.tso_id')
                          ->where('t_mtd.city', $cityId);
            }

            $data['distributorMtdSales'] = $mtdQuery->select(
                    'd.distributor_name',
                    DB::raw('COUNT(so.id) as total_orders'),
                    DB::raw('SUM(so.total_pcs) as total_qty'),
                    DB::raw('SUM(so.total_amount) as total_amount')
                )
                ->groupBy('d.id', 'd.distributor_name')
                ->orderByDesc('total_amount')
                ->get();

            foreach ($data['distributorMtdSales'] as $dist) {
                $data['distributorMtdTotals']['orders'] += $dist->total_orders;
                $data['distributorMtdTotals']['qty'] += $dist->total_qty;
                $data['distributorMtdTotals']['amount'] += $dist->total_amount;
            }
        }

        // Product Sales
        if ($config->show_product_sales) {
            $query = DB::table('sale_orders as so')
                ->join('sale_order_data as sod', 'sod.so_id', '=', 'so.id')
                ->join('products as p', 'p.id', '=', 'sod.product_id')
                ->whereDate('so.dc_date', $dateStr)
                ->where('so.status', 1);

            if ($cityId) {
                $query->join('tso', 'tso.id', '=', 'so.tso_id')
                      ->where('tso.city', $cityId);
            }

            $data['productSales'] = $query->select(
                    'p.product_name',
                    DB::raw('SUM(sod.qty) as total_qty')
                )
                ->groupBy('p.id', 'p.product_name')
                ->orderByDesc('total_qty')
                ->get();

            foreach ($data['productSales'] as $prod) {
                $data['productTotals']['qty'] += $prod->total_qty;
            }
        }

        // Top/Bottom TSO
        if ($config->show_top_bottom_tso) {
            $query = DB::table('sale_orders as so')
                ->join('tso', 'tso.id', '=', 'so.tso_id')
                ->join('distributors as d', 'd.id', '=', 'so.distributor_id')
                ->whereDate('so.dc_date', $dateStr)
                ->where('so.status', 1);

            if ($cityId) {
                $query->where('tso.city', $cityId);
            }

            $tsoSales = $query->select(
                    'tso.name as tso_name',
                    'd.distributor_name',
                    DB::raw('SUM(so.total_amount) as total_amount')
                )
                ->groupBy('tso.id', 'tso.name', 'd.distributor_name')
                ->orderByDesc('total_amount')
                ->get();

            $data['topTsos'] = $tsoSales->take(10);
            $data['bottomTsos'] = $tsoSales->slice(-10)->reverse();
        }

        // Top/Bottom Shop
        if ($config->show_top_bottom_shop) {
            $query = DB::table('sale_orders as so')
                ->join('shops as s', 's.id', '=', 'so.shop_id')
                ->whereDate('so.dc_date', $dateStr)
                ->where('so.status', 1);

            if ($cityId) {
                $query->join('tso', 'tso.id', '=', 'so.tso_id')
                      ->where('tso.city', $cityId);
            }

            $shopSales = $query->select(
                    's.shop_code',
                    's.company_name as shop_name',
                    DB::raw('SUM(so.total_pcs) as total_qty'),
                    DB::raw('SUM(so.total_amount) as total_amount')
                )
                ->groupBy('s.id', 's.shop_code', 's.company_name')
                ->orderByDesc('total_amount')
                ->get();

            $data['topShops'] = $shopSales->take(10);
            $data['bottomShops'] = $shopSales->slice(-10)->reverse();
        }

        // Zero Sale TSOs (active TSOs with 0 sales today)
        if (!empty($config->show_zero_sale_tso)) {
            $zeroSaleQuery = DB::table('tso as t')
                ->join('designations as d', 'd.id', '=', 't.designation_id')
                ->join('distributors as dist', 'dist.id', '=', 't.distributor_id')
                ->leftJoin('sale_orders as so', function($join) use ($dateStr) {
                    $join->on('so.tso_id', '=', 't.id')
                         ->whereDate('so.dc_date', $dateStr)
                         ->where('so.status', 1);
                })
                ->where('t.status', 1)
                ->where('t.active', 1)
                ->whereNull('so.id'); // no sale today

            if ($cityId) {
                $zeroSaleQuery->where('t.city', $cityId);
            }

            if (!empty($config->zero_sale_designation_ids)) {
                $zeroSaleQuery->whereIn('t.designation_id', $config->zero_sale_designation_ids);
            }

            $zeroSaleRecords = $zeroSaleQuery->select(
                    't.name as tso_name',
                    'd.name as designation',
                    'dist.distributor_name',
                    DB::raw('0 as total_sale')
                )
                ->orderBy('dist.distributor_name')
                ->orderBy('t.name')
                ->get();

            $data['zeroSaleTsos'] = $zeroSaleRecords->groupBy('designation');
        }

        return $data;
    }

    /**
     * Generate the PDF content.
     */
    public function generatePdfContent(DailyReportConfig $config, string $dateStr)
    {
        $data = $this->getReportData($config, $dateStr);
        $pdf = Pdf::loadView('emails.daily_report_pdf', $data);
        return $pdf->output();
    }

    /**
     * Generate the PDF content for a specific city.
     */
    public function generatePdfContentForCity(DailyReportConfig $config, string $dateStr, $cityId)
    {
        $data = $this->getReportData($config, $dateStr, $cityId);
        $pdf = Pdf::loadView('emails.daily_report_pdf', $data);
        return $pdf->output();
    }
}
