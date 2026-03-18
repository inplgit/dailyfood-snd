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
    public function getReportData(DailyReportConfig $config, string $dateStr)
    {
        $cityIds = $config->city_ids ?? [];
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
        $cityName = $cityId ? (\App\Models\City::find($cityId)->name ?? 'Unknown') : 'All Cities';
        
        $data = [
            'cityName' => $cityName,
            'overall' => [],
            'tsoData' => [],
            'distributorSales' => [],
            'productSales' => [],
            'topTsos' => [],
            'bottomTsos' => [],
            'topShops' => [],
            'bottomShops' => [],
        ];

        // TSO Attendance
        if ($config->show_tso_attendance) {
            $tsoQuery = TSO::with('distributor')
                ->where('status', 1)
                ->where('active', 1);

            if ($cityId) {
                $tsoQuery->where('city', $cityId);
            }

            $allTsos = $tsoQuery->get();
            $allTsoIds = $allTsos->pluck('id')->toArray();
            
            $presentTsoIds = DB::table('attendences')
                ->whereDate('in', $dateStr)
                ->whereIn('tso_id', $allTsoIds)
                ->pluck('tso_id')
                ->toArray();
                
            $absentList = $allTsos->filter(function($tso) use ($presentTsoIds) {
                return !in_array($tso->id, $presentTsoIds);
            })->map(function($tso) {
                return (object)[
                    'name' => $tso->name,
                    'distributor_name' => $tso->distributor ? $tso->distributor->distributor_name : 'N/A'
                ];
            });

            $data['tsoData'] = [
                'total' => $allTsos->count(),
                'present_count' => count($presentTsoIds),
                'absent_count' => $absentList->count(),
                'absent_list' => $absentList
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
}
