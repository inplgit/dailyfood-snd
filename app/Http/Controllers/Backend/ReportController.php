<?php

namespace App\Http\Controllers\Backend;

use Auth;
use Carbon\Carbon;
use App\Models\TSO;
use App\Models\Rack;
use App\Models\Shop;
use App\Models\Stock;
use App\Models\Product;
use App\Models\SaleOrder;
use App\Models\TSOTarget;
use App\Models\AssignRack;
use App\Models\Attendence;
use App\Models\Distributor;
use App\Models\SalesReturn;
use App\Models\ProductPrice;
use Illuminate\Http\Request;
use App\Models\SaleOrderData;
use App\Models\ReceiptVoucher;
use Yajra\DataTables\DataTables;
use App\Helpers\MasterFormsHelper;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class ReportController extends Controller
{
    public $page;
    public function __construct()
    {

        $this->master = new MasterFormsHelper();
        $this->page = 'pages.Reports.';

    }




    public function brand_wise_daily_sale(Request $request)
    {
        ini_set('max_execution_time', 2400);
        $from = $request->from;
        $to = $request->to;

        if ($request->ajax()) {
            // Generate date range array
            $dates = [];
            $current = strtotime($from);
            $last = strtotime($to);

            while ($current <= $last) {
                $dates[] = date('Y-m-d', $current);
                $current = strtotime('+1 day', $current);
            }

            $monthfrom = date('m', strtotime($from));
            $monthto = date('m', strtotime($to));
            $monthYear = explode('-', $request->from);

            $tsosQuery = TSO::with([
                'attendence' => function ($query) use ($request) {
                    $query->whereBetween(DB::raw('DATE(`in`)'), [$request->from, $request->to]);

                    if ($request->tso_id) {
                        $query->groupBy(DB::raw('DATE(`in`)'));
                    }
                },
                'designation',
                'distributor',
                'cities',
                'saleOrders' => function ($query) use ($request) {
                    $query->where('status', 1);

                    if ($request->from && $request->to) {
                        $query->whereBetween('dc_date', [$request->from, $request->to]);
                    }

                    if ($request->shop_id) {
                        $query->where('sale_orders.shop_id', $request->shop_id);
                    }
                    $query->whereNotNull('dc_date');

                    $query->whereIn('sale_orders.distributor_id', function ($subQuery) {
                        $subQuery->select('distributor_id')
                                ->from('tso')
                                ->whereColumn('tso.id', 'sale_orders.tso_id');
                    });
                    if ($request->route_id) {
                        $query->whereIn('sale_orders.shop_id', function ($subQuery) use ($request) {
                            $subQuery->select('id')
                                    ->from('shops')
                                    ->where('route_id', $request->route_id);
                        });
                    }
                }
            ])
            ->whereHas('distributor', function ($q) {
                $q->where('status', 1);
            })
            ->where('status', 1)
            ->whereIn('distributor_id', $this->master->get_users_distributors(Auth::user()->id))
            ->when($request->distributor_id, function ($query) use ($request) {
                $query->where('distributor_id', $request->distributor_id);
            })
            ->when($request->tso_id, function ($query) use ($request) {
                $query->where('id', $request->tso_id);
            })
            ->when(empty($request->tso_id) && empty($request->distributor_id), function ($query) use ($request) {
                $query->whereHas('saleOrders', function ($q) use ($request) {
                    $q->where('status', 1)
                    ->whereBetween('dc_date', [$request->from, $request->to]);
                });
            })
            ->when($request->designation, function ($query) use ($request) {
                $query->where('designation_id', $request->designation);
            })
            ->when($request->city, function ($query) use ($request) {
                $query->where('city', $request->city);
            });

            $tsos = $tsosQuery->get();
            $products = $this->master->get_all_product();

            // Extra filter variables passed to Blade
            $city = $request->city;
            $distributor_id = $request->distributor_id;
            $tso_id = $request->tso_id;

            $view = 'NationalSummary.national_item_detail_ajax_new';
            return view($this->page . $view, compact(
                'tsos',
                'from',
                'to',
                'monthYear',
                'monthto',
                'monthfrom',
                'dates',
                'products',
                'city',
                'distributor_id',
                'tso_id'
            ));
        }

        return view($this->page . 'NationalSummary.national_summary_new');
    }


    public function order_booker_target_report_old(Request $request)
    {
        $tso_id         = $request->tso_id;
        $shop_id        = $request->shop_id;
        $route_id       = $request->route_id;
        $distributor_id = $request->distributor_id;
 
        if ($request->ajax()) :
            $from = date('Y-m-d', strtotime($request->from));
            $to   = date('Y-m-d', strtotime($request->to));

            $monthfrom = date('m', strtotime($request->from));
            $monthto   = date('m', strtotime($request->to));

            $target_type = $request->target_type;
 
            $tso_target = TSOTarget::leftJoin('products', 'products.id', '=', 'tso_targets.product_id')
                ->join('tso', 'tso.id', '=', 'tso_targets.tso_id')
                ->join('users_distributors', 'tso.user_id', '=', 'users_distributors.user_id')
                ->when($request->distributor_id == null, function ($query) {
                    $query->whereIn(
                        'users_distributors.distributor_id',
                        MasterFormsHelper::get_users_distributors(Auth::user()->id)
                    );
                })
                ->join('distributors', 'distributors.id', '=', 'tso.distributor_id')
                ->leftJoin('product_prices', function ($join) {
                    $join->on('product_prices.product_id', '=', 'products.id')
                         ->where('product_prices.status', 1); // Only fetch active prices
                }) 
                // ->join('shop_tso as st', function($join) use($request) {
                //     $join->on('st.tso_id', '=', 'tso.id');
                //     if ($request->shop_id) {
                //         $join->where('st.shop_id', $request->shop_id);
                //     }
                // });
                ->join('shop_tso as st', function($join) use ($shop_id) {
                    $join->on('st.tso_id', '=', 'tso.id');
                    if ($shop_id) {
                        $join->where('st.shop_id', $shop_id);
                    }
                })
                ->join('shops as s', 's.id', '=', 'st.shop_id')
                ->when($route_id, function ($q) use ($route_id) {
                    $q->where('s.route_id', $route_id);
                });
                if (!empty($tso_id)) {
                    $tso_target->where('tso_targets.tso_id', $tso_id);
                }
                if (!empty($target_type)) {
                    $tso_target->where('tso_targets.type', $target_type);
                }
                $tso_target = $tso_target
                    ->whereBetween(DB::raw('MONTH(tso_targets.month)'), [$monthfrom, $monthto])
                    ->select(
                        'distributors.distributor_name',
                        'tso_targets.*',
                        DB::raw('SUM(tso_targets.qty) as tso_targets_qty'),
                        'products.product_name',
                        'tso.name as tso_name',
                        'tso_targets.product_id',
                        'tso_targets.tso_id',
                        'tso_targets.qty',
                        'product_prices.trade_price as trade_price',
                        's.id as shop_id',
                        's.company_name as shop_name',
                        's.route_id'
                    )
                    ->groupBy(
                        'tso_targets.product_id',
                        'tso_targets.shop_type',
                        'tso_targets.tso_id',
                        'distributors.distributor_name',
                        'products.product_name',
                        'tso.name',
                        's.id',
                        's.company_name',
                        's.route_id'
                    )
                    ->orderBy('products.orderby', 'ASC')
                    ->get();
                // dd($tso_target->toArray());
            return view($this->page . 'orderBookerTargetReport.order_booker_target_report_ajax', compact('tso_target','monthfrom', 'monthto', 'from', 'to' ,'target_type', 'distributor_id', 'tso_id'));
        endif;
        return view($this->page . 'orderBookerTargetReport.order_booker_target_report');
    }
    public function order_booker_target_report(Request $request)
    {
        // dd($request->all());
        $tso_id         = (array) $request->input('tso_id', []);
        $distributor_id = (array) $request->input('distributor_id', []);
        $shop_id        = $request->shop_id;
        $route_id       = $request->route_id;
        if ($request->ajax()) :
            $from = date('Y-m-d', strtotime($request->from));
            $to   = date('Y-m-d', strtotime($request->to));


            $monthfrom = date('m', strtotime($request->from));
            $monthto = date('m', strtotime($request->to));

            $summary = $request->summary;
            $target_type = $request->target_type;
            $tso_target = TSOTarget::leftjoin('products', 'products.id', 'tso_targets.product_id')
                ->join('tso', 'tso.id', 'tso_targets.tso_id')
                ->join('users_distributors','tso.user_id','=','users_distributors.user_id')
                ->when(empty($distributor_id), function ($query) {
                    $query->whereIn('users_distributors.distributor_id', MasterFormsHelper::get_users_distributors(Auth::user()->id));
                })
                ->join('distributors', 'distributors.id', 'tso.distributor_id')
                ->leftJoin('product_prices', function ($join) {
                    $join->on('product_prices.product_id', '=', 'products.id')
                         ->where('product_prices.status', 1); // Only fetch active prices
                });
            if (!empty($tso_id)) {
                $tso_target->whereIn('tso_targets.tso_id', (array) $tso_id);
            }
            if (!empty($target_type)) {
                $tso_target->where('tso_targets.type', $target_type);
            }

            if (!empty($distributor_id)) {
                $tso_target->whereIn('tso.distributor_id', (array) $distributor_id);
            }

            $tso_target = $tso_target
                // ->whereBetween(DB::raw('MONTH(tso_targets.month)'), [$monthfrom, $monthto])
                ->whereBetween(DB::raw('DATE(tso_targets.month)'), [$from, $to])
                ->select(
                    'products.product_name',
                    'tso_targets.product_id',
                    DB::raw('SUM(tso_targets.qty) as tso_targets_qty'),
                    DB::raw('SUM((product_prices.trade_price) * (tso_targets.qty)) as target_value'),
                    DB::raw('GROUP_CONCAT(DISTINCT distributors.id) as distributor_ids'),
                    DB::raw('GROUP_CONCAT(DISTINCT tso.id) as tso_ids')
                )
                ->groupBy('tso_targets.product_id', 'products.product_name')
                ->orderBy('products.orderby', 'ASC')
                ->get();
                // ->select(
                //     'distributors.distributor_name',
                //     'tso_targets.*',
                //     DB::raw('SUM(tso_targets.qty) as tso_targets_qty'),
                //     'products.product_name',
                //     'tso.name as tso_name',
                //     'tso_targets.product_id',
                //     'tso_targets.tso_id',
                //     'tso_targets.qty',
                //     'product_prices.trade_price as trade_price'

                // )
                // ->orderBy('products.orderby', 'ASC')
                // ->groupBy(
                //     'tso_targets.product_id',
                //     'tso_targets.shop_type',
                //     'tso_targets.tso_id',
                //     'distributors.distributor_name',
                //     'products.product_name',
                //     'tso.name'
                // )
                // ->get();
                // dd($tso_target);
            return view($this->page . 'orderBookerTargetReport.order_booker_target_report_ajax', compact('tso_target','monthfrom', 'monthto', 'from', 'to' ,'target_type', 'distributor_id', 'tso_id'));
        endif;
        return view($this->page . 'orderBookerTargetReport.order_booker_target_report');
    }



    public function tso_sales_return_report(Request $request)
    {
        $tso_id = $request->tso_id;
        $distributor_id = $request->distributor_id;
        $city = $request->city;
        if ($request->ajax()) :
 
            $from = $request->from;
            $to = $request->to;
            $data =   DB::table('sale_order_returns as a')
                ->join('sale_order_return_details as d', 'd.sale_order_return_id', 'a.id')
                ->join('shops', 'shops.id', 'a.shop_id')
                ->join('products', 'd.product_id', 'products.id')
                ->join('routes', function ($join) use ($request) {
                    $join->on('routes.id', '=', 'shops.route_id');
                    if ($request->route_id != null)
                        $join->where('routes.id', $request->route_id);
                })
                ->join('distributors as b', 'a.distributor_id', 'b.id')
                ->join('tso as c', function ($join) use ($request) {
                    $join->on('c.id', '=', 'a.tso_id')->where('c.active', 1);
                    // if ($request->city != null)
                    //     $join->where('c.city', $request->city);
                })
                ->leftJoin('users', 'users.id', '=', 'c.manager')
                ->leftJoin('users_distributors', 'c.user_id', '=', 'users_distributors.user_id')
                ->when($request->distributor_id == null, function ($query) use ($request) {
                    $query->whereIn('users_distributors.distributor_id', MasterFormsHelper::get_users_distributors(Auth::user()->id));
                })
                ->join('cities', 'cities.id', 'c.city')
                ->when($request->distributor_id != null, function ($query) use ($request) {
                    $query->where('a.distributor_id', $request->distributor_id);
                })
                ->when($request->tso_id != null, function ($query) use ($request) {
                    $query->where('a.tso_id', $request->tso_id);
                })
                ->when($request->shop_id != null, function ($query) use ($request) {
                    $query->where('a.shop_id', $request->shop_id);
                })
                ->when($request->product_id != null, function ($query) use ($request) {
                    $query->where('d.product_id', $request->product_id);
                })
                ->whereBetween('a.return_date', [$from, $to])
                ->where('a.status', 1)
		        ->where('a.excecution', 1)
                ->where('c.status', 1)
                // 🟢 Exclude all sale_order_return_details with reason = 'Fresh'
                ->where('d.reason', '!=', 'Fresh')
                ->select(
                    'a.id',
                    'b.distributor_name',
                    'c.name as tso',
                    'c.id as tso_id',
                    'c.user_id',
                    'a.return_date',
                    'shops.company_name as shop_name',
                    'users.name as user_name',
                    'routes.route_name',
                    'cities.name as city',
                    'products.product_name',
                    'd.quantity',
                    'a.excecution',
                    'a.return_no'
                )
                ->orderBy('a.return_no', 'DESC')
                ->orderBy('products.orderby', 'ASC')
                ->get();
 
                // dd($data);
            return view($this->page . 'tsoSalesReturnReport.tso_sales_return_ajax', compact('data', 'from', 'to', 'distributor_id', 'tso_id', 'city'));
        endif;
        return view($this->page . 'tsoSalesReturnReport.tso_sales_return');
    }
 
      
  public function shop_wise_sales_report(Request $request) // Aliiiiiiiiii
    {
        $tso_id = $request->tso_id;
        $distributor_id = $request->distributor_id;
        $city = $request->city;
        $from = $request->from;
        $to = $request->to;
        if ($request->ajax()) :
            $data = DB::table('sale_orders as a')
                ->leftJoin('sale_order_data as d', 'd.so_id', 'a.id')
                ->join('shops', 'shops.id', 'a.shop_id')
                ->join('products', 'd.product_id', 'products.id')
                ->join('routes', function ($join) use ($request) {
                    $join->on('routes.id', '=', 'shops.route_id');
                    if ($request->route_id != null)
                        $join->where('routes.id', $request->route_id);
                })
                ->join('distributors as b', 'a.distributor_id', 'b.id')
                ->join('tso as c', function ($join) use ($request) {
                    $join->on('c.id', '=', 'a.tso_id')->where('c.active', 1);
                    if ($request->city != null)
                        $join->where('c.city', $request->city);
                })
                ->leftJoin('users', 'users.id', '=', 'c.manager')
                ->leftJoin('users_distributors', 'c.user_id', '=', 'users_distributors.user_id')
                ->when($request->distributor_id == null, function ($query) use ($request) {
                    $query->whereIn('users_distributors.distributor_id', MasterFormsHelper::get_users_distributors(Auth::user()->id));
                })
                ->join('cities', 'cities.id', 'c.city')
                ->when($request->distributor_id != null, function ($query) use ($request) {
                    $query->where('a.distributor_id', $request->distributor_id);
                })
                ->when($request->tso_id != null, function ($query) use ($request) {
                    $query->where('a.tso_id', $request->tso_id);
                })
                ->when($request->shop_id != null, function ($query) use ($request) {
                    $query->where('a.shop_id', $request->shop_id);
                })
                ->when($request->product_id != null, function ($query) use ($request) {
                    $query->where('d.product_id', $request->product_id);
                })
                ->when($request->execution != null, function ($query) use ($request) {
                    $query->where('a.excecution', $request->execution);
                })
                ->whereBetween('a.dc_date', [$from, $to])
                ->where('a.status', 1)
                ->where('a.excecution', 1)
                ->where('c.status', 1)
                ->select(
                    'a.id',
                    'a.distributor_id',
                    'a.tso_id',
                    'shops.id as shop_id',
                    'shops.shop_code',
                    'shops.company_name as shop_name',
                    'routes.route_name',
                    'b.distributor_name',
                    'c.name as tso',
                    'users.name as manager',
                    'cities.name as city',

                    DB::raw('SUM(a.total_pcs) as qty'),
                    DB::raw('SUM(a.total_amount) as rate'),
                    DB::raw('SUM(a.total_amount) as net_sales') // adjust if you calculate differently
                )
                ->groupBy(
                    'shops.id',
                    'shops.shop_code',
                    'shops.company_name',
                    'routes.route_name',
                    'b.distributor_name',
                    'c.name',
                    'users.name',
                    'cities.name'
                )
                ->orderBy('shops.shop_code')
                ->orderBy('products.orderby', 'ASC')
                ->get();

                // $data = $data->map(function ($row) use ($from, $to) {
                //     $row->returned_qty = MasterFormsHelper::get_returned_qty_by_sale_order_id($row->id, $from, $to);
                //     return $row;
                // });

                // dd($data);
            return view($this->page . 'shopWiseSalesReport.shop_wise_sales_ajax', compact('data', 'from', 'to', 'distributor_id', 'tso_id', 'city'));
        endif;
        return view($this->page . 'shopWiseSalesReport.shop_wise_sales');
    }


    // public function order_booker_daily_activity_report(Request $request)
    // {
    //     $from           = $request->from;
    //     $to             = $request->to;
    //     $distributor_id = $request->distributor_id;
    //     $tso_id         = $request->tso_id;
    //     $city           = $request->city;

    //     if ($request->ajax()) {
    //         // Aggregate Orders Subquery
    //         $ordersAgg = DB::raw("
    //             (
    //                 SELECT
    //                     e.shop_id,
    //                     DATE(e.created_at) AS order_date,
    //                     COUNT(DISTINCT e.id) AS productives,
    //                     SUM(sod.qty) AS executed_qty,
    //                     SUM(sod.rate * sod.qty) AS executed_sales,
    //                     COALESCE(sr.total_return_qty, 0) AS shop_with_return
    //                 FROM sale_orders e
    //                 JOIN sale_order_data sod ON sod.so_id = e.id
    //                 LEFT JOIN (
    //                     SELECT shop_id, SUM(quantity) AS total_return_qty
    //                     FROM sale_order_return_details
    //                     WHERE status = 1 AND excecution = 1
    //                     GROUP BY shop_id
    //                 ) sr ON sr.shop_id = e.shop_id
    //                 WHERE e.status = 1 AND e.excecution = 1
    //                 GROUP BY e.shop_id, DATE(e.created_at), sr.total_return_qty
    //             ) ord
    //         ");

    //         $data = DB::table('shops as a')
    //             ->leftJoin('shop_visits as sv', function ($join) use ($from, $to, $request) {
    //                 $join->on('a.id', '=', 'sv.shop_id')
    //                     ->where('sv.type', 0);

    //                 if ($from && $to) {
    //                     $join->whereBetween('sv.visit_date', [$from, $to]);
    //                 }

    //                 if ($request->visit_date) {
    //                     $join->where('sv.visit_date', $request->visit_date);
    //                 }
    //             })
    //             ->join('routes', 'routes.id', '=', 'a.route_id')
    //             ->join('distributors as b', 'b.id', '=', 'a.distributor_id')
    //             ->join('shop_tso as st', function ($join) use ($request) {
    //                 $join->on('st.shop_id', '=', 'a.id');

    //                 if ($request->tso_id) {
    //                     $join->where('st.tso_id', $request->tso_id); // Filter inside join to avoid duplicates
    //                 }
    //             })
    //             ->join('tso as c', 'c.id', '=', 'st.tso_id')
    //             ->join('users as d', 'd.id', '=', 'c.manager')

    //             // Join aggregated orders on (shop, date)
    //             ->leftJoin($ordersAgg, function ($join) use ($request) {
    //                 $join->on('a.id', '=', 'ord.shop_id')
    //                     ->on('sv.visit_date', '=', 'ord.order_date');

    //                 if ($request->tso_id) {
    //                     $join->where('a.tso_id', $request->tso_id);
    //                 }

    //                 if ($request->distributor_id) {
    //                     $join->where('a.distributor_id', $request->distributor_id);
    //                 }
    //             })

    //             ->when($request->distributor_id, fn($q) => $q->where('a.distributor_id', $request->distributor_id))
    //             ->when($request->shop_id, fn($q) => $q->where('a.id', $request->shop_id))
    //             ->where('a.status', 1)
    //             ->select(
    //                 'a.id',
    //                 'b.distributor_name',
    //                 'c.name as tso',
    //                 'c.id as tso_id',
    //                 'c.user_id',
    //                 'd.name as manager_name',
    //                 'sv.visit_date',
    //                 'a.shop_code as shop_code',
    //                 'a.company_name as shop_name',
    //                 'routes.route_name',
    //                 DB::raw('COUNT(DISTINCT sv.id) as total_visit'),
    //                 DB::raw('COALESCE(MAX(ord.productives), 0) as productive_visit'),
    //                 DB::raw('COALESCE(MAX(ord.executed_qty), 0) as executed_qty'),
    //                 DB::raw('COALESCE(MAX(ord.executed_sales), 0) as executed_sales'),
    //                 DB::raw('COALESCE(MAX(ord.shop_with_return), 0) as shop_with_return')
    //             )
    //             ->groupBy(
    //                 // 'a.id',
    //                 // 'b.distributor_name',
    //                 // 'c.name',
    //                 // 'c.id',
    //                 // 'c.user_id',
    //                 // 'd.name',
    //                 // 'sv.visit_date',
    //                 'a.shop_code',
    //                 // 'a.company_name',
    //                 // 'routes.route_name'
    //             )
    //             ->orderBy('a.id', 'ASC')
    //             ->get();

    //         return view(
    //             $this->page . 'orderBookerDailyActivity.order_booker_daily_activity_list_ajax',
    //             compact('data', 'from', 'to', 'distributor_id', 'tso_id')
    //         );
    //     }

    //     return view($this->page . 'orderBookerDailyActivity.order_booker_daily_activity_list');
    // }
    public function order_booker_daily_activity_report_old(Request $request)//----------------------------------------------
    {
        $from           = $request->from;
        $to             = $request->to;
        $distributor_id = $request->distributor_id;
        $tso_id         = $request->tso_id;
        $shop_id        = $request->shop_id;
        $city           = $request->city;

        if ($request->ajax()) {
            $ordersAgg = DB::raw("
                (
                    SELECT
                        e.shop_id,
                        DATE(e.dc_date) AS order_date,   -- use dc_date for consistency
                        COUNT(DISTINCT e.id) AS productives,
                        SUM(sod.qty) AS executed_qty,
                        SUM(sod.rate * sod.qty) AS executed_sales,
                        COALESCE(SUM(sr.quantity), 0) AS shop_with_return
                    FROM sale_orders e
                    JOIN sale_order_data sod ON sod.so_id = e.id
                    LEFT JOIN sale_order_return_details sr 
                        ON sr.shop_id = e.shop_id
                        AND sr.status = 1
                    WHERE e.status = 1
                    AND e.excecution = 1
                    AND e.dc_date BETWEEN '$from' AND '$to'
                    GROUP BY e.shop_id, DATE(e.dc_date)
                ) ord
            ");
            $tsoIds = null;
            if ($distributor_id && !$tso_id) {
                $tsoIds = DB::table('tso')
                    ->where('distributor_id', $distributor_id)
                    ->pluck('id')
                    ->toArray();
            }

            $data = DB::table('shops as a')
                ->join('routes', 'routes.id', '=', 'a.route_id')
                ->join('distributors as b', 'a.distributor_id', '=', 'b.id')
                ->join('shop_tso as st', function ($join) use ($request) {
                    $join->on('st.shop_id', '=', 'a.id');
                    if ($request->tso_id) {
                        $join->where('st.tso_id', $request->tso_id);
                    }
                })
                ->join('tso as c', 'c.id', '=', 'st.tso_id')
                ->join('users as d', 'd.id', '=', 'c.manager')
                ->leftJoin('shop_visits as sv', function ($join) use ($from, $to, $request) {
                    $join->on('a.id', '=', 'sv.shop_id')
                        ->whereColumn('sv.user_id', 'c.user_id')
                        ->where('sv.type', 0);
                    if ($from && $to) {
                        $join->whereBetween('sv.visit_date', [$from, $to]);
                    }
                })
                ->leftJoin($ordersAgg, function ($join) use ($request) {
                    $join->on('a.id', '=', 'ord.shop_id')
                        ->on('sv.visit_date', '=', 'ord.order_date');
                    // if ($request->tso_id) {
                    //     $join->where('st.tso_id', $request->tso_id);
                    // }
                    // if ($request->distributor_id) {
                    //     $join->where('a.distributor_id', $request->distributor_id);
                    // }
                })
                ->when($request->distributor_id, fn($q) => $q->where('a.distributor_id', $request->distributor_id))
                ->when($tso_id, fn($q) => $q->where('st.tso_id', $tso_id))
                ->when($tsoIds, fn($q) => $q->whereIn('st.tso_id', $tsoIds))
                ->when($request->route_id, fn($q) => $q->where('a.route_id', $request->route_id))
                ->when($request->shop_id, fn($q) => $q->where('a.id', $request->shop_id))
                ->where('a.status', 1)
                ->select(
                    'a.id',
                    'b.distributor_name',
                    'c.name as tso',
                    'c.id as tso_id',
                    'c.user_id',
                    'd.name as manager_name',
                    'sv.visit_date',
                    'a.shop_code',
                    'a.id as shop_id',
                    'a.company_name as shop_name',
                    'routes.route_name',
                    'routes.id as route_id',
                    DB::raw('COUNT(DISTINCT sv.id) as visits_count'),
                    DB::raw('COALESCE(MAX(ord.productives), 0) as productive_visit'),
                    DB::raw('COALESCE(MAX(ord.executed_qty), 0) as executed_qty'),
                    DB::raw('COALESCE(MAX(ord.executed_sales), 0) as executed_sales'),
                    DB::raw('COALESCE(MAX(ord.shop_with_return), 0) as shop_with_return'),
                    DB::raw('(
                        SELECT COUNT(DISTINCT st2.shop_id)
                        FROM shop_tso st2
                        JOIN shops s2 ON s2.id = st2.shop_id
                        WHERE s2.distributor_id = a.distributor_id
                        AND s2.status = 1
                        ' . ($request->tso_id ? 'AND st2.tso_id = '.$request->tso_id : '') . '
                        ' . ($request->route_id ? 'AND s2.route_id = '.$request->route_id : '') . '
                        ' . ($request->shop_id ? 'AND s2.id = '.$request->shop_id : '') . '
                    ) as total_shop')
                )
                ->groupBy(
                    // 'a.id',
                    // 'b.distributor_name',
                    'c.name',
                    'c.id',
                    // 'c.user_id',
                    // 'd.name',
                    // 'sv.visit_date',
                    // 'a.shop_code',
                    // 'a.company_name',
                    // 'routes.route_name'
                )
                ->orderBy('a.id', 'ASC')
                ->get();

                // dd($data);
            return view(
                $this->page . 'orderBookerDailyActivity.order_booker_daily_activity_list_ajax',
                compact('data', 'from', 'to', 'distributor_id', 'tso_id', 'shop_id', 'city')
            );
        }

        return view($this->page . 'orderBookerDailyActivity.order_booker_daily_activity_list');
    }
    public function order_booker_daily_activity_report(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $distributor_id = $request->distributor_id;
        $tso_id = $request->tso_id;
        $shop_id = $request->shop_id;
        $city = $request->city;  // Unused in query—remove if not needed

        if ($request->ajax()) {
            // Precompute total_shop (runs once)
            $totalShopQuery = DB::table('shop_tso as st2')
                ->join('shops as s2', 's2.id', '=', 'st2.shop_id')
                ->where('s2.status', 1);
            if ($distributor_id) {
                $totalShopQuery->where('s2.distributor_id', $distributor_id);
            }
            if ($tso_id) {
                $totalShopQuery->where('st2.tso_id', $tso_id);
            }
            if ($request->route_id) {
                $totalShopQuery->where('s2.route_id', $request->route_id);
            }
            if ($shop_id) {
                $totalShopQuery->where('s2.id', $shop_id);
            }
            $total_shop = $totalShopQuery->count(DB::raw('DISTINCT st2.shop_id'));

            // Build ord as a subquery (handles bindings properly)
            $ordersSubquery = DB::table('sale_orders as e')
                ->select(
                    'e.shop_id',
                    DB::raw('DATE(e.dc_date) AS order_date'),
                    DB::raw('COUNT(DISTINCT e.id) AS productives'),
                    DB::raw('SUM(sod.qty) AS executed_qty'),
                    DB::raw('SUM(sod.rate * sod.qty) AS executed_sales'),
                    DB::raw('COALESCE(SUM(sr.quantity), 0) AS shop_with_return')
                )
                ->join('sale_order_data as sod', 'sod.so_id', '=', 'e.id')
                ->leftJoin('sale_order_return_details as sr', function ($join) {
                    $join->on('sr.shop_id', '=', 'e.shop_id')
                        ->where('sr.status', 1);
                })
                ->where('e.status', 1)
                ->where('e.excecution', 1)  // Note: Typo? Should this be 'execution'?
                ->whereBetween('e.dc_date', [$from, $to])
                ->groupBy('e.shop_id', DB::raw('DATE(e.dc_date)'));

            $tsoIds = null;
            if ($distributor_id && !$tso_id) {
                $tsoIds = DB::table('tso')
                    ->where('distributor_id', $distributor_id)
                    ->pluck('id')
                    ->toArray();
            }

            $query = DB::table('shops as a')
                ->join('routes', 'routes.id', '=', 'a.route_id')
                ->join('distributors as b', 'a.distributor_id', '=', 'b.id')
                ->join('shop_tso as st', function ($join) use ($tso_id) {
                    $join->on('st.shop_id', '=', 'a.id');
                    if ($tso_id) {
                        $join->where('st.tso_id', $tso_id);
                    }
                })
                ->join('tso as c', 'c.id', '=', 'st.tso_id')
                ->join('users as d', 'd.id', '=', 'c.manager')
                ->leftJoin('shop_visits as sv', function ($join) use ($from, $to) {
                    $join->on('a.id', '=', 'sv.shop_id')
                        ->whereColumn('sv.user_id', 'c.user_id')
                        ->where('sv.type', 0);
                    if ($from && $to) {
                        $join->whereBetween('sv.visit_date', [$from, $to]);
                    }
                })
                ->leftJoinSub($ordersSubquery, 'ord', function ($join) {
                    $join->on('a.id', '=', 'ord.shop_id')
                        ->on('sv.visit_date', '=', 'ord.order_date');
                })  // No extra bindings array needed here
                ->when($distributor_id, fn($q) => $q->where('a.distributor_id', $distributor_id))
                ->when($tso_id, fn($q) => $q->where('st.tso_id', $tso_id))
                ->when($tsoIds, fn($q) => $q->whereIn('st.tso_id', $tsoIds))
                ->when($request->route_id, fn($q) => $q->where('a.route_id', $request->route_id))
                ->when($shop_id, fn($q) => $q->where('a.id', $shop_id))
                ->where('a.status', 1)
                ->select(
                    'a.id',
                    'b.distributor_name',
                    'c.name as tso',
                    'c.id as tso_id',
                    'c.user_id',
                    'd.name as manager_name',
                    'sv.visit_date',
                    'a.shop_code',
                    'a.id as shop_id',
                    'a.company_name as shop_name',
                    'routes.route_name',
                    'routes.id as route_id',
                    DB::raw('COUNT(DISTINCT sv.id) as visits_count'),
                    DB::raw('COALESCE(MAX(ord.productives), 0) as productive_visit'),
                    DB::raw('COALESCE(MAX(ord.executed_qty), 0) as executed_qty'),
                    DB::raw('COALESCE(MAX(ord.executed_sales), 0) as executed_sales'),
                    DB::raw('COALESCE(MAX(ord.shop_with_return), 0) as shop_with_return'),
                    DB::raw("{$total_shop} as total_shop")
                )
                ->groupBy('c.name', 'c.id')
                ->orderBy('a.id', 'ASC');

            $data = $query->get();  // Add ->paginate(100) if results are too large

            return view(
                $this->page . 'orderBookerDailyActivity.order_booker_daily_activity_list_ajax',
                compact('data', 'from', 'to', 'distributor_id', 'tso_id', 'shop_id', 'city')
            );
        }

        return view($this->page . 'orderBookerDailyActivity.order_booker_daily_activity_list');
    }


    // public function order_booker_daily_activity_location_report_old(Request $request)
    // {
    //     $from          = $request->from;
    //     $to            = $request->to;
    //     $distributor_id= $request->distributor_id;
    //     $tso_id        = $request->tso_id;
    //     $city          = $request->city;
    //     $shop_id       = $request->shop_id;
    //     $route_id      = $request->route_id;
 
    //     if ($request->ajax()) :
    //         $ordersAgg = DB::raw("
    //             (
    //             SELECT
    //                 e.shop_id,
    //                 DATE(e.created_at) AS order_date,
    //                 COUNT(DISTINCT e.id) AS productives,
    //                 SUM(sod.qty) AS executed_qty,
    //                 SUM(sod.rate * sod.qty) AS executed_sales,
    //                 COALESCE(sr.total_return_qty, 0) AS shop_with_return
    //             FROM sale_orders e
    //             JOIN sale_order_data sod ON sod.so_id = e.id
    //             LEFT JOIN (
    //                 SELECT shop_id, SUM(quantity) AS total_return_qty
    //                 FROM sale_order_return_details
    //                 WHERE status = 1 AND excecution = 1
    //                 GROUP BY shop_id
    //             ) sr ON sr.shop_id = e.shop_id
    //             WHERE e.status = 1 AND e.excecution = 1
    //             GROUP BY e.shop_id, DATE(e.created_at), sr.total_return_qty
    //             ) ord
    //         ");
    //         $tsoIds = null;
    //         if ($distributor_id && !$tso_id) {
    //             $tsoIds = DB::table('tso')
    //                 ->where('distributor_id', $distributor_id)
    //                 ->pluck('id')
    //                 ->toArray();
    //         }

    //         $data = DB::table('shops as a')
    //             ->leftJoin('shop_visits as sv', function ($join) use ($from, $to, $request) {
    //                 $join->on('a.id', '=', 'sv.shop_id');
    //                 $join->where('sv.type', 0);
    //                 if ($from && $to) {
    //                     $join->whereBetween('sv.visit_date', [$from, $to]);
    //                 }
    //                 // if ($request->tso_id) $join->where('a.tso_id', $request->tso_id);
    //                 if ($request->visit_date) {
    //                     $join->where('sv.visit_date', $request->visit_date);
    //                 }
    //             })
    //             ->join('routes', 'routes.id', '=', 'a.route_id')
    //             ->join('distributors as b', 'a.distributor_id', '=', 'b.id')
    //             ->join('shop_tso as st', function ($join) use ($tso_id) {
    //                 $join->on('st.shop_id', '=', 'a.id');
    //                 if ($tso_id) {
    //                     $join->where('st.tso_id', $tso_id);
    //                 }
    //             })
    //             ->join('tso as c', 'c.id', '=', 'st.tso_id')
    //             ->join('users as d', 'd.id', '=', 'c.manager')
    //             ->leftJoin($ordersAgg, function ($join) use ($distributor_id) {
    //                 $join->on('a.id', '=', 'ord.shop_id')
    //                     ->on('sv.visit_date', '=', 'ord.order_date');
    //                 // if ($distributor_id) {
    //                 //     $join->where('a.distributor_id', $distributor_id);
    //                 // }
    //             })
    //             // ->join('tso as c', 'c.id', '=', 'a.tso_id')
    //             // ->join('users as d', 'd.id', '=', 'c.manager')
 
    //             // // 👇 join the aggregated orders on (shop, date)
    //             // ->leftJoin($ordersAgg, function ($join) use ($request) {
    //             //     $join->on('a.id', '=', 'ord.shop_id')
    //             //         ->on('sv.visit_date', '=', 'ord.order_date');
    //             //     // if ($request->tso_id) $join->where('a.tso_id', $request->tso_id);
    //             //     if ($request->distributor_id) $join->where('a.distributor_id', $request->distributor_id);
    //             // })

    //             ->when($request->distributor_id, fn($q) => $q->where('a.distributor_id', $request->distributor_id))
    //             ->when($request->shop_id, fn($q) => $q->where('a.id', $request->shop_id))
    //             ->when($tsoIds, fn($q) => $q->whereIn('st.tso_id', $tsoIds))
    //             ->when($route_id, fn($q) => $q->where('a.route_id', $route_id))

    //             ->where('a.status', 1)
    //             ->select(
    //                 'a.id',
    //                 'a.distributor_id',
    //                 'b.distributor_name',
    //                 'c.name as tso',
    //                 'c.id as tso_id',
    //                 'c.user_id',
    //                 'd.name as manager_name',
    //                 'sv.visit_date',
    //                 'sv.created_at as visit_created_at',
    //                 'a.shop_code as shop_code',
    //                 'a.company_name as shop_name',
    //                 'a.remarks as shop_remarks',
    //                 'a.map as shop_map_name',
    //                 'a.latitude as latitude',
    //                 'a.longitude as longitude',
    //                 'routes.route_name',
    //                 DB::raw('DATE_FORMAT(a.created_at, "%Y-%m-%d") as shop_date'), // Date part
    //                 DB::raw('DATE_FORMAT(a.created_at, "%H:%i:%s") as shop_time'),
    //                 DB::raw('COUNT(DISTINCT sv.id) as total_visit'),
    //                 // ord.* is already per (shop, date). Use MAX (not SUM) to avoid multiplying by visit rows.
    //                 DB::raw('COALESCE(MAX(ord.productives), 0) as productive_visit'),
    //                 DB::raw('COALESCE(MAX(ord.executed_qty), 0) as executed_qty'),
    //                 DB::raw('COALESCE(MAX(ord.executed_sales), 0) as executed_sales'),
    //                 DB::raw('COALESCE(MAX(ord.shop_with_return), 0) as shop_with_return')
    //             )
    //             ->groupBy(
    //                 'a.id',
    //                 'b.distributor_name',
    //                 'c.name',
    //                 'c.id',
    //                 // 'c.user_id',
    //                 // 'd.name',
    //                 // 'sv.visit_date',
    //                 // 'a.shop_code',
    //                 // 'a.company_name',
    //                 // 'routes.route_name'
    //             )
    //             ->orderBy('a.id', 'ASC')
    //             ->get();
    //             // dd($data);
    //         return view($this->page . 'orderBookerDailyActivityLocation.order_booker_daily_activity_location_list_ajax', compact('data', 'from', 'to', 'distributor_id', 'tso_id'));
    //     endif;
 
    //     return view($this->page . 'orderBookerDailyActivityLocation.order_booker_daily_activity_location_list');
    // }
    public function order_booker_daily_activity_location_report_old(Request $request)
    {
        $from          = $request->from;
        $to            = $request->to;
        $distributor_id= $request->distributor_id;
        $tso_id        = $request->tso_id;
        $city          = $request->city;
        $shop_id       = $request->shop_id;
        $route_id      = $request->route_id;

        if ($request->ajax()) {

            // aggregated orders subquery
            $ordersAgg = DB::raw("
                (
                    SELECT
                        e.shop_id,
                        DATE(e.created_at) AS order_date,
                        COUNT(DISTINCT e.id) AS productives,
                        SUM(sod.qty) AS executed_qty,
                        SUM(sod.rate * sod.qty) AS executed_sales,
                        COALESCE(sr.total_return_qty, 0) AS shop_with_return
                    FROM sale_orders e
                    JOIN sale_order_data sod ON sod.so_id = e.id
                    LEFT JOIN (
                        SELECT shop_id, SUM(quantity) AS total_return_qty
                        FROM sale_order_return_details
                        WHERE status = 1 AND excecution = 1
                        GROUP BY shop_id
                    ) sr ON sr.shop_id = e.shop_id
                    WHERE e.status = 1 AND e.excecution = 1
                    GROUP BY e.shop_id, DATE(e.created_at), sr.total_return_qty
                ) ord
            ");

            // tso ids by distributor
            $tsoIds = null;
            if ($distributor_id && !$tso_id) {
                $tsoIds = DB::table('tso')
                    ->where('distributor_id', $distributor_id)
                    ->pluck('id')
                    ->toArray();
            }

            // main query
            $data = DB::table('shops as a')
                ->leftJoin('shop_visits as sv', function ($join) use ($from, $to, $request) {
                    $join->on('a.id', '=', 'sv.shop_id')
                        ->where('sv.type', 0);

                    if ($from && $to) {
                        $join->whereBetween('sv.visit_date', [$from, $to]);
                    }

                    if ($request->visit_date) {
                        $join->where('sv.visit_date', $request->visit_date);
                    }
                })
                ->join('routes', 'routes.id', '=', 'a.route_id')
                ->join('distributors as b', 'a.distributor_id', '=', 'b.id')
                ->join('shop_tso as st', function ($join) use ($tso_id) {
                    $join->on('st.shop_id', '=', 'a.id');
                    if ($tso_id) {
                        $join->where('st.tso_id', $tso_id);
                    }
                })
                ->join('tso as c', 'c.id', '=', 'st.tso_id')
                ->join('users as d', 'd.id', '=', 'c.manager')
                ->leftJoin($ordersAgg, function ($join) {
                    $join->on('a.id', '=', 'ord.shop_id')
                        ->on('sv.visit_date', '=', 'ord.order_date');
                })
                ->when($distributor_id, fn($q) => $q->where('a.distributor_id', $distributor_id))
                ->when($shop_id, fn($q) => $q->where('a.id', $shop_id))
                ->when($tsoIds, fn($q) => $q->whereIn('st.tso_id', $tsoIds))
                ->when($route_id, fn($q) => $q->where('a.route_id', $route_id))
                ->where('a.status', 1)
                ->select(
                    'a.id',
                    'a.distributor_id',
                    'b.distributor_name',
                    'c.name as tso',
                    'c.id as tso_id',
                    'c.user_id',
                    'd.name as manager_name',
                    'sv.visit_date',
                    'sv.created_at as visit_created_at',
                    'a.shop_code',
                    'a.company_name as shop_name',
                    'a.remarks as shop_remarks',
                    'a.map as shop_map_name',
                    'a.latitude',
                    'a.longitude',
                    'routes.route_name',
                    DB::raw('DATE_FORMAT(a.created_at, "%Y-%m-%d") as shop_date'),
                    DB::raw('DATE_FORMAT(a.created_at, "%H:%i:%s") as shop_time'),
                    DB::raw('COUNT(DISTINCT sv.id) as total_visit'),
                    DB::raw('COALESCE(MAX(ord.productives), 0) as productive_visit'),
                    DB::raw('COALESCE(MAX(ord.executed_qty), 0) as executed_qty'),
                    DB::raw('COALESCE(MAX(ord.executed_sales), 0) as executed_sales'),
                    DB::raw('COALESCE(MAX(ord.shop_with_return), 0) as shop_with_return')
                )
                ->groupBy('a.id', 'b.distributor_name', 'c.name', 'c.id')
                ->orderBy('a.id', 'ASC')
                ->get();

            // preload sale orders + visits
            $prepared = $data->map(function ($row) use ($from, $to) {
                $saleOrders = DB::table('sale_orders')
                    ->where('tso_id', $row->tso_id)
                    ->where('distributor_id', $row->distributor_id)
                    ->where('shop_id', $row->id)
                    ->whereBetween('dc_date', [$from, $to]);

                $row->total_pcs   = $saleOrders->sum('total_pcs') ?? 0;
                $row->unit_record = $saleOrders->first();

                $row->shop_visits = DB::table('shop_visits')
                    ->where('user_id', $row->user_id)
                    ->where('shop_id', $row->id)
                    ->whereBetween('visit_date', [$from, $to])
                    ->get();

                return $row;
            });
            // dd($data, $prepared);

            return view(
                $this->page . 'orderBookerDailyActivityLocation.order_booker_daily_activity_location_list_ajax',
                compact('prepared', 'from', 'to', 'distributor_id', 'tso_id')
            );
        }

        return view($this->page . 'orderBookerDailyActivityLocation.order_booker_daily_activity_location_list');
    }
    public function order_booker_daily_activity_location_report(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $distributor_id = $request->distributor_id;
        $tso_id = $request->tso_id;
        $city = $request->city;
        $shop_id = $request->shop_id;
        $route_id = $request->route_id;
        if ($request->ajax()) {
            // aggregated orders subquery
            $ordersAgg = DB::raw("
                (
                    SELECT
                        e.shop_id,
                        DATE(e.created_at) AS order_date,
                        COUNT(DISTINCT e.id) AS productives,
                        SUM(sod.qty) AS executed_qty,
                        SUM(sod.rate * sod.qty) AS executed_sales,
                        COALESCE(sr.total_return_qty, 0) AS shop_with_return
                    FROM sale_orders e
                    JOIN sale_order_data sod ON sod.so_id = e.id
                    LEFT JOIN (
                        SELECT shop_id, SUM(quantity) AS total_return_qty
                        FROM sale_order_return_details
                        WHERE status = 1 AND excecution = 1
                        GROUP BY shop_id
                    ) sr ON sr.shop_id = e.shop_id
                    WHERE e.status = 1 AND e.excecution = 1
                    GROUP BY e.shop_id, DATE(e.created_at), sr.total_return_qty
                ) ord
            ");
            // tso ids by distributor
            $tsoIds = null;
            if ($distributor_id && !$tso_id) {
                $tsoIds = DB::table('tso')
                    ->where('distributor_id', $distributor_id)
                    ->pluck('id')
                    ->toArray();
            }
            // main query
            $data = DB::table('shops as a')
                ->leftJoin('shop_visits as sv', function ($join) use ($from, $to, $request) {
                    $join->on('a.id', '=', 'sv.shop_id')
                        ->where('sv.type', 0);
                    if ($from && $to) {
                        $join->whereBetween('sv.visit_date', [$from, $to]);
                    }
                    if ($request->visit_date) {
                        $join->where('sv.visit_date', $request->visit_date);
                    }
                })
                ->join('routes', 'routes.id', '=', 'a.route_id')
                ->join('distributors as b', 'a.distributor_id', '=', 'b.id')
                ->join('shop_tso as st', function ($join) use ($tso_id) {
                    $join->on('st.shop_id', '=', 'a.id');
                    if ($tso_id) {
                        $join->where('st.tso_id', $tso_id);
                    }
                })
                ->join('tso as c', 'c.id', '=', 'st.tso_id')
                ->join('users as d', 'd.id', '=', 'c.manager')
                ->leftJoin($ordersAgg, function ($join) {
                    $join->on('a.id', '=', 'ord.shop_id')
                        ->on('sv.visit_date', '=', 'ord.order_date');
                })
                ->when($distributor_id, fn($q) => $q->where('a.distributor_id', $distributor_id))
                ->when($shop_id, fn($q) => $q->where('a.id', $shop_id))
                ->when($tsoIds, fn($q) => $q->whereIn('st.tso_id', $tsoIds))
                ->when($route_id, fn($q) => $q->where('a.route_id', $route_id))
                ->where('a.status', 1)
                ->select(
                    'a.id',
                    'a.distributor_id',
                    'b.distributor_name',
                    'c.name as tso',
                    'c.id as tso_id',
                    'c.user_id',
                    'd.name as manager_name',
                    'sv.visit_date',
                    'sv.created_at as visit_created_at',
                    'sv.latitude as visit_latitude',
                    'sv.longitude as visit_longitude',
                    'a.shop_code',
                    'a.company_name as shop_name',
                    'a.remarks as shop_remarks',
                    'a.map as shop_map_name',
                    'a.latitude',
                    'a.longitude',
                    'routes.route_name',
                    DB::raw('DATE_FORMAT(a.created_at, "%Y-%m-%d") as shop_date'),
                    DB::raw('DATE_FORMAT(a.created_at, "%H:%i:%s") as shop_time'),
                    DB::raw('COUNT(DISTINCT sv.id) as total_visit'),
                    DB::raw('COALESCE(MAX(ord.productives), 0) as productive_visit'),
                    DB::raw('COALESCE(MAX(ord.executed_qty), 0) as executed_qty'),
                    DB::raw('COALESCE(MAX(ord.executed_sales), 0) as executed_sales'),
                    DB::raw('COALESCE(MAX(ord.shop_with_return), 0) as shop_with_return')
                )
                ->groupBy('a.id', 'b.distributor_name', 'c.name', 'c.id')
                ->orderBy('a.id', 'ASC')
                ->get();
            // preload sale orders
            // preload sale orders
            // $saleOrdersAll = DB::table('sale_orders')
            //     ->select('id', 'shop_id', 'tso_id', 'distributor_id', 'created_at', 'total_pcs')
            //     ->whereBetween('dc_date', [$from, $to])
            //     ->whereIn('shop_id', $data->pluck('id'))
            //     ->get()
            //     ->groupBy(function ($order) {
            //         return $order->shop_id . '|' . $order->tso_id . '|' . $order->distributor_id;
            //     });
            // // preload visits
            // $allShopVisits = DB::table('shop_visits')
            //     ->whereBetween('visit_date', [$from, $to])
            //     ->whereIn('shop_id', $data->pluck('id'))
            //     ->whereIn('user_id', $data->pluck('user_id'))
            //     ->get()
            //     ->groupBy(function ($visit) {
            //         return $visit->shop_id . '|' . $visit->user_id;
            //     });
            // // prepare data
            // $prepared = $data->map(function ($row) use ($saleOrdersAll, $allShopVisits) {
            //     $key = $row->id . '|' . $row->tso_id . '|' . $row->distributor_id;
            //     $saleOrders = $saleOrdersAll->get($key, collect());
            //     $row->total_pcs = $saleOrders->sum('total_pcs') ?? 0;
            //     $row->unit_record = $saleOrders->first();
            //     $visitKey = $row->id . '|' . $row->user_id;
            //     $row->shop_visits = $allShopVisits->get($visitKey, collect());
            //     return $row;
            // });
            $saleOrdersAll = SaleOrder::with(['usersLocation' => function ($q) {
                    $q->latest();
                }])
                ->whereBetween('dc_date', [$from, $to])
                ->whereIn('shop_id', $data->pluck('id'))
                ->get()
                ->each(function ($order) {
                    $matched = $order->usersLocation->firstWhere('created_at', $order->created_at)
                        ?? $order->usersLocation->firstWhere('created_at', $order->updated_at);

                    $order->setRelation('usersLocation', $matched);
                })
                ->groupBy(function ($order) {
                    return $order->shop_id . '|' . $order->tso_id . '|' . $order->distributor_id;
                });
            // preload visits
            $allShopVisits = DB::table('shop_visits')
                ->whereBetween('visit_date', [$from, $to])
                ->whereIn('shop_id', $data->pluck('id'))
                ->whereIn('user_id', $data->pluck('user_id'))
                ->get()
                ->groupBy(function ($visit) {
                    return $visit->shop_id . '|' . $visit->user_id;
                });
            // prepare data unit_record
            $prepared = $data->map(function ($row) use ($saleOrdersAll, $allShopVisits) {
                $key = $row->id . '|' . $row->tso_id . '|' . $row->distributor_id;
                $saleOrders = $saleOrdersAll->get($key, collect());
                $row->total_pcs = $saleOrders->sum('total_pcs') ?? 0;
                $row->unit_record = $saleOrders->first();
                $visitKey = $row->id . '|' . $row->user_id;
                $row->shop_visits = $allShopVisits->get($visitKey, collect());
                return $row;
            });
            // dd($data, $prepared);
            return view(
                $this->page . 'orderBookerDailyActivityLocation.order_booker_daily_activity_location_list_ajax',
                compact('prepared', 'from', 'to', 'distributor_id', 'tso_id')
            );
        }
        return view($this->page . 'orderBookerDailyActivityLocation.order_booker_daily_activity_location_list');
    }






    public function sales_return_report(Request $request)
    {
        $tso_id = $request->tso_id;
        $distributor_id = $request->distributor_id;
        $city = $request->city;
        if ($request->ajax()) :
 
            $from = $request->from;
            $to = $request->to;
            $data =   DB::table('sale_order_returns as a')
                ->join('sale_order_return_details as d', 'd.sale_order_return_id', 'a.id')
                ->join('shops', 'shops.id', 'a.shop_id')
                ->join('products', 'd.product_id', 'products.id')
                ->leftJoin('product_prices', function ($join) {
                    $join->on('product_prices.product_id', '=', 'products.id')
                         ->where('product_prices.status', 1);
                })
                ->join('routes', function ($join) use ($request) {
                    $join->on('routes.id', '=', 'shops.route_id');
                    if ($request->route_id != null)
                        $join->where('routes.id', $request->route_id);
                })
                ->join('distributors as b', 'a.distributor_id', 'b.id')
                ->join('tso as c', function ($join) use ($request) {
                    $join->on('c.id', '=', 'a.tso_id')->where('c.active', 1);
                    // if ($request->city != null)
                    //     $join->where('c.city', $request->city);
                })
                ->leftJoin('users', 'users.id', '=', 'c.manager')
                ->leftJoin('users_distributors', 'c.user_id', '=', 'users_distributors.user_id')
                ->when($request->distributor_id == null, function ($query) use ($request) {
                    $query->whereIn('users_distributors.distributor_id', MasterFormsHelper::get_users_distributors(Auth::user()->id));
                })
                ->join('cities', 'cities.id', 'c.city')
                ->when($request->distributor_id != null, function ($query) use ($request) {
                    $query->where('a.distributor_id', $request->distributor_id);
                })
                ->when($request->tso_id != null, function ($query) use ($request) {
                    $query->where('a.tso_id', $request->tso_id);
                })
                ->when($request->shop_id != null, function ($query) use ($request) {
                    $query->where('a.shop_id', $request->shop_id);
                })
                ->when($request->product_id != null, function ($query) use ($request) {
                    $query->where('d.product_id', $request->product_id);
                })
                ->whereBetween('a.return_date', [$from, $to])
                ->where('a.status', 1)
                ->where('a.excecution', 1)
                ->where('c.status', 1)
                // 🟢 Exclude all sale_order_return_details with reason = 'Fresh'
                ->where('d.reason', '!=', 'Fresh')
                ->select(
                    'a.id',
                    'products.product_name',
                    'product_prices.pcs_per_carton as packing',
                    'd.quantity',
                    'product_prices.trade_price as price',
                    DB::raw('d.quantity * product_prices.trade_price as total'),
                    'b.distributor_name',
                    'c.name as tso',
                    'c.id as tso_id',
                    'c.user_id',
                    'a.return_date',
                    'shops.company_name as shop_name',
                    'users.name as user_name',
                    'routes.route_name',
                    'cities.name as city',
                    'a.excecution',
                    'a.return_no'
                )
                ->orderBy('a.return_no', 'DESC')
                ->orderBy('products.orderby', 'ASC')
                ->get();
 
                // dd($data);
            return view($this->page . 'salesReturnReport.sales_return_report_ajax', compact('data', 'from', 'to', 'distributor_id', 'tso_id', 'city'));
        endif;
        return view($this->page . 'salesReturnReport.sales_return_report');
    }

    public function brand_distributer_report(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $shop_id = $request->shop_id;
        $route_id = $request->route_id;
        $product_id = $request->product_id;

        if ($request->ajax()) {
            $tsos = TSO::status()
                ->active()
                ->join('users_distributors', 'users_distributors.user_id', '=', 'tso.user_id')
                ->whereIn('users_distributors.distributor_id', $this->master->get_users_distributors(Auth::id()))
                ->when($request->distributor_id, fn($q) => $q->where('users_distributors.distributor_id', $request->distributor_id))
                ->when($request->tso_id, fn($q) => $q->where('tso.id', $request->tso_id))
                ->when($request->designation, fn($q) => $q->where('tso.designation_id', $request->designation))
                ->when($request->city, fn($q) => $q->where('tso.city', $request->city))
                ->with(['designation', 'distributor', 'cities'])
                ->select('tso.*', 'users_distributors.distributor_id')
                ->get();

            $reportData = collect();

            foreach ($tsos as $tso) {
                // ✅ Routes for this TSO
                $routes = DB::table('route_tso')
                    ->where('tso_id', $tso->id)
                    ->pluck('route_id');

                // ✅ Shop count
                $shop_count = DB::table('shop_tso')
                    ->join('shops', 'shop_tso.shop_id', '=', 'shops.id')
                    ->where('shop_tso.tso_id', $tso->id)
                    ->whereIn('shops.route_id', $routes)
                    ->when($shop_id, fn($q) => $q->where('shops.id', $shop_id))
                    ->when($route_id, fn($q) => $q->where('shops.route_id', $route_id))
                    ->distinct('shop_tso.shop_id')
                    ->count('shop_tso.shop_id');

                // ✅ Shop visits
                $shop_visits = DB::table('shops as a')
                    ->join('shop_visits as sv', 'a.id', '=', 'sv.shop_id')
                    ->join('shop_tso as st', 'a.id', '=', 'st.shop_id')
                    ->where('st.tso_id', $tso->id)
                    ->whereIn('a.route_id', $routes)
                    ->when($shop_id, fn($q) => $q->where('a.id', $shop_id))
                    ->when($route_id, fn($q) => $q->where('a.route_id', $route_id))
                    ->when($from && $to, fn($q) => $q->whereBetween('sv.visit_date', [$from, $to]))
                    ->count('sv.id');

                // ✅ Sales per product
                $salesByProduct = DB::table('sale_orders as so')
                    ->join('sale_order_data as sod', 'so.id', '=', 'sod.so_id')
                    ->join('products as p', 'p.id', '=', 'sod.product_id')
                    ->whereBetween('so.dc_date', [$from, $to])
                    ->where('so.tso_id', $tso->id)
                    ->where('so.distributor_id', $tso->distributor_id)
                    ->when($product_id, fn($q) => $q->where('p.id', $product_id))
                    ->when($shop_id, fn($q) => $q->where('so.shop_id', $shop_id))
                    ->when($route_id, fn($q) => $q->whereIn('so.shop_id', function($sub) use ($route_id) {
                        $sub->select('id')->from('shops')->where('route_id', $route_id);
                    }))
                    ->where('so.status', 1)
                    ->select(
                        'p.id as product_id',
                        'p.product_name',
                        // 'p.orderby', 
                        DB::raw('COUNT(DISTINCT so.shop_id) as productive_shops'),
                        DB::raw('SUM(sod.qty) as total_units')
                    )
                    ->groupBy('p.id', 'p.product_name')
                    ->orderBy('p.orderby', 'ASC')
                    ->get();

                foreach ($salesByProduct as $sale) {
                    $drop_size = $sale->productive_shops > 0
                        ? number_format($sale->total_units / $sale->productive_shops, 2)
                        : '0.00';

                    $reportData->push([
                        'tso' => $tso,
                        'product' => $sale->product_name,
                        'shop_count' => $shop_count,
                        'shop_visits' => $shop_visits,
                        'productive_count' => $sale->productive_shops,
                        'total_units' => $sale->total_units,
                        'drop_size' => $drop_size,
                        // 'orderby' => $sale->orderby,
                    ]);

                    // $reportData = $reportData->sortBy('orderby');
                }
            }

            // ✅ Totals across products
            $totals = [
                'productive' => $reportData->sum('productive_count'),
                'sale_units' => $reportData->sum('total_units'),
                'drop_size'  => $reportData->sum('productive_count') > 0
                    ? number_format($reportData->sum('total_units') / $reportData->sum('productive_count'), 2)
                    : '0.00'
            ];

            return view($this->page . 'orderSummary.brand_distributer_report_ajax', compact('reportData', 'from', 'to', 'totals'));
        }

        return view($this->page . 'orderSummary.brand_distributer_report');
    }

    public function brand_distributer_report_old(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $shop_id = $request->shop_id;
        $route_id = $request->route_id;

        if ($request->ajax()) {
        $tsos = TSO::status()
            ->active()
            ->join('users_distributors', 'users_distributors.user_id', '=', 'tso.user_id')
            ->whereIn('users_distributors.distributor_id', $this->master->get_users_distributors(Auth::id()))
            ->when($request->distributor_id, function ($query) use ($request) {
                $query->where('users_distributors.distributor_id', $request->distributor_id);
            })
            ->when($request->tso_id, function ($query) use ($request) {
                $query->where('tso.id', $request->tso_id);
            })
            ->when($request->designation, function ($query) use ($request) {
                $query->where('tso.designation_id', $request->designation);
            })
            ->when($request->city, function ($query) use ($request) {
                $query->where('tso.city', $request->city);
            })
            // ✅ Route filter via shops table
            ->when($request->route_id, function ($query) use ($request) {
                $query->whereExists(function ($subQuery) use ($request) {
                    $subQuery->select(DB::raw(1))
                        ->from('shops')
                        ->join('sale_orders', 'sale_orders.shop_id', '=', 'shops.id')
                        ->whereRaw('sale_orders.tso_id = tso.id')
                        ->where('shops.route_id', $request->route_id)
                        ->whereBetween('sale_orders.dc_date', [$request->from, $request->to]);
                });
            })
            // ✅ Shop filter
            // ->when($request->shop_id, function ($query) use ($request) {
            //     $query->whereExists(function ($subQuery) use ($request) {
            //         $subQuery->select(DB::raw(1))
            //             ->from('sale_orders')
            //             ->whereRaw('sale_orders.tso_id = tso.id')
            //             ->where('sale_orders.shop_id', $request->shop_id)
            //             ->whereBetween('sale_orders.dc_date', [$request->from, $request->to]);
            //     });
            // })
            ->whereExists(function ($query) use ($from, $to) {
                $query->select(DB::raw(1))
                    ->from('sale_orders')
                    ->whereRaw('sale_orders.tso_id = tso.id')
                    ->whereBetween('dc_date', [$from, $to]);
            })
            ->with(['designation', 'distributor', 'cities'])
            ->select('tso.*', 'users_distributors.distributor_id')
            ->get()
            ->toArray();
            // dd($tsos);
            return view($this->page . 'orderSummary.brand_distributer_report_ajax', compact('tsos', 'from', 'to', 'shop_id', 'route_id'));
        }

        return view($this->page . 'orderSummary.brand_distributer_report');
    }



    public function stock_report_new(Request $request)
    {
        ini_set('memory_limit', '4096M');
        set_time_limit(3000);
        if ($request->ajax()) {
    
            $type = $request->type;
            $detail = $request->detail;
            $from = $request->from;
            $to = $request->to;
            $city = $request->city;
            // dd($from, $to);
    
            if ($detail == 'summary') {
    
                $result = Product::join('stocks', 'stocks.product_id', '=', 'products.id')
                    ->Join('distributors', 'distributors.id', '=', 'stocks.distributor_id')
                    ->selectRaw("
                        SUM(CASE WHEN stock_type = 0 THEN stocks.qty ELSE 0 END) AS purchase_qty,
                        SUM(CASE WHEN stock_type = 1 THEN stocks.qty ELSE 0 END) AS opening_qty,
                        SUM(CASE WHEN stock_type = 2 THEN stocks.qty ELSE 0 END) AS transfer_received_qty,
                        SUM(CASE WHEN stock_type = 3 THEN stocks.qty ELSE 0 END) AS sales_qty,
                        SUM(CASE WHEN stock_type = 4 THEN stocks.qty ELSE 0 END) AS sales_return_qty,
                        SUM(CASE WHEN stock_type = 5 THEN stocks.qty ELSE 0 END) AS transfer_qty,
                        products.product_name,
                        products.packing_size,
                        distributors.max_discount,
                        products.sales_price,
                        products.id as product_id,
                        stocks.voucher_date,
                        stocks.stock_type,
                        stocks.flavour_id,
                        distributors.id as distributor_id,
                        distributors.city_id
                    ")
                    ->where('products.status', 1)
                    ->where('stocks.status', 1);
    
    
                // $result = Product::join('stocks','stocks.product_id','products.id')
                //     ->Join('distributors', 'distributors.id', '=', 'stocks.distributor_id')
                //     ->select(
                //                 DB::raw('SUM(CASE WHEN stock_type = 0 THEN stocks.qty ELSE 0 END) AS purchase_qty'),
                //                 DB::raw('SUM(CASE WHEN stock_type = 1 THEN stocks.qty ELSE 0 END) AS opening_qty'),
                //                 DB::raw('SUM(CASE WHEN stock_type = 2 THEN stocks.qty ELSE 0 END) AS transfer_received_qty'),
                //                 DB::raw('SUM(CASE WHEN stock_type = 3 THEN stocks.qty ELSE 0 END) AS sales_qty'),
                //                 DB::raw('SUM(CASE WHEN stock_type = 4 THEN stocks.qty ELSE 0 END) AS sales_return_qty'),
                //                 DB::raw('SUM(CASE WHEN stock_type = 5 THEN stocks.qty ELSE 0 END) AS transfer_qty'),
                //             'products.product_name','products.packing_size','distributors.max_discount','products.sales_price',
                //             'products.id as product_id','stocks.flavour_id','distributors.id as distributor_id','stocks.voucher_date'
                //         );
                        // $result = Product::join('stocks','stocks.product_id','products.id')
                        // ->leftJoin('distributors', 'distributors.id', '=', 'stocks.distributor_id')
                        // ->select('products.product_name','products.packing_size','distributors.max_discount','products.sales_price');
                    if (!empty($request->city)) {
                            $result->where('distributors.city_id', $request->city);
                    }        
                    if(!empty($request->product_id))
                            {
                                $result->where('stocks.product_id',$request->product_id);
                            }
                    // if(!empty($request->from))
                    // {
                    //     $result->where('stocks.voucher_date','>=',$request->from);
                    // }
                    // if(!empty($request->to))
                    // {
                    //     $result->where('stocks.voucher_date','<=',$request->to);
                    // }
    
                    // $result = $result->where('products.status',1)
                    // ->where('stocks.status',1)
                    // // ->where('products.product_type_id',1)
                    // ->groupBy('stocks.product_id','stocks.flavour_id')
                    // ->get();
                    $query1=$result;
                    $query = $result->groupBy('stocks.product_id', 'stocks.flavour_id')->get();
                    $result_grouped_vdate = $query1->groupBy('stocks.product_id', 'stocks.flavour_id','stocks.voucher_date')->get();
                    if(!empty($from)){
                        $previous_day = date('Y-m-d', strtotime($from . ' -1 day'));
    
                        $preparedData = MasterFormsHelper::prepare_stock_data($result_grouped_vdate, '', $to,'summary',$request->city);
                        
                        $preparedPurchaseDataPrevious = MasterFormsHelper::prepare_stock_type_data($result_grouped_vdate, [0], '', $previous_day,'summary',$request->city);
                        
                        $preparedSalesReturnDataPrevious=MasterFormsHelper::prepare_stock_type_data($result_grouped_vdate, [4], '', $previous_day,'summary',$request->city);
                        $preparedTransferRecievedDataPrevious=MasterFormsHelper::prepare_stock_type_data($result_grouped_vdate, [2], '', $previous_day,'summary',$request->city);
                        $preparedTransferDataPrevious=MasterFormsHelper::prepare_stock_type_data($result_grouped_vdate, [5], '', $previous_day,'summary',$request->city);
                        $preparedSalesDataPrevious=MasterFormsHelper::prepare_stock_type_data($result_grouped_vdate, [3], '', $previous_day,'summary',$request->city);
                    }
                    else{
                        $preparedPurchaseDataPrevious=[];
                        $preparedSalesReturnDataPrevious=[];
                        $preparedTransferRecievedDataPrevious=[];
                        $preparedTransferDataPrevious=[];
                        $preparedSalesDataPrevious=[];
                        $preparedData = MasterFormsHelper::prepare_stock_data($result_grouped_vdate, $from, $to,'summary',$request->city);
                     
                    }
    
                    $preparedPurchaseData = MasterFormsHelper::prepare_stock_type_data($result_grouped_vdate, [0], $from, $to,'summary',$request->city);
                    $preparedSalesReturnData=MasterFormsHelper::prepare_stock_type_data($result_grouped_vdate, [4], $from, $to,'summary',$request->city);
                    $preparedTransferRecievedData=MasterFormsHelper::prepare_stock_type_data($result_grouped_vdate, [2], $from, $to,'summary',$request->city);
                    //$preparedUnpackingInData=MasterFormsHelper::prepare_stock_type_data($result_grouped_vdate, [6], $from, $to,'summary');
                    // $preparedAvailableData=MasterFormsHelper::prepare_stock_type_data($result_grouped_vdate, [0,1,2,4], $from, $to,'summary');
                    //$preparedAvailableData=MasterFormsHelper::prepare_stock_type_data($result_grouped_vdate, [0,2,4], $from, $to,'summary');
                    $preparedTransferData=MasterFormsHelper::prepare_stock_type_data($result_grouped_vdate, [5], $from, $to,'summary',$request->city);
                    $preparedSalesData=MasterFormsHelper::prepare_stock_type_data($result_grouped_vdate, [3], $from, $to,'summary',$request->city);
    
                    $result=$query;
    
    
    
    
                            // dd($result->toArray());
            } else {
                
                    $query = Product::join('stocks', 'stocks.product_id', '=', 'products.id')
                    ->Join('distributors', 'distributors.id', '=', 'stocks.distributor_id')
                    ->selectRaw("
                        SUM(CASE WHEN stock_type = 0 THEN stocks.qty ELSE 0 END) AS purchase_qty,
                        SUM(CASE WHEN stock_type = 1 THEN stocks.qty ELSE 0 END) AS opening_qty,
                        SUM(CASE WHEN stock_type = 2 THEN stocks.qty ELSE 0 END) AS transfer_received_qty,
                        SUM(CASE WHEN stock_type = 3 THEN stocks.qty ELSE 0 END) AS sales_qty,
                        SUM(CASE WHEN stock_type = 4 THEN stocks.qty ELSE 0 END) AS sales_return_qty,
                        SUM(CASE WHEN stock_type = 5 THEN stocks.qty ELSE 0 END) AS transfer_qty,
                        products.product_name,
                        products.packing_size,
                        distributors.max_discount,
                        products.sales_price,
                        products.id as product_id,
                        stocks.voucher_date,
                        stocks.stock_type,
                        stocks.flavour_id,
                        distributors.id as distributor_id,
                        distributors.city_id,
                        distributors.distributor_name
                    ")
                    ->where('products.status', 1)
                    ->where('stocks.status', 1);
        
                if (!empty($request->city)) {
                    $query->where('distributors.city_id', $request->city);
                }
        
                if (!empty($request->distributor_id)) {
                    $query->where('stocks.distributor_id', $request->distributor_id);
                }
        
                if (!empty($request->product_id)) {  //&& $detail=='detail'
                    $query->where('stocks.product_id', $request->product_id);
                }
        
                // if (!empty($from) && !empty($to)) {
                //     $query->whereBetween('stocks.voucher_date', [$from, $to]);
                // }
    
                // if(!empty($request->from))
                // {
                //     $result->where('stocks.voucher_date','>=',$request->from);
                // }
                // if(!empty($request->to))
                // {
                //     $result->where('stocks.voucher_date','<=',$request->to);
                // }
        
                // Directly get all the records without pagination
                $query1=$query;
    
    
                $result = $query->groupBy('stocks.product_id', 'stocks.flavour_id', 'stocks.distributor_id')->get();
                $result_grouped_vdate = $query1->groupBy('stocks.product_id', 'stocks.flavour_id', 'stocks.distributor_id','stocks.voucher_date')->get();
                
                if(!empty($from)){
                    $previous_day = date('Y-m-d', strtotime($from . ' -1 day'));
                    $preparedData = MasterFormsHelper::prepare_stock_data($result_grouped_vdate, '', $to,'detail',$request->city);
                    $preparedPurchaseDataPrevious = MasterFormsHelper::prepare_stock_type_data($result_grouped_vdate, [0], '', $previous_day,'detail',$request->city);
                    $preparedSalesReturnDataPrevious=MasterFormsHelper::prepare_stock_type_data($result_grouped_vdate, [4], '', $previous_day,'detail',$request->city);
                    $preparedTransferRecievedDataPrevious=MasterFormsHelper::prepare_stock_type_data($result_grouped_vdate, [2], '', $previous_day,'detail',$request->city);
                    $preparedTransferDataPrevious=MasterFormsHelper::prepare_stock_type_data($result_grouped_vdate, [5], '', $previous_day,'detail',$request->city);
                    $preparedSalesDataPrevious=MasterFormsHelper::prepare_stock_type_data($result_grouped_vdate, [3], '', $previous_day,'detail',$request->city);
                    // dd($preparedSalesReturnDataPrevious);
                }else{
                    $preparedPurchaseDataPrevious=[];
                    $preparedSalesReturnDataPrevious=[];
                    $preparedTransferRecievedDataPrevious=[];
                    $preparedTransferDataPrevious=[];
                    $preparedSalesDataPrevious=[];
                    $preparedData = MasterFormsHelper::prepare_stock_data($result_grouped_vdate, $from, $to,'detail',$request->city);
                }
                
              
                $preparedPurchaseData = MasterFormsHelper::prepare_stock_type_data($result_grouped_vdate, [0], $from, $to,'detail',$request->city);
                $preparedSalesReturnData=MasterFormsHelper::prepare_stock_type_data($result_grouped_vdate, [4], $from, $to,'detail',$request->city);
                
        
                $preparedTransferRecievedData=MasterFormsHelper::prepare_stock_type_data($result_grouped_vdate, [2], $from, $to,'detail',$request->city);
                //$preparedUnpackingInData=MasterFormsHelper::prepare_stock_type_data($result_grouped_vdate, [6], $from, $to,'detail');
                // $preparedAvailableData=MasterFormsHelper::prepare_stock_type_data($result_grouped_vdate, [0,1,2,4], $from, $to,'detail');
                //$preparedAvailableData=MasterFormsHelper::prepare_stock_type_data($result_grouped_vdate, [0,2,4], $from, $to,'detail');
                $preparedTransferData=MasterFormsHelper::prepare_stock_type_data($result_grouped_vdate, [5], $from, $to,'detail',$request->city);
                $preparedSalesData=MasterFormsHelper::prepare_stock_type_data($result_grouped_vdate, [3], $from, $to,'detail',$request->city);
                
                //$preparedUnpackingOutData=MasterFormsHelper::prepare_stock_type_data($result_grouped_vdate, [7], $from, $to,'detail');
            }
    
            
            
            if ($detail == 'summary') {
               return view($this->page . 'stock.stock_report_Ajax_detail_new', compact('preparedPurchaseDataPrevious','preparedSalesReturnDataPrevious','preparedTransferRecievedDataPrevious','preparedSalesDataPrevious','preparedSalesReturnData','preparedSalesData','preparedTransferData','preparedTransferRecievedData','preparedTransferDataPrevious','preparedData','preparedPurchaseData','result', 'type', 'detail', 'from', 'to'));
            } else {
               
                return view($this->page . 'stock.stock_report_Ajax_new', compact('preparedPurchaseDataPrevious','preparedSalesReturnDataPrevious','preparedTransferRecievedDataPrevious','preparedTransferDataPrevious','preparedSalesDataPrevious','preparedSalesReturnData','preparedSalesData','preparedTransferData','preparedTransferRecievedData','preparedData','preparedPurchaseData','result', 'type', 'detail', 'from', 'to'));
            }
        }
    
        return view($this->page . 'stock.stock_report_new');
    }

public function closing_stock_minus_zero(Request $request){
	

        return view($this->page . 'stock.closing_stock_minus_zero');
    }









public function closing_stock_minus_zero_sub(Request $request)
{
 
    $result = Product::join('stocks', 'stocks.product_id', 'products.id')
        ->leftJoin('distributors', 'distributors.id', '=', 'stocks.distributor_id')
        ->select(
            DB::raw('SUM(CASE WHEN stock_type = 0 THEN stocks.qty ELSE 0 END) AS purchase_qty'),
            DB::raw('SUM(CASE WHEN stock_type = 1 THEN stocks.qty ELSE 0 END) AS opening_qty'),
            DB::raw('SUM(CASE WHEN stock_type = 2 THEN stocks.qty ELSE 0 END) AS transfer_received_qty'),
            DB::raw('SUM(CASE WHEN stock_type = 3 THEN stocks.qty ELSE 0 END) AS sales_qty'),
            DB::raw('SUM(CASE WHEN stock_type = 4 THEN stocks.qty ELSE 0 END) AS sales_return_qty'),
            DB::raw('SUM(CASE WHEN stock_type = 5 THEN stocks.qty ELSE 0 END) AS transfer_qty'),
            'products.product_name', 'products.packing_size', 'distributors.max_discount', 'products.sales_price',
            'products.id as product_id', 'stocks.flavour_id', 'distributors.id as distributor_id'
        );

    if (!empty($request->city)) {
        $result->where('distributors.city_id', $request->city);
    }

    if (!empty($request->distributor_id)) {
        $result->where('stocks.distributor_id', $request->distributor_id);
    }

    if (!empty($request->product_id)) {
        $result->where('stocks.product_id', $request->product_id);
    }

    if (!empty($request->from)) {
        $result->where('stocks.voucher_date', '>=', $request->from);
    }

    if (!empty($request->to)) {
        $result->where('stocks.voucher_date', '<=', $request->to);
    }

    $result = $result->where('products.status', 1)
        ->where('stocks.status', 1)
        ->groupBy('products.id', 'stocks.flavour_id')
        ->get();
       

    $from = null;
    $to = Carbon::now()->toDateString();

    foreach ($result as $item) {
        $packing = $item->packing_size > 0 ? $item->packing_size : 1;
        $discount_amount = ($item->sales_price / 100) * $item->max_discount;
        $price = $item->sales_price - $discount_amount;



 $get_sales = MasterFormsHelper::get_stock_type_wise_data_with_date_ordersalecheck(
            $item->product_id,
            $item->flavour_id,
            $item->distributor_id,
            [3],
            $from,
            $to,
        );
        $sales_qty = $get_sales['main_qty'];
        $sales_amount = $get_sales['main_amount'];
//dd($sales_qty);	

        $get_available = MasterFormsHelper::get_stock_type_wise_data_with_date(
            $item->product_id,
            $item->flavour_id,
            $item->distributor_id,
            [0, 1, 2, 4],
            $from,
            $to,
        );
        $available_qty = $get_available['main_qty'];
        $available_amount = $get_available['main_amount'];

       


        $sales_qty_display = 0;
        $sales_qty_carton_display = 0;
        foreach (explode(',', $sales_qty) as $val) {
            $qty_explode = explode('=>', $val);
            $qty = isset($qty_explode[0]) ? (float)$qty_explode[0] : 0;
            $unit = isset($qty_explode[1]) ? $qty_explode[1] : '-';
            $pcsPerCarton = isset($qty_explode[2]) ? (float)$qty_explode[2] : 1;
            if ($pcsPerCarton == 0) $pcsPerCarton = 1;

            if (str_replace(' ', '', $unit) == 'Carton') {
                $sales_qty_carton_display += $qty;
                $sales_qty_display += ($qty * $pcsPerCarton);
            } else {
                $sales_qty_display += $qty;
                $sales_qty_carton_display += ($qty / $pcsPerCarton);
            }
        }



        $available_qty_display = 0;
        $available_qty_carton_display = 0;
        foreach (explode(',', $available_qty) as $val) {
            $qty_explode = explode('=>', $val);
            $qty = isset($qty_explode[0]) ? (float)$qty_explode[0] : 0;
            $unit = isset($qty_explode[1]) ? $qty_explode[1] : '-';
            $pcsPerCarton = isset($qty_explode[2]) ? (float)$qty_explode[2] : 1;
            if ($pcsPerCarton == 0) $pcsPerCarton = 1;

            if (str_replace(' ', '', $unit) == 'Carton') {
                $available_qty_carton_display += $qty;
                $available_qty_display += ($qty * $pcsPerCarton);
            } else {
                $available_qty_display += $qty;
                $available_qty_carton_display += ($qty / $pcsPerCarton);
            }
        }

 $return_qty_display = 0;
                    $return_qty_carton_display = 0;  
                    $return_unit_display = ''; 


                    $get_sales_return = MasterFormsHelper::get_stock_type_wise_data_with_date(
                        $item->product_id,
                        $item->flavour_id,
                        $item->distributor_id,
                        [4],
                        $from,
                        $to,
                    );
                    $sales_return_qty = $get_sales_return['main_qty'];
                    $return_amount = $get_sales_return['main_amount'];

    			foreach (explode(',', $sales_return_qty) as $val) {
                       
                            $qty_explode = explode('=>', $val);
                            $qty = isset($qty_explode[0]) ? (float)$qty_explode[0] : 0;
                            $unit = isset($qty_explode[1]) ? $qty_explode[1] : '-';
			    $pcsPerCarton = isset($qty_explode[2]) ? (float)$qty_explode[2] : 1;
				if($pcsPerCarton ==0 || $pcsPerCarton  ==''){ $pcsPerCarton=1; }
                            if(str_replace(' ', '', $unit)=='Bo'){
                                $unit='Box';
                            }
                            if(str_replace(' ', '', $unit)=='Carton'){
                                $return_qty_carton_display+=$qty;
                               
				
				  $return_qty_display += ($qty * $pcsPerCarton);
				
				
                            }else{
                                $return_qty_display += $qty;
                               
				
				  
				  $return_qty_carton_display+= ($qty / $pcsPerCarton);
				 
				
                            }
                            
                            
                            $return_unit_display .= $unit . '<br/>';

                    }

$sales_qty_display = $sales_qty_display - $return_qty_display;

        $closing_qty_display_new = $available_qty_display - $sales_qty_display;
        $closing_qty_carton_display_new = $available_qty_carton_display - $sales_qty_carton_display;
        $closing_amount_new = $available_amount - $sales_amount;


//dd($available_qty_display,$sales_qty_display,$return_qty_display);



        $get_purchase = MasterFormsHelper::get_stock_type_wise_data_with_date(
            $item->product_id,
            $item->flavour_id,
            $item->distributor_id,
            [0],
            $from,
            $to,
        );
        $purchase_qty = $get_purchase['main_qty'];

        foreach (explode(',', $purchase_qty) as $val) {
            $qty_explode = explode('=>', $val);
            $qty = isset($qty_explode[0]) ? (float)$qty_explode[0] : 0;
            $unit = isset($qty_explode[1]) ? $qty_explode[1] : '-';
            $pcsPerCarton = isset($qty_explode[2]) ? (float)$qty_explode[2] : 1;
            if ($pcsPerCarton == 0) $pcsPerCarton = 1;
        }

        $placedOrders = SaleOrderData::where('product_id', $item->product_id)
            ->where('flavour_id', $item->flavour_id)
            ->whereHas('saleOrder', function ($query) use ($item, $to) {
                $query->where('excecution', 0)
                    ->where('distributor_id', $item->distributor_id)
                    ->where('status', 1)
                    ->where('created_at', '<=', $to . ' 23:59:59');
            })
        ->sum('qty');

        $pcsPerCarton = ProductPrice::select('pcs_per_carton')
            ->where('product_id', $item->product_id)
            ->where('uom_id', '!=', 7)
            ->where('status', 1)
            ->where('start_date', '<=', date('Y-m-d'))
            ->orderBy('start_date', 'desc')
            ->value('pcs_per_carton');

       $booking_order_ctn = ($pcsPerCarton == 7) 
    ? $placedOrders 
    : (($pcsPerCarton != 0) ? ($placedOrders / $pcsPerCarton) : 0);


        $get_sales_carton = SaleOrderData::where('product_id', $item->product_id)
            ->where('flavour_id', $item->flavour_id)
            ->where('sale_type', '=', 7)
            ->whereHas('saleOrder', function ($query) use ($item, $to) {
                $query->where('excecution', 0)
                    ->where('distributor_id', $item->distributor_id)
                    ->where('status', 1)
                    ->where('created_at', '<=', $to);
            })
            ->get();

        $get_sales_pcs = SaleOrderData::where('product_id', $item->product_id)
            ->where('flavour_id', $item->flavour_id)
            ->where('sale_type', '!=', 7)
            ->whereHas('saleOrder', function ($query) use ($item, $to) {
                $query->where('excecution', 0)
                    ->where('distributor_id', $item->distributor_id)
                    ->where('status', 1)
                    ->where('created_at', '<=', $to);
            })
            ->get();

        $get_sales_carton_amount = $get_sales_carton->sum('total');
        $get_sales_pcs_amount = $get_sales_pcs->sum('total');
        $totaltotalvalue = $get_sales_carton_amount + $get_sales_pcs_amount;

        $reduction_needed =  $closing_qty_display_new;

            

            if ($closing_qty_display_new != 0) {
            $existingStocks = Stock::where('distributor_id', $item->distributor_id)
                ->where('product_id', $item->product_id)
                ->where('stock_type', 0)
                ->where('status', 1)
                ->get();

                    
                $existingStocksOpening = Stock::where('distributor_id', $item->distributor_id)
                    ->where('product_id', $item->product_id)
                    ->where('stock_type', 1) // Opening
                    ->where('status', 1)
                    ->get();

                if ($existingStocks->isEmpty() && $existingStocksOpening->isEmpty()) {
                    continue; // No stock to adjust, skip this item
                }



            // if ($existingStocks->isEmpty()) {
            //     return redirect()->route('closing_stock_minus_zero')->with('success', 'Closing stock adjusted successfully.');
            // }

            $reduction_remaining = $reduction_needed;

            foreach ($existingStocks as $stock) {
                if ($stock->flavour_id == $item->flavour_id && $reduction_remaining > 0) {
                    if ($stock->qty >= $reduction_remaining) {
                        // Stock has enough to cover all remaining reduction
                        $stock->qty -= $reduction_remaining;
                        $stock->save();
                        $reduction_remaining = 0; // done
                        break;
                    } else {
                        // Stock has partial quantity, use all and reduce remaining
                        $reduction_remaining -= $stock->qty;
                        $stock->qty = 0;
                        $stock->save();
                        // continue loop for next matching stock
                    }
                }
            }


                // If purchase stock was not enough, reduce from OPENING stock
            if ($reduction_remaining > 0) {
                foreach ($existingStocksOpening as $stock) {
                    if ($stock->flavour_id == $item->flavour_id && $reduction_remaining > 0) {
                        if ($stock->qty >= $reduction_remaining) {
                            $stock->qty -= $reduction_remaining;
                            $stock->save();
                            $reduction_remaining = 0;
                            break;
                        } else {
                            $reduction_remaining -= $stock->qty;
                            $stock->qty = 0;
                            $stock->save();
                        }
                    }
                }
            }


}

    }

    return redirect()->route('closing_stock_minus_zero')->with('success', 'Closing Stock Adjustment successfully.');
}


  public function report_center(Request $request)
    {

         return view($this->page . 'report_center');

    }


  public function report_center_super(Request $request)
    {

         return view($this->page . 'report_center_super');

    }


    public function shop_ledger_report(Request $request)
    {
        $date = $request->from;
        $from_date=$request->from;
        $to_date=$request->to;
            if ($request->ajax()) :

                $saleOrders = SaleOrder::select('dc_date', 'shop_id', 'invoice_no', 'notes', 'transport_details', 'total_amount')
                ->with('shop')
                ->where('excecution', 1)
                ->when(!empty($from_date) && !empty($to_date), function ($query) use ($from_date, $to_date) {
                    return $query->whereBetween('dc_date', [$from_date, $to_date]);
                })
                ->when($request->distributor_id != null, function ($query) use ($request) {
                    return $query->where('distributor_id', $request->distributor_id);
                })
                ->when($request->tso_id != null, function ($query) use ($request) {
                    return $query->where('tso_id', $request->tso_id);
                })
                ->when($request->shop_id != null, function ($query) use ($request) {
                    return $query->where('shop_id', $request->shop_id);
                })
                ->orderBy('shop_id')
                ->get();


                $receiptVouchers = ReceiptVoucher::select('id as rec_id', 'issue_date', 'shop_id', 'amount', 'remarks', 'detail')
                    ->where('status', 1)
                    ->where('execution', 1)
                    ->when(!empty($from_date) && !empty($to_date), function ($query) use ($from_date, $to_date) {
                        return $query->whereBetween('issue_date', [$from_date, $to_date]);
                    })
                    ->when($request->distributor_id != null, function ($query) use ($request) {
                        return $query->where('distributor_id', $request->distributor_id);
                    })
                    ->when($request->tso_id != null, function ($query) use ($request) {
                        return $query->where('tso_id', $request->tso_id);
                    })
                    ->when($request->shop_id != null, function ($query) use ($request) {
                        return $query->where('shop_id', $request->shop_id);
                    })
                    ->with('shop')
                    ->orderBy('shop_id')
                    ->get();

                    $shops= Shop::where('status',1)
                    ->when($request->shop_id != null, function ($query) use ($request) {
                        return $query->where('id', $request->shop_id);
                    })
                    ->get();



                return view($this->page . 'Shop.shop_ledger_report_ajax', compact('saleOrders','shops','receiptVouchers', 'date','from_date','to_date'));
            else :
                return view($this->page . 'Shop.shop_ledger_report');
            endif;

    }



    public function receipt_voucher_summary(Request $request)
    {
        $date = $request->from;
        $from_date=$request->from;
        $to_date=$request->to;
            if ($request->ajax()) :

                $receipt_vouchers = ReceiptVoucher::where('status', 1)
                ->when($request->distributor_id != null, function ($query) use ($request) {
                    return $query->where('distributor_id', $request->distributor_id);
                })
                ->when($request->tso_id != null, function ($query) use ($request) {
                    return $query->where('tso_id', $request->tso_id);
                })
                ->when(($request->from != '' && $request->to != ''), function ($query) use ($request) {
                    return $query->whereBetween('issue_date', [$request->from, $request->to]);
                })
                ->with('tso','distributor','route','deliveryMan','shop')
                ->get();



                return view($this->page . 'ReceiptVoucher.receipt_voucher_summary_ajax', compact('receipt_vouchers', 'date','from_date','to_date'));
            else :
                return view($this->page . 'ReceiptVoucher.receipt_voucher_summary');
            endif;

    }

    public function order_summary(Request $request)
    {


        $from = $request->from;
        $date = $from;
        $to = $request->to;
        $distributor_id = $request->distributor_id;
        $tso_id = $request->tso_id;

            if ($request->ajax()) :

                $tsos =  TSO::status()->active()
                    ->join('users_distributors','users_distributors.user_id','tso.user_id')
                    ->whereIn('users_distributors.distributor_id', $this->master->get_users_distributors(Auth::user()->id))
                    ->when($request->distributor_id != null, function ($query) use ($request) {

                        $query->where('users_distributors.distributor_id', $request->distributor_id);
                    })->when($request->tso_id != null, function ($query) use ($request) {

                        $query->where('tso.id', $request->tso_id);
                    })->when($request->designation != null, function ($query) use ($request) {

                        $query->where('tso.designation_id', $request->designation);
                    })->when($request->city != null, function ($query) use ($request) {

                        $query->where('tso.city', $request->city);
                    })->with(['attendence' => function ($query) use ($request) {

                    $query->whereRaw('DATE(`in`) BETWEEN ? AND ?', [$request->from, $request->to]);
                    if ($request->tso_id!=null)
                    $query->groupBy(DB::raw('DATE(`in`)'));


                    }, 'designation', 'distributor', 'cities'])->select('tso.*','users_distributors.distributor_id')->get()->toArray();


                return view($this->page . 'orderSummary.order_summary_ajax', compact('tsos', 'date', 'from', 'to', 'distributor_id', 'tso_id'));
            else :
                return view($this->page . 'orderSummary.order_summary');
            endif;

    }
    public function order_list(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $type = $request->report_type;

        if ($request->ajax()) :
            $order_list =   DB::table('sale_orders as a')
                ->join('sale_order_data', 'sale_order_data.so_id', 'a.id')
                ->join('shops', 'shops.id', 'a.shop_id')
                ->join('routes', 'routes.id', 'shops.route_id')
                ->join('distributors as b', 'a.distributor_id', 'b.id')
                ->join('tso as c', function ($join) use ($request) {
                    $join->on('c.id', '=', 'a.tso_id')->where('c.active', 1);
                    if ($request->city != null)
                        $join->where('c.city', $request->city);
                    if ($request->designation != null)
                        $join->where('designation_id', $request->designation);
                })
                ->join('users_distributors','c.user_id','=','users_distributors.user_id')
                ->when($request->distributor_id == null, function ($query) use ($request) {

                    $query->whereIn('users_distributors.distributor_id' ,MasterFormsHelper::get_users_distributors(Auth::user()->id));
                })
                ->join('cities', 'cities.id', 'c.city')
                ->when($request->distributor_id != null, function ($query) use ($request) {
                    $query->where('a.distributor_id', $request->distributor_id);
                })
                ->when($request->tso_id != null, function ($query) use ($request) {
                    $query->where('a.tso_id', $request->tso_id);
                })

                ->where('a.status', 1)
                ->where('c.status',1)
                ->whereBetween('a.dc_date', [$from, $to])
                ->when($request->report_type == 0, function ($query) use ($request) {
                    $query->select('a.id', 'b.distributor_name', 'c.name as tso', 'c.id as tso_id', 'c.user_id', 'a.dc_date', 'shops.company_name as shop_name', DB::raw('SUM(sale_order_data.total) as total_amount'), 'routes.route_name', 'cities.name as city', 'a.invoice_no')
                    ->groupBy('a.id');
                })
                ->when($request->report_type == 1, function ($query) use ($request) {
                    $query->select('a.id', 'b.distributor_name', 'c.name as tso', 'c.id as tso_id', 'c.user_id', 'a.dc_date', 'shops.company_name as shop_name', DB::raw('SUM(sale_order_data.total) as total_amount'), 'routes.route_name', 'cities.name as city', 'a.invoice_no')
                    ->groupBy('a.tso_id');
                })
                // ->select('b.distributor_name','c.name as tso','a.dc_date', 'shops.company_name as shop_name', 'SUM(sale_order_data.total) as total_amount', 'routes.route_name', 'cities.name as city','a.invoice_no')

                ->get();
            //    dd($order_list);
            return view($this->page . 'orderList.order_list_ajax', compact('order_list','type'));
        endif;
        return view($this->page . 'orderList.order_list');
    }


    public function product_avail(Request $request)
    {
        if ($request->ajax()) :

            $data =   DB::table('sale_orders as a')
                ->join('sale_order_data as d', 'd.so_id', 'a.id')
                ->join('shops', 'shops.id', 'a.shop_id')
                ->join('products', 'd.product_id', 'products.id')
                ->join('routes', function ($join) use ($request) {
                    $join->on('routes.id', '=', 'shops.route_id');
                    if ($request->route_id != null)
                        $join->where('routes.id', $request->route_id);
                })
                ->join('distributors as b', 'a.distributor_id', 'b.id')
                ->join('tso as c', function ($join) use ($request) {
                    $join->on('c.id', '=', 'a.tso_id');
                    if ($request->city != null)
                        $join->where('c.city', $request->city);
                })
                ->join('users_distributors','c.user_id','=','users_distributors.user_id')
                ->when($request->distributor_id == null, function ($query) use ($request) {

                    $query->whereIn('users_distributors.distributor_id' ,MasterFormsHelper::get_users_distributors(Auth::user()->id));
                })
                ->join('cities', 'cities.id', 'c.city')
                ->when($request->distributor_id != null, function ($query) use ($request) {
                    $query->where('a.distributor_id', $request->distributor_id);
                })
                ->when($request->tso_id != null, function ($query) use ($request) {
                    $query->where('a.tso_id', $request->tso_id);
                })
                ->when($request->shop_id != null, function ($query) use ($request) {
                    $query->where('a.shop_id', $request->shop_id);
                })
                ->when($request->product_id != null, function ($query) use ($request) {
                    $query->where('d.product_id', $request->product_id);
                })
                ->where('d.availability', '>', 0)
                ->where('a.status', 1)
                ->where('c.status', 1)
                ->select(
                    'a.id',
                    'b.distributor_name',
                    'c.name as tso',
                    'c.id as tso_id',
                    'c.user_id',
                    'a.dc_date',
                    'shops.company_name as shop_name',
                    DB::raw('SUM(d.total) as total_amount'),
                    'routes.route_name',
                    'cities.name as city',
                    'products.product_name',
                    'd.availability',
                    'd.foc'
                )
                ->groupBy('a.shop_id')
                ->groupBy('d.product_id')
                ->orderBy('d.id', 'DESC')
                ->get();
            return view($this->page . 'productAvail.product_avail_ajax', compact('data'));
        endif;

        return view($this->page . 'productAvail.product_avail');
    }

    public function product_productivity(Request $request)
    {
        if ($request->ajax()) :
            $from = $request->from;
            $to = $request->to;
            $data =   DB::table('sale_orders as s')
            ->join('sale_order_data as sd' , 'sd.so_id' , 's.id')
            ->join('products as p' ,'p.id','sd.product_id')
            ->when($request->product_id != null, function ($query) use ($request) {
                $query->where('sd.product_id', $request->product_id);
            })
            ->when($request->distributor_id != null, function ($query) use ($request) {
                $query->where('s.distributor_id', $request->distributor_id);
            })
            ->when($request->tso_id != null, function ($query) use ($request) {
                $query->where('s.tso_id', $request->tso_id);
            })
            ->when($request->from != null && $request->to != null, function ($query) use ($request) {
                $query->whereBetween('s.dc_date', [$request->from, $request->to]);
            })
            ->select('sd.product_id' , 'sd.flavour_id' , 'sd.sale_type','s.distributor_id' , 's.tso_id',
            DB::raw('COUNT(DISTINCT s.shop_id) as distinct_shop_count'),
            DB::raw('COUNT(s.shop_id) as shop_count'))
            ->groupby('sd.product_id' , 'sd.flavour_id' , 'sd.sale_type','s.distributor_id' , 's.tso_id')
            ->get();
            // dd($data);
            return view($this->page . 'productProductivity.product_productivity_ajax', compact('data' , 'from' , 'to'));
        endif;

        return view($this->page . 'productProductivity.product_productivity');
    }

function loadSheet($from, $to, $tso_id, $distributor_id, $execution)
{
    $from = $from ?? date('Y-m-d');
    $to =  $to ?? date('Y-m-d');

    $so_data = DB::table('sale_orders')
        ->join('sale_order_data', 'sale_order_data.so_id', 'sale_orders.id')
        ->join('products', 'products.id', 'sale_order_data.product_id')
        ->join('uom', 'uom.id', 'sale_order_data.sale_type')
        ->where('sale_orders.status', 1)
        ->whereBetween('sale_orders.dc_date', [$from, $to])
        ->select(
            'products.id as product_id',
            'products.product_name',
            'sale_order_data.flavour_id',
            'sale_order_data.sale_type',
            DB::raw('SUM(sale_order_data.qty) as qty'),
            DB::raw('SUM(sale_order_data.total) as amount'),
            'sale_orders.excecution'
        )
        ->groupBy('sale_order_data.product_id', 'sale_order_data.flavour_id', 'sale_order_data.sale_type');

    if (!empty($tso_id) && $tso_id != 0) {
        $so_data = $so_data->where('sale_orders.tso_id', $tso_id);
    }

    if (!empty($distributor_id) && $distributor_id != 0) {
        $so_data = $so_data->where('sale_orders.distributor_id', $distributor_id);
    }

    if (isset($execution) && $execution != 0) {
        $so_data = $so_data->where('sale_orders.excecution', $execution);
    }

    $so_data = $so_data->get();

    $master = new \App\Helpers\MasterFormsHelper();
    $html = "<h4>Load Sheet (" . $from . " to " . $to . ")</h4>";

    if (count($so_data) > 0) {
        $i = 1;
        $total = 0;
        $grand_total_qty = [];

        // Group data manually
        $grouped_data = [];
        foreach ($so_data as $item) {
            $key = $item->product_id . '-' . $item->flavour_id;

            if (!isset($grouped_data[$key])) {
                $grouped_data[$key] = [
                    'product_name' => $item->product_name,
                    'flavour_id' => $item->flavour_id,
                    'qtys' => [],
                    'prices' => [],
                    'total' => 0,
                ];
            }

            $uom_name = $master->uom_name($item->sale_type);
            $grouped_data[$key]['qtys'][] = number_format($item->qty);

            // Fetch trade price if available
            $trade_price = $master::get_trade_price($item->product_id, $item->sale_type) ?? 0;
            $grouped_data[$key]['prices'][] = number_format($trade_price, 2);

            $grouped_data[$key]['total'] += $item->amount;

            // Accumulate grand total qty
            $grand_total_qty[$item->sale_type] = isset($grand_total_qty[$item->sale_type])
                ? $grand_total_qty[$item->sale_type] + $item->qty
                : $item->qty;
        }

        // Start table
        $html .= "<table class='table table-bordered'><thead>
                    <tr>
                        <th>Sr No</th>
                        <th>Product Name</th>
                        <th>Flavour Name</th>
                        <th>Total Sale Unit</th>
                        <th>T.P Rate</th>
                        <th>Total Amount</th>
                    </tr>
                  </thead><tbody>";

        // Render table rows
        foreach ($grouped_data as $item) {
            $html .= "<tr>
                        <td>{$i}</td>
                        <td>{$item['product_name']}</td>
                        <td>" . ($master::get_flavour_name($item['flavour_id']) ?? '--') . "</td>
                        <td>" . implode(' , ', $item['qtys']) . "</td>
                        <td>" . implode(' , ', $item['prices']) . "</td>
                        <td>" . number_format($item['total'], 2) . "</td>
                      </tr>";
            $i++;
            $total += $item['total'];
        }

        // Grand total row
      //  $total_qty_value = '';
        // foreach ($grand_total_qty as $uom_id => $qty) {
        //     $uom_name = $master->uom_name($uom_id);
        //     $total_qty_value .= ($total_qty_value ? '<br>' : '') . number_format($qty);
        // }

         $total_qty_value = array_sum($grand_total_qty);

        $html .= "<tr>
                    <td colspan='3'><strong>Total</strong></td>
                    <td>{$total_qty_value}</td>
                    <td></td>
                    <td><strong>" . number_format($total, 2) . "</strong></td>
                  </tr>";

        $html .= "</tbody></table>";
    } else {
        $html .= "<table class='table table-bordered'><tr><td colspan='6' style='background:rgb(255, 170, 170)'>No Record Found</td></tr></table>";
    }

$qo_summary = DB::table('sale_orders')
    ->join('shops', 'shops.id', '=', 'sale_orders.shop_id')
     ->join('tso', 'tso.id', '=', 'sale_orders.tso_id') // ✅ added
    ->join('sale_order_data', 'sale_orders.id', '=', 'sale_order_data.so_id')
    ->where('sale_orders.status', 1)
    ->whereBetween('sale_orders.dc_date', [$from, $to])
    ->select(
        'sale_orders.id as so_id',
        'sale_orders.invoice_no as invoice_no',
        'sale_orders.dc_date',
        'shops.company_name',
        'tso.name as tso_name', // ✅ added
        DB::raw('SUM(sale_order_data.qty) as total_qty'),
        DB::raw('SUM(sale_order_data.total) as total_amount')
    )
    ->when($tso_id && $tso_id != 0, fn($q) => $q->where('sale_orders.tso_id', $tso_id))
    ->when($distributor_id && $distributor_id != 0, fn($q) => $q->where('sale_orders.distributor_id', $distributor_id))
    ->when(isset($execution) && $execution != 0, fn($q) => $q->where('sale_orders.excecution', $execution))
    ->groupBy('sale_orders.id', 'sale_orders.dc_date', 'shops.company_name', 'tso.name') // ✅ updated

 
    ->orderBy('sale_orders.invoice_no', 'ASC')
    ->get();

if (count($qo_summary) > 0) {
    $html .= "<br><h4>Order Summary</h4>";
    $html .= "<table class='table table-bordered'>
        <thead>
            <tr>
                <th style='text-align: center;'>S#</th>
                <th style='text-align: center;'>Invoice No</th>
                <th style='text-align: center;'>Date</th>
                <th style='text-align: center;'>Shop Name</th>
                 <th style='text-align: center;'>Order Booker Name</th>
                <th style='text-align: center;'>Total Order Qty</th>
                <th style='text-align: center;'>Amount</th>
            </tr>
        </thead>
        <tbody>";

    $s = 1;
    $total_qty = 0;
    $total_amount = 0;

    foreach ($qo_summary as $row) {
        $html .= "<tr>
            <td style='text-align: center;'>{$s}</td>
            <td style='text-align: center;'>{$row->invoice_no}</td>
            <td style='text-align: center;'>" . date('d-M-Y', strtotime($row->dc_date)) . "</td>
            <td style='text-align: center;'>{$row->company_name}</td>
               <td style='text-align: center;'>{$row->tso_name }</td>
            <td style='text-align: center;'>" . number_format($row->total_qty) . "</td>
            <td style='text-align: center;'>" . number_format($row->total_amount, 2) . "</td>
        </tr>";
        $s++;
        $total_qty += $row->total_qty;
        $total_amount += $row->total_amount;
    }

    $html .= "<tr style='background:#f2f2f2'>
        <th colspan='5'>Grand Total</th>
        <th style='text-align: center;'>" . number_format($total_qty) . "</th>
        <th style='text-align: center;'>" . number_format($total_amount, 2) . "</th>
    </tr>";

    $html .= "</tbody></table>";
}


    return $html;
}


    function load_Sheet(Request $request)
    {
        $from = $request->from ?? date('Y-m-d');
        $to =  $request->to ?? date('Y-m-d');

        if ($request->ajax()) :


            $tso_id =  $request->tso_id;
            $distributor_id =  $request->distributor_id;
            $execution = $request->execution;


            $so_data = DB::table('sale_orders')->join('sale_order_data', 'sale_order_data.so_id', 'sale_orders.id')
                ->join('products', 'products.id', 'sale_order_data.product_id')
                ->join('uom', 'uom.id', 'sale_order_data.sale_type')
                ->where('sale_orders.status', 1)
                ->whereBetween('sale_orders.dc_date', [$from, $to])
                ->select(DB::raw('sum(sale_order_data.qty) as qty'), 'products.id as product_id' ,'products.product_name' , 'sale_order_data.flavour_id' , DB::raw('sum(sale_order_data.total) as amount')
                ,'sale_orders.excecution')
                // ->select('products.id as product_id' ,'products.product_name' , 'sale_order_data.flavour_id' ,
                // DB::raw('sum(sale_order_data.total) as amount'))
                // ->addSelect([
                //     'qty_summary' => DB::table('sale_order_data')
                //         ->join('uom', 'uom.id', '=', 'sale_order_data.sale_type')
                //         ->select(DB::raw("GROUP_CONCAT(CONCAT(SUM(sale_order_data.qty), 'x', uom.uom_name) SEPARATOR ', ')"))
                //         ->whereColumn('sale_order_data.product_id', 'products.id')
                //         ->whereColumn('sale_order_data.flavour_id', 'sale_order_data.flavour_id')
                //         ->whereBetween('sale_orders.dc_date', [$from, $to])
                //         ->groupBy('sale_order_data.product_id', 'sale_order_data.flavour_id','sale_order_data.sale_type')
                // ])
                ->groupby('sale_order_data.product_id' , 'sale_order_data.flavour_id');
            if (!empty($tso_id)) {
                $so_data = $so_data->where('sale_orders.tso_id', $tso_id);
            }

            if (!empty($distributor_id)) {
                $so_data = $so_data->where('sale_orders.distributor_id', $distributor_id);
            }
            if (isset($execution)) {
                $so_data = $so_data->where('sale_orders.excecution', $execution);
            }
            $so_data  = $so_data->get();


           
$summary_data = DB::table('sale_orders')
    ->join('shops', 'shops.id', '=', 'sale_orders.shop_id')
    ->join('tso', 'tso.id', '=', 'sale_orders.tso_id') // ✅ added
    ->join('sale_order_data', 'sale_orders.id', '=', 'sale_order_data.so_id')
    ->where('sale_orders.status', 1)
    ->whereBetween('sale_orders.dc_date', [$from, $to])
    ->select(
        'sale_orders.id as so_id',
        'sale_orders.invoice_no as invoice_no',
        'sale_orders.dc_date',
        'shops.company_name',
        'tso.name as tso_name', // ✅ added
        DB::raw('SUM(sale_order_data.qty) as total_qty'),
        DB::raw('SUM(sale_order_data.total) as total_amount')
    )
    ->when($tso_id, fn($q) => $q->where('sale_orders.tso_id', $tso_id))
    ->when($distributor_id, fn($q) => $q->where('sale_orders.distributor_id', $distributor_id))
    ->when(isset($execution), fn($q) => $q->where('sale_orders.excecution', $execution))
    ->groupBy(
        'sale_orders.id',
        'sale_orders.dc_date',
        'shops.company_name',
        'tso.name' // ✅ group by
    )
    ->orderBy('sale_orders.invoice_no', 'ASC')
    ->get();



            return view($this->page . 'loadsheet.load_sheet_ajax', compact('so_data', 'from', 'to','tso_id','distributor_id','execution','summary_data'));
        endif;
        return view($this->page . 'loadsheet.load_sheet', compact('from', 'to'));
    }



    public function order_vs_execution(Request $request)
    {

        if ($request->ajax()) :

            $from = $request->from;
            $to = $request->to;
            $data =   DB::table('sale_orders as a')
                ->join('sale_order_data as d', 'd.so_id', 'a.id')
                ->join('shops', 'shops.id', 'a.shop_id')
                ->join('products', 'd.product_id', 'products.id')
                ->join('routes', function ($join) use ($request) {
                    $join->on('routes.id', '=', 'shops.route_id');
                    if ($request->route_id != null)
                        $join->where('routes.id', $request->route_id);
                })
                ->join('distributors as b', 'a.distributor_id', 'b.id')
                ->join('tso as c', function ($join) use ($request) {
                    $join->on('c.id', '=', 'a.tso_id')->where('c.active', 1);
                    if ($request->city != null)
                        $join->where('c.city', $request->city);
                })
                ->join('users_distributors','c.user_id','=','users_distributors.user_id')
                ->when($request->distributor_id == null, function ($query) use ($request) {

                    $query->whereIn('users_distributors.distributor_id' ,MasterFormsHelper::get_users_distributors(Auth::user()->id));
                })
                ->join('cities', 'cities.id', 'c.city')
                ->when($request->distributor_id != null, function ($query) use ($request) {
                    $query->where('a.distributor_id', $request->distributor_id);
                })
                ->when($request->tso_id != null, function ($query) use ($request) {
                    $query->where('a.tso_id', $request->tso_id);
                })
                ->when($request->shop_id != null, function ($query) use ($request) {
                    $query->where('a.shop_id', $request->shop_id);
                })
                ->when($request->product_id != null, function ($query) use ($request) {
                    $query->where('d.product_id', $request->product_id);
                })
                ->when($request->execution != null, function ($query) use ($request) {
                    $query->where('a.excecution', $request->execution);
                })
                ->whereBetween('a.dc_date', [$from, $to])
                ->where('a.status', 1)
                ->where('c.status', 1)
                ->select(
                    'a.id',
                    'b.distributor_name',
                    'c.name as tso',
                    'c.id as tso_id',
                    'c.user_id',
                    'a.dc_date',
                    'shops.company_name as shop_name',
                    'routes.route_name',
                    'cities.name as city',
                    'products.product_name',
                    'd.total',
                    'd.qty',
                    'a.excecution',
                    'a.invoice_no'
                )
                ->orderBy('a.invoice_no', 'DESC')
                ->get();

            return view($this->page . 'OrderVSExecution.order_vs_execution_ajax', compact('data'));
        endif;

        return view($this->page . 'OrderVSExecution.order_vs_execution');
    }

  public function order_vs_execution_return(Request $request)
    {
        if ($request->ajax()) :
            $from = $request->from;
            $to = $request->to;
    
            $data = DB::table('sale_orders as a')
                ->join('sale_order_data as d', 'd.so_id', '=', 'a.id')
                ->leftJoin(
                    DB::raw('(SELECT 
                                sales_returns.so_id, 
                                sales_returns.amount AS total_return_amount, 
                               sales_return_data.qty AS total_return_qty
                              FROM sales_return_data 
                              LEFT JOIN sale_order_data AS rd ON rd.id = sales_return_data.sales_order_data_id 
                              LEFT JOIN sales_returns ON sales_return_data.sales_return_id = sales_returns.id 
                              GROUP BY sales_returns.so_id) as returns_summary'),
                    'returns_summary.so_id',
                    '=',
                    'a.id'
                )
                ->join('shops', 'shops.id', '=', 'a.shop_id')
                ->join('products', 'd.product_id', '=', 'products.id')
                ->join('routes', function ($join) use ($request) {
                    $join->on('routes.id', '=', 'shops.route_id');
                    if ($request->route_id != null) {
                        $join->where('routes.id', $request->route_id);
                    }
                })
                ->join('distributors as b', 'a.distributor_id', '=', 'b.id')
                ->join('tso as c', function ($join) use ($request) {
                    $join->on('c.id', '=', 'a.tso_id')->where('c.active', 1);
                    if ($request->city != null) {
                        $join->where('c.city', $request->city);
                    }
                })
                ->join('users_distributors', 'c.user_id', '=', 'users_distributors.user_id')
                ->when($request->distributor_id == null, function ($query) use ($request) {
                    $query->whereIn('users_distributors.distributor_id', MasterFormsHelper::get_users_distributors(Auth::user()->id));
                })
                ->join('cities', 'cities.id', '=', 'c.city')
                ->when($request->distributor_id != null, function ($query) use ($request) {
                    $query->where('a.distributor_id', $request->distributor_id);
                })
                ->when($request->tso_id != null, function ($query) use ($request) {
                    $query->where('a.tso_id', $request->tso_id);
                })
                ->when($request->shop_id != null, function ($query) use ($request) {
                    $query->where('a.shop_id', $request->shop_id);
                })
                ->when($request->product_id != null, function ($query) use ($request) {
                    $query->where('d.product_id', $request->product_id);
                })
                ->when($request->execution != null, function ($query) use ($request) {
                    $query->where('a.excecution', $request->execution);
                })
                ->whereBetween('a.dc_date', [$from, $to])
                ->where('a.status', 1)
                ->where('c.status', 1)
                ->select(
                    'a.id',
                    'b.distributor_name',
                    'c.name as tso',
                    'c.id as tso_id',
                    'c.user_id',
                    'a.dc_date',
                    'shops.company_name as shop_name',
                    'routes.route_name',
                    'cities.name as city',
                    'products.product_name',
                    'd.total',
                    'd.qty',
                    'a.excecution',
                    'a.invoice_no',
                    'returns_summary.total_return_amount',
                    'returns_summary.total_return_qty'
                )
                ->orderBy('a.invoice_no', 'DESC')
                ->get();
    
            return view($this->page . 'OrderVSExecution.order_vs_execution_return_ajax', compact('data'));
        endif;
    
        return view($this->page . 'OrderVSExecution.order_vs_execution_return');
    }


  
public function national_summary_new(Request $request)
{
    ini_set('max_execution_time', 2400);
    
    $from = $request->from;
    $to = $request->to;

    if ($request->ajax()) {
        // Generate date range array
        $dates = [];
        $current = strtotime($from);
        $last = strtotime($to);
        
        while($current <= $last) {
            $dates[] = date('Y-m-d', $current);
            $current = strtotime('+1 day', $current);
        }
        
        $monthfrom = date('m', strtotime($from));
        $monthto = date('m', strtotime($to));
        $monthYear = explode('-', $request->from);
        
        $tsosQuery = Tso::with([
            'attendence' => function ($query) use ($request) {
                $query->whereBetween(DB::raw('DATE(`in`)'), [$request->from, $request->to]);
        
                if ($request->tso_id) {
                    $query->groupBy(DB::raw('DATE(`in`)'));
                }
            },
            'designation',
            'distributor',
            'cities',
            'saleOrders' => function ($query) use ($request) {
                $query->where('status', 1);
        
                if ($request->from && $request->to) {
                    $query->whereBetween('dc_date', [$request->from, $request->to]);
                }
        
                $query->whereNotNull('dc_date');
        
                $query->whereIn('sale_orders.distributor_id', function ($subQuery) {
                    $subQuery->select('distributor_id')
                             ->from('tso')
                             ->whereColumn('tso.id', 'sale_orders.tso_id');
                });
            }
        ])
        ->whereHas('distributor', function ($q) {
            $q->where('status', 1);
        })
        ->where('status', 1)
        ->whereIn('distributor_id', $this->master->get_users_distributors(Auth::user()->id))
        ->when($request->distributor_id, function ($query) use ($request) {
            $query->where('distributor_id', $request->distributor_id);
        })
        ->when($request->tso_id, function ($query) use ($request) {
            $query->where('id', $request->tso_id);
        })
        ->when($request->designation, function ($query) use ($request) {
            $query->where('designation_id', $request->designation);
        })
        ->when($request->city, function ($query) use ($request) {
            $query->where('city', $request->city);
        });
        
        $tsos = $tsosQuery->get();
        $products = $this->master->get_all_product();

        $view = 'NationalSummary.national_item_detail_ajax_new';
        return view($this->page . $view, compact('tsos', 'from', 'to', 'monthYear', 'monthto', 'monthfrom', 'dates', 'products'));
    }

    return view($this->page . 'NationalSummary.national_summary_new');
}


     public function tso_target(Request $request)
    {

        if ($request->ajax()) :

            $monthfrom = date('m', strtotime($request->from));
            $monthto = date('m', strtotime($request->to));

            $summary = $request->summary;
            $target_type = $request->target_type;
            $tso_target = TSOTarget::leftjoin('products', 'products.id', 'tso_targets.product_id')
                ->join('tso', 'tso.id', 'tso_targets.tso_id')
                ->join('users_distributors','tso.user_id','=','users_distributors.user_id')
                ->when($request->distributor_id == null, function ($query) use ($request) {

                    $query->whereIn('users_distributors.distributor_id' ,MasterFormsHelper::get_users_distributors(Auth::user()->id));
                })
                ->join('distributors', 'distributors.id', 'tso.distributor_id')
                ->leftJoin('product_prices', function ($join) {
                    $join->on('product_prices.product_id', '=', 'products.id')
                         ->where('product_prices.status', 1); // Only fetch active prices
                });
            if (!empty($request->tso_id)) {
                $tso_target->where('tso_targets.tso_id', $request->tso_id);
            }
            if (!empty($target_type)) {
                $tso_target->where('tso_targets.type', $target_type);
            }

            if (!empty($request->distributor_id)) {
                $tso_target->where('tso.distributor_id', $request->distributor_id);
            }

            $tso_target = $tso_target
                ->whereBetween(DB::raw('MONTH(tso_targets.month)'), [$monthfrom, $monthto])

                ->select(
                    'distributors.distributor_name',
                    'tso_targets.*',
                    DB::raw('SUM(tso_targets.qty) as tso_targets_qty'),
                    'products.product_name',
                    'tso.name as tso_name',
                    'tso_targets.product_id',
                    'tso_targets.tso_id',
                    'tso_targets.qty',
                     'product_prices.trade_price as trade_price'

                )
                ->groupBy('tso_targets.product_id' , 'tso_targets.shop_type', 'tso_targets.tso_id', 'distributors.distributor_name', 'products.product_name', 'tso.name')
                ->get();
                // dd($tso_target->toArray());
            return view($this->page . 'tsotarget.tso_target_ajax', compact('tso_target', 'summary','monthfrom', 'monthto' ,'target_type'));
        endif;
        return view($this->page . 'tsotarget.tso_target');
    }

    public function racks_report(Request $request)
    {


        if ($request->ajax()) :

            $tso_id = $request->tso_id;
            $rack_id = $request->rack_id;
            $shop_id = $request->shop_id;
            $distributor_id = $request->distributor_id;

            $monthfrom = date('m', strtotime($request->from));
            $monthto = date('m', strtotime($request->to));
            // dd($monthfrom , $request->from);
            $racks_details = AssignRack::join('racks' , 'racks.id' , 'assign_racks.rack_id')
            ->join('shops' , 'shops.id' , 'assign_racks.shop_id')
            ->join('tso' , 'tso.id' , 'assign_racks.tso_id')
            ->join('users_distributors','tso.user_id','=','users_distributors.user_id')
            ->join('distributors', 'distributors.id', 'tso.distributor_id')

            ->when($distributor_id == null, function ($query) use ($request) {

                $query->whereIn('users_distributors.distributor_id' ,MasterFormsHelper::get_users_distributors(Auth::user()->id));
            })
            ->when($tso_id , function($query) use ($tso_id) {
                $query->where('assign_racks.tso_id' , $tso_id);
            })
            ->when($shop_id , function($query) use ($shop_id) {
                $query->where('assign_racks.shop_id' , $shop_id);
            })
            ->when($rack_id , function($query) use ($rack_id) {
                $query->where('assign_racks.rack_id' , $rack_id);
            })
            ->whereBetween(DB::raw('MONTH(assign_racks.created_at)'), [$monthfrom, $monthto])
            ->select('distributors.distributor_name','assign_racks.*' , 'tso.name as tso_name' , 'shops.company_name as shop_name' , 'racks.rack_code')
            ->groupBy('assign_racks.id')
            ->get();

            return view($this->page . 'rack.rack_ajax' , compact('racks_details'));
        endif;
        return view($this->page . 'rack.rack' );
    }


    public function scheme_product(Request $request)
    {
        if ($request->ajax()) :
            $from =$request->from;
            $to =$request->to;

            $data =   DB::table('sale_orders as a')
                ->join('sale_order_data as d', 'd.so_id', 'a.id')
                ->join('shops', 'shops.id', 'a.shop_id')
                ->join('products', 'd.sheme_product_id', 'products.id')
                ->join('routes', function ($join) use ($request) {
                    $join->on('routes.id', '=', 'shops.route_id');
                    if ($request->route_id != null)
                        $join->where('routes.id', $request->route_id);
                })
                ->join('distributors as b', 'a.distributor_id', 'b.id')
                ->join('tso as c', function ($join) use ($request) {
                    $join->on('c.id', '=', 'a.tso_id');
                    if ($request->city != null)
                        $join->where('c.city', $request->city);
                })

                ->join('users_distributors','c.user_id','=','users_distributors.user_id')
                ->when($request->distributor_id == null, function ($query) use ($request) {

                    $query->whereIn('users_distributors.distributor_id' ,MasterFormsHelper::get_users_distributors(Auth::user()->id));
                })
                ->join('cities', 'cities.id', 'c.city')
                ->when($request->distributor_id != null, function ($query) use ($request) {
                    $query->where('a.distributor_id', $request->distributor_id);
                })
                ->when($request->tso_id != null, function ($query) use ($request) {
                    $query->where('a.tso_id', $request->tso_id);
                })
                ->when($request->shop_id != null, function ($query) use ($request) {
                    $query->where('a.shop_id', $request->shop_id);
                })
                ->when($request->product_id != null, function ($query) use ($request) {
                    $query->where('d.product_id', $request->product_id);
                })
                ->where('d.offer_qty', '>', 0)
                ->whereBetween('a.dc_date', [$from, $to])
                ->where('a.status', 1)
                ->where('c.status', 1)
                ->select(
                    'a.id',
                    'b.distributor_name',
                    'c.name as tso',
                    'c.id as tso_id',
                    'c.user_id',
                    'a.dc_date',
                    'shops.company_name as shop_name',
                    DB::raw('SUM(d.offer_qty) as total_amount'),
                    'routes.route_name',
                    'cities.name as city',
                    'a.dc_date',
                    'products.product_name',
                    'd.offer_qty'
                )
                ->groupBy('a.shop_id')
                ->groupBy('d.sheme_product_id')
                ->orderBy('d.id', 'DESC')
                ->get();
            return view($this->page . 'ShcemeProductReport.scheme_product_ajax', compact('data'));
        endif;

        return view($this->page . 'ShcemeProductReport.scheme_product');
    }

    // public function day_wise_attendence_report(Request $request)
    // {
    //     if ($request->ajax()) :
    //         $monthYear = explode('-', $request->from_date);
    //         $from_date = $request->from_date;
    //         $to_date = $request->to_date;
    //         // dd($request->from , $monthYear);
    //        $attendences =  TSO::status()->select('id', 'name', 'tso_code', 'distributor_id', 'designation_id', 'city')
    //             ->when($request->distributor_id != null, function ($query) use ($request) {

    //                 $query->where('distributor_id', $request->distributor_id);
    //             })->when($request->tso_id != null, function ($query) use ($request) {

    //                 $query->where('id', $request->tso_id);
    //             })->when($request->designation != null, function ($query) use ($request) {

    //                 $query->where('designation_id', $request->designation);
    //             })->when($request->city != null, function ($query) use ($request) {

    //                 $query->where('city', $request->city);
    //             })->with(['designation:id,name', 'distributor:id,distributor_name', 'cities:id,name'])->get()->toArray();
    //             // dd($attendences);
    //         return view($this->page . 'attendenceReport.day_wise_attendence_report_ajax', compact('attendences', 'monthYear' , 'from_date' , 'to_date'));
    //     endif;
    //     return view($this->page . 'attendenceReport.day_wise_attendence_report');
    // }



    // public function day_wise_attendence_report(Request $request)
    // {


    //     if ($request->ajax()) :
    //         $monthYear = explode('-', $request->from_date);
    //         $from_date = $request->from_date;
    //         $to_date = $request->to_date;
    //         // dd($request->from , $monthYear);
    //        $attendences =  TSO::status()->where('active' , 1)
    //             ->join('users_distributors','users_distributors.user_id','tso.user_id')
    //             ->whereIn('users_distributors.distributor_id', $this->master->get_users_distributors(Auth::user()->id))
    //             ->select('tso.id', 'tso.emp_id','tso.name', 'tso.tso_code','tso.cnic', 'users_distributors.distributor_id', 'tso.designation_id', 'tso.city')
    //             ->when($request->distributor_id != null, function ($query) use ($request) {

    //                 $query->where('users_distributors.distributor_id', $request->distributor_id);
    //             })->when($request->tso_id != null, function ($query) use ($request) {

    //                 $query->where('tso.id', $request->tso_id);
    //             })->when($request->designation != null, function ($query) use ($request) {

    //                 $query->where('tso.designation_id', $request->designation);
    //             })->when($request->city != null, function ($query) use ($request) {

    //                 $query->where('tso.city', $request->city);
    //             })->with(['designation:id,name', 'distributor:id,distributor_name', 'cities:id,name'])->get()->toArray();

    //         return view($this->page . 'attendenceReport.day_wise_attendence_report_ajax', compact('attendences', 'monthYear' , 'from_date' , 'to_date'));
    //     endif;
    //     return view($this->page . 'attendenceReport.day_wise_attendence_report');
    // }

public function day_wise_attendence_report(Request $request)
{
    if ($request->ajax()) {

        // --------------------- dates ---------------------
        $monthYear = explode('-', $request->from_date);
        $from_date = $request->from_date;
        $to_date   = $request->to_date;

        // ---------------- distributor filter -------------
        // 1. user‑selected IDs (may be empty)
        $requestedDistributorIds = array_filter(
            (array) $request->input('distributor_id', [])
        );

        // 2. IDs allowed for current user
        $allowedDistributorIdsRaw = $this->master->get_users_distributors(Auth::id());
        $allowedDistributorIds    = is_array($allowedDistributorIdsRaw)
            ? $allowedDistributorIdsRaw
            : (array) json_decode(json_encode($allowedDistributorIdsRaw), true);

        // 3. final list: either intersection or full allowed list
        $finalDistributorIds = $requestedDistributorIds
            ? array_intersect($allowedDistributorIds, $requestedDistributorIds)
            : $allowedDistributorIds;

        // ---------------- main query ---------------------
        $attendences = TSO::status()
            ->where('active', 1)
            ->join('users_distributors', 'users_distributors.user_id', '=', 'tso.user_id')
            ->whereIn('users_distributors.distributor_id', $finalDistributorIds)   // 👈 array‑based filter
            ->select(
                'tso.id',
                'tso.emp_id',
                'tso.name',
                'tso.tso_code',
                'tso.cnic',
                'users_distributors.distributor_id',
                'tso.designation_id',
                'tso.city'
            )
            ->when($request->tso_id, function ($q) use ($request) {
                $q->where('tso.id', $request->tso_id);
            })
            ->when($request->designation, function ($q) use ($request) {
                $q->where('tso.designation_id', $request->designation);
            })
            ->when($request->city, function ($q) use ($request) {
                $q->where('tso.city', $request->city);
            })
            ->with([
                'designation:id,name',
                'distributor:id,distributor_name',
                'cities:id,name',
            ])
            ->get()
            ->toArray();

        // --------------- return view ---------------------
        return view(
            $this->page . 'attendenceReport.day_wise_attendence_report_ajax',
            compact('attendences', 'monthYear', 'from_date', 'to_date')
        );
    }

    // non‑AJAX request
    return view($this->page . 'attendenceReport.day_wise_attendence_report');
}


    public function attendence_report(Request $request)
    {
        if ($request->ajax()) :
            $monthYear = explode('-', $request->from);
            // dd($request->from , $monthYear);
           $attendences =  TSO::status()->select('id', 'name', 'tso_code', 'distributor_id', 'designation_id', 'city')
                ->when($request->distributor_id != null, function ($query) use ($request) {

                    $query->where('distributor_id', $request->distributor_id);
                })->when($request->tso_id != null, function ($query) use ($request) {

                    $query->where('id', $request->tso_id);
                })->when($request->designation != null, function ($query) use ($request) {

                    $query->where('designation_id', $request->designation);
                })->when($request->city != null, function ($query) use ($request) {

                    $query->where('city', $request->city);
                })->with(['designation:id,name', 'distributor:id,distributor_name', 'cities:id,name'])->get()->toArray();

            return view($this->page . 'attendenceReport.attendence_report_ajax', compact('attendences', 'monthYear'));
        endif;
        return view($this->page . 'attendenceReport.attendence_report');
    }


    public function daily_booking_unit_summary(Request $request)
    {
        if ($request->ajax()) :

            $from = $request->from;
            $to = $request->to;

        $data = DB::table('sale_orders as a')
        ->join('sale_order_data as d', 'd.so_id', 'a.id')
        ->join('shops', 'shops.id', 'a.shop_id')
        ->join('routes', function ($join) use ($request) {
            $join->on('routes.id', '=', 'shops.route_id');
            if ($request->route_id != null)
                $join->where('routes.id', $request->route_id);
        })
        ->join('distributors as b', 'a.distributor_id', 'b.id')
        ->join('tso as c', function ($join) use ($request) {
            $join->on('c.id', '=', 'a.tso_id')->where('c.active', 1);
            if ($request->city != null)
                $join->where('c.city', $request->city);
        })
        ->leftJoin('users', 'users.id', '=', 'c.manager') // manager name join
        ->join('users_distributors', 'c.user_id', '=', 'users_distributors.user_id')
        ->when($request->distributor_id == null, function ($query) use ($request) {
            $query->whereIn('users_distributors.distributor_id', MasterFormsHelper::get_users_distributors(Auth::user()->id));
        })
        ->join('cities', 'cities.id', 'c.city')
        ->when($request->distributor_id != null, function ($query) use ($request) {
            $query->where('a.distributor_id', $request->distributor_id);
        })
        ->when($request->tso_id != null, function ($query) use ($request) {
            $query->where('a.tso_id', $request->tso_id);
        })
        // ->when($request->route_id != null, function ($query) use ($request) {
            // $query->where('a.route_id', $request->route_id);
        // })
        ->when($request->shop_id != null, function ($query) use ($request) {
            $query->where('a.shop_id', $request->shop_id);
        })
        ->whereBetween('a.dc_date', [$from, $to])
        ->where('a.status', 1)
        ->where('c.status', 1)
        ->select(
            DB::raw("MIN(a.invoice_no) as invoice_no"),
            DB::raw("MIN(a.dc_date) as dc_date"),
            'shops.company_name as shop_name',
            'routes.route_name',
            'c.name as tso',
            'b.distributor_name',
            'users.name as manager_name', // manager name select
            DB::raw("SUM(d.qty) as total_qty"),
            DB::raw("SUM(d.total) as total_amount")
        )
        ->groupBy('shops.company_name', 'routes.route_name', 'c.name', 'b.distributor_name', 'users.name')
        ->orderBy('shops.company_name', 'ASC')
        ->get();


            return view(
                $this->page . 'DailyBookingUnitSummary.daily_booking_unit_summary_ajax',
                [
                    'data' => $data,
                    'from' => $from,
                    'to' => $to,
                    'city' => $request->city,
                    'distributor_id' => $request->distributor_id,
                    'tso_id' => $request->tso_id
                ]
            );
        endif;

        return view($this->page . 'DailyBookingUnitSummary.daily_booking_unit_summary');
    }


    
public function distributer_product_sales_value_report_old(Request $request)
{
    $from = $request->from;
    $to = $request->to;

    if ($request->ajax()) {
        $query = DB::table('sale_orders')
            ->join('sale_order_data', 'sale_orders.id', '=', 'sale_order_data.so_id')
            ->join('products', 'products.id', '=', 'sale_order_data.product_id')
            ->join('tso', 'tso.id', '=', 'sale_orders.tso_id')
            ->whereBetween('sale_orders.dc_date', [$from, $to])
            ->where('sale_orders.status', 1)
            // ->where('sale_orders.excecution', 1)
            ->where('sale_order_data.status', 1);
            // ->where('tso.status', 1)

        // Apply filters
        if ($request->distributor_id) {
            $query->where('sale_orders.distributor_id', $request->distributor_id);
        }

        if ($request->tso_id) {
            $query->where('sale_orders.tso_id', $request->tso_id);
        }

        if ($request->designation) {
            $query->where('sale_orders.designation_id', $request->designation);
        }

        if ($request->city) {
            $query->when($request->city, function ($query) use ($request) {
                $query->where('tso.city', $request->city);
            });
        }

        // ✅ Route filter (via shops table)
        if ($request->route_id) {
            $query->whereIn('sale_orders.shop_id', function ($subQuery) use ($request) {
                $subQuery->select('id')
                    ->from('shops')
                    ->where('route_id', $request->route_id);
            });
        }

        // ✅ Shop filter
        if ($request->shop_id) {
            $query->where('sale_orders.shop_id', $request->shop_id);
        }

        $city           = $request->city;
        $distributor_id = $request->distributor_id;
        $tso_id         = $request->tso_id;
        $route_id       = $request->route_id;
        $shop_id        = $request->shop_id;


        // Group by product and get sales data
        $productSales = $query
            ->select(
                DB::raw("'LAZIZA' as brand"),
                'products.product_name',
                DB::raw('SUM(sale_order_data.qty) as qty'),
                DB::raw('SUM(sale_order_data.total) as sales_value')
            )
            ->groupBy('sale_order_data.product_id', 'sale_order_data.flavour_id')
            ->groupBy('sale_order_data.product_id')
            ->orderBy('products.orderby', 'ASC')
            ->get();

        return view($this->page . 'DistributorProductSalesValueReport.distributor_poduct_sales_value_report_ajax', compact('productSales', 'from', 'to','city','distributor_id','tso_id'));
    }

    return view($this->page . 'DistributorProductSalesValueReport.distributor_poduct_sales_value_report');
}
public function distributer_product_sales_value_report(Request $request)
{
    $from = $request->from;
    $to = $request->to;
    $distributor_id = $request->distributor_id;
    $tso_id = $request->tso_id;
    $city = $request->city;
    $route_id       = $request->route_id;
    $shop_id        = $request->shop_id;

    if ($request->ajax()) {
        $productSales =   DB::table('sale_orders as a')
            ->join('sale_order_data as b', 'b.so_id', 'a.id')
            ->join('tso as c', 'c.id', 'a.tso_id')
            ->join('products as d', 'd.id', 'b.product_id')
            ->join('cities as e', 'e.id', 'c.city')
            ->join('distributors as f', 'f.id', '=', 'a.distributor_id')
            ->join('shops as s', 's.id', '=', 'a.shop_id')
            ->whereBetween('a.dc_date', [$from, $to])

            ->when($request->distributor_id != null, function ($query) use ($request) {
                $query->where('a.distributor_id', $request->distributor_id);
            })
            ->when($request->tso_id != null, function ($query) use ($request) {

                $query->where('a.tso_id', $request->tso_id);
            })
            ->when($request->shop_id != null, function ($query) use ($request) {
                return $query->where('shop_id', $request->shop_id);
            })
            ->when($request->city != null, function ($query) use ($request) {
                $query->where('e.id', $request->city);
            })
            ->when($route_id, fn($q) => $q->where('s.route_id', $route_id))
            ->when($request->product_id != null, function ($query) use ($request) {
                $query->where('d.id', $request->product_id);
            })
            ->where('a.status', 1)
            ->where('c.status', 1)
            ->select('c.name', 'c.cnic', 'f.distributor_name as distributor_name', 'a.tso_id', 'd.product_name', 'd.carton_size', 'b.product_id', 'b.flavour_id', DB::raw('sum(b.total) as sales_value'), DB::raw('sum(b.rate) as rate'), DB::raw('sum(b.discount_amount) as discount_amount'), DB::raw('sum(b.qty) as qty'), 'e.name as city_name')
            ->groupBy('b.product_id', 'b.flavour_id')
            ->groupBy('b.product_id')
            // ->orderBy('a.tso_id')
            ->orderBy('d.orderby', 'ASC')
            ->get();

        return view($this->page . 'DistributorProductSalesValueReport.distributor_poduct_sales_value_report_ajax', compact('productSales', 'from', 'to','city','distributor_id','tso_id'));
    }

    return view($this->page . 'DistributorProductSalesValueReport.distributor_poduct_sales_value_report');
}


 
    public function booking_vs_execution(Request $request)
    {
         if ($request->ajax()) :

            $from = $request->from;
            $to = $request->to;
            $city           = $request->city;
            $distributor_id = $request->distributor_id;
            $tso_id         = $request->tso_id;
            $route_id       = $request->route_id;
            $shop_id        = $request->shop_id;

            $data =   DB::table('sale_orders as a')
                    ->join('sale_order_data as d', 'd.so_id', 'a.id')
                    ->join('shops', 'shops.id', 'a.shop_id')
                    ->join('products', 'd.product_id', 'products.id')
                    ->join('routes', function ($join) use ($request) {
                        $join->on('routes.id', '=', 'shops.route_id');
                        if ($request->route_id != null)
                            $join->where('routes.id', $request->route_id);
                    })
                    ->join('distributors as b', 'a.distributor_id', 'b.id')
                    ->join('tso as c', function ($join) use ($request) {
                        $join->on('c.id', '=', 'a.tso_id')->where('c.active', 1);
                        if ($request->city != null)
                            $join->where('c.city', $request->city);
                    })
                   ->leftJoin('users_distributors', 'c.user_id', '=', 'users_distributors.user_id')
                  ->when($request->distributor_id == null, function ($query) use ($request) {
    $query->whereIn('a.distributor_id', MasterFormsHelper::get_users_distributors(Auth::user()->id));
})
                    ->join('cities', 'cities.id', 'c.city')
                    ->when($request->distributor_id != null, function ($query) use ($request) {
                        $query->where('a.distributor_id', $request->distributor_id);
                    })
                    ->when($request->tso_id != null, function ($query) use ($request) {
                        $query->where('a.tso_id', $request->tso_id);
                    })
                    ->when($request->shop_id != null, function ($query) use ($request) {
                        $query->where('a.shop_id', $request->shop_id);
                    })
                    ->when($request->product_id != null, function ($query) use ($request) {
                        $query->where('d.product_id', $request->product_id);
                    })
                    ->when($request->execution != null, function ($query) use ($request) {
                        $query->where('a.excecution', $request->execution);
                    })
                    ->whereBetween('a.dc_date', [$from, $to])
                    ->where('a.status', 1)
                    ->where('c.status', 1)
                  ->select(
    'a.id',
    'b.distributor_name',
    'c.name as tso',
    'c.id as tso_id',
    'c.user_id',
    'a.dc_date',
    'shops.company_name as shop_name',
    'routes.route_name',
    'cities.name as city',
    'products.product_name',
    'd.qty as booking_qty',
    DB::raw("CASE WHEN a.excecution = 1 THEN d.qty ELSE 0 END as execution_qty"),
    'a.excecution',
    'a.invoice_no'
)
->distinct()
                    ->orderBy('products.orderby', 'ASC')
                    ->orderBy('a.invoice_no', 'ASC')
                    ->get();

            return view($this->page . 'OrderVSExecution.booking_vs_execution_ajax', compact('data', 'from', 'to', 'city', 'distributor_id', 'tso_id', 'route_id', 'shop_id'));
        endif;

        return view($this->page . 'OrderVSExecution.booking_vs_execution');
    

    }
 
    public function order_vs_execution_product_wise(Request $request)
    {
         if ($request->ajax()) :

            $from = $request->from;
            $to = $request->to;
            $city           = $request->city;
            $distributor_id = $request->distributor_id;
            $tso_id         = $request->tso_id;
            $route_id       = $request->route_id;
            $shop_id        = $request->shop_id;

            $data = DB::table('sale_orders as a')
                    ->join('sale_order_data as d', 'd.so_id', 'a.id')
                    ->join('shops', 'shops.id', 'a.shop_id')
                    ->join('products', 'd.product_id', 'products.id')
                    ->join('routes', function ($join) use ($request) {
                        $join->on('routes.id', '=', 'shops.route_id');
                        if ($request->route_id != null)
                            $join->where('routes.id', $request->route_id);
                    })
                    ->join('distributors as b', 'a.distributor_id', 'b.id')
                    ->join('tso as c', function ($join) use ($request) {
                        $join->on('c.id', '=', 'a.tso_id')->where('c.active', 1);
                        if ($request->city != null)
                            $join->where('c.city', $request->city);
                    })
                    ->join('users_distributors','c.user_id','=','users_distributors.user_id')
                    ->when($request->distributor_id == null, function ($query) use ($request) {

                        $query->whereIn('users_distributors.distributor_id' ,MasterFormsHelper::get_users_distributors(Auth::user()->id));
                    })
                    ->join('cities', 'cities.id', 'c.city')
                    ->when($request->distributor_id != null, function ($query) use ($request) {
                        $query->where('a.distributor_id', $request->distributor_id);
                    })
                    ->when($request->tso_id != null, function ($query) use ($request) {
                        $query->where('a.tso_id', $request->tso_id);
                    })
                    ->when($request->shop_id != null, function ($query) use ($request) {
                        $query->where('a.shop_id', $request->shop_id);
                    })
                    ->when($request->product_id != null, function ($query) use ($request) {
                        $query->where('d.product_id', $request->product_id);
                    })
                    ->when($request->execution != null, function ($query) use ($request) {
                        $query->where('a.excecution', $request->execution);
                    })
                    ->whereBetween('a.dc_date', [$from, $to])
                    ->where('a.status', 1)
                    ->where('c.status', 1)
                    // ->select(
                    //     'a.id',
                    //     'b.distributor_name',
                    //     'c.name as tso',
                    //     'c.id as tso_id',
                    //     'c.user_id',
                    //     'a.dc_date',
                    //     'shops.company_name as shop_name',
                    //     'routes.route_name',
                    //     'cities.name as city',
                    //     'products.carton_size as packing_size',
                    //     'products.product_name',
                    //     'd.qty as order_qty',
                    //     DB::raw("CASE WHEN a.excecution = 1 THEN d.qty ELSE 0 END as execution_qty"),
                    //     'a.excecution',
                    //     'a.invoice_no'
                    // )
                    // ->groupBy('products.product_name')
                    // ->orderBy('products.orderby', 'ASC')
                    // ->orderBy('a.invoice_no', 'ASC')
                    // ->get();

                  ->select(
                        'products.product_name',
                        'products.carton_size as packing_size',
                        DB::raw("SUM(d.qty) as order_qty"),
                        DB::raw("SUM(CASE WHEN a.excecution = 1 THEN d.qty ELSE 0 END) as execution_qty")
                    )
                    ->whereBetween('a.dc_date', [$from, $to])
                    ->where('a.status', 1)
                    ->where('c.status', 1)
                    ->groupBy(
                        'products.product_name'
                    )
                    ->orderBy('products.orderby', 'ASC')
                    ->get();



            return view($this->page . 'OrderVSExecution.order_vs_execution_product_wise_ajax', compact('data', 'from', 'to', 'city', 'distributor_id', 'tso_id', 'route_id', 'shop_id'));
        endif;

        return view($this->page . 'OrderVSExecution.order_vs_execution_product_wise');


    }


    public function non_dispatch_order_report(Request $request)
    {
        if ($request->ajax()) :

            $from = $request->from;
            $to = $request->to;

        $data = DB::table('sale_orders as a')
            ->join('sale_order_data as d', 'd.so_id', 'a.id')
            ->join('products as p', 'p.id', '=', 'd.product_id') 
            ->join('shops', 'shops.id', 'a.shop_id')
            ->join('routes', function ($join) use ($request) {
                $join->on('routes.id', '=', 'shops.route_id');
                if ($request->route_id != null)
                    $join->where('routes.id', $request->route_id);
            })
            ->join('distributors as b', 'a.distributor_id', 'b.id')
            ->join('tso as c', function ($join) use ($request) {
                $join->on('c.id', '=', 'a.tso_id')->where('c.active', 1);
                if ($request->city != null)
                    $join->where('c.city', $request->city);
            })
            ->leftJoin('users', 'users.id', '=', 'c.manager') // manager name join
            ->join('users_distributors', 'c.user_id', '=', 'users_distributors.user_id')
            ->when($request->distributor_id == null, function ($query) use ($request) {
                $query->whereIn('users_distributors.distributor_id', MasterFormsHelper::get_users_distributors(Auth::user()->id));
            })
            ->join('cities', 'cities.id', 'c.city')
            ->when($request->distributor_id != null, function ($query) use ($request) {
                $query->where('a.distributor_id', $request->distributor_id);
            })
            ->when($request->tso_id != null, function ($query) use ($request) {
                $query->where('a.tso_id', $request->tso_id);
            })
            ->when($request->shop_id != null, function ($query) use ($request) {
                $query->where('a.shop_id', $request->shop_id);
            })
            ->whereBetween('a.dc_date', [$from, $to])
            ->where('a.status', 1)
            ->where('c.status', 1)
            ->where('a.excecution', 0)
            ->select(
                DB::raw("MIN(a.invoice_no) as invoice_no"),
                DB::raw("MIN(a.dc_date) as dc_date"),
                'shops.company_name as shop_name',
                'routes.route_name',
                'c.name as tso',
                'b.distributor_name',
                'users.name as manager_name', 
                DB::raw("SUM(d.qty) as total_qty"),
                DB::raw("SUM(d.total) as total_amount"),
                DB::raw("GROUP_CONCAT(p.product_name SEPARATOR ', ') as product") 
            )
            ->groupBy('shops.company_name', 'routes.route_name', 'c.name', 'b.distributor_name', 'users.name', 'p.id')
            // ->orderBy('shops.company_name', 'ASC') // Uncomment if you want to order by shop name
            ->orderBy('p.orderby', 'ASC')
            ->get();
            // dd($data);

            return view(
                $this->page . 'NonDispatchOrderReport.non_dispatch_order_report_ajax',
                [
                    'data' => $data,
                    'from' => $from,
                    'to' => $to,
                    'city' => $request->city,
                    'distributor_id' => $request->distributor_id,
                    'tso_id' => $request->tso_id
                ]
            );
        endif;

        return view($this->page . 'NonDispatchOrderReport.non_dispatch_order_report');
    }


    public function product_wise_sale(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $distributor_id = $request->distributor_id;
        $tso_id = $request->tso_id;
        $city = $request->city;
        $route_id       = $request->route_id;
        $shop_id        = $request->shop_id;
 
        if ($request->ajax()) :
            // if ($request->type == 'Detail') {
 
 
            $data =   DB::table('sale_orders as a')
                ->join('sale_order_data as b', 'b.so_id', 'a.id')
                ->join('tso as c', 'c.id', 'a.tso_id')
                ->join('products as d', 'd.id', 'b.product_id')
                ->join('cities as e', 'e.id', 'c.city')
                ->join('distributors as f', 'f.id', '=', 'a.distributor_id') // Join with distributors table
                ->join('shops as s', 's.id', '=', 'a.shop_id')
                ->whereBetween('a.dc_date', [$from, $to])

                ->when($request->distributor_id != null, function ($query) use ($request) {
                    $query->where('a.distributor_id', $request->distributor_id);
                })
                ->when($request->tso_id != null, function ($query) use ($request) {
 
                    $query->where('a.tso_id', $request->tso_id);
                })
                ->when($request->shop_id != null, function ($query) use ($request) {
                    return $query->where('shop_id', $request->shop_id);
                })
                ->when($request->city != null, function ($query) use ($request) {
                    $query->where('e.id', $request->city);
                })
                ->when($route_id, fn($q) => $q->where('s.route_id', $route_id))
                ->when($request->product_id != null, function ($query) use ($request) {
                    $query->where('d.id', $request->product_id);
                })
                ->where('a.status', 1)
                ->where('c.status', 1)
                ->select('c.name', 'c.cnic', 'f.distributor_name as distributor_name', 'a.tso_id', 'd.product_name', 'd.carton_size', 'b.product_id', 'b.flavour_id', DB::raw('sum(b.total) as total'), DB::raw('sum(b.rate) as rate'), DB::raw('sum(b.discount_amount) as discount_amount'), DB::raw('sum(b.qty) as qty'), 'e.name as city_name')
                ->groupBy('b.product_id', 'b.flavour_id')
                ->groupBy('b.product_id')
                // ->orderBy('a.tso_id')
                ->orderBy('d.orderby', 'ASC')
                ->get();
            return view($this->page . 'productWiseSales.product_wise_list_ajax', compact('data', 'from', 'to', 'distributor_id', 'tso_id', 'city'));
       
        endif;
        return view($this->page . 'productWiseSales.product_wise_list');
    }

  public function cancelled_orders_report(Request $request)
    {
        $tso_id = $request->tso_id;
        $distributor_id = $request->distributor_id;
        $city = $request->city;
        if ($request->ajax()) :
 
            $from = $request->from;
            $to = $request->to;
            $data =   DB::table('sale_orders as a')
                ->join('sale_order_data as d', 'd.so_id', 'a.id')
                ->join('shops', 'shops.id', 'a.shop_id')
                ->join('products', 'd.product_id', 'products.id')
                ->join('routes', function ($join) use ($request) {
                    $join->on('routes.id', '=', 'shops.route_id');
                    if ($request->route_id != null)
                        $join->where('routes.id', $request->route_id);
                })
                ->join('distributors as b', 'a.distributor_id', 'b.id')
                ->join('tso as c', function ($join) use ($request) {
                    $join->on('c.id', '=', 'a.tso_id')->where('c.active', 1);
                    if ($request->city != null)
                        $join->where('c.city', $request->city);
                })
                ->leftJoin('users', 'users.id', '=', 'c.manager')
                ->leftJoin('users_distributors', 'c.user_id', '=', 'users_distributors.user_id')
                ->when($request->distributor_id == null, function ($query) use ($request) {
                    $query->whereIn('users_distributors.distributor_id', MasterFormsHelper::get_users_distributors(Auth::user()->id));
                })
                ->join('cities', 'cities.id', 'c.city')
                ->when($request->distributor_id != null, function ($query) use ($request) {
                    $query->where('a.distributor_id', $request->distributor_id);
                })
                ->when($request->tso_id != null, function ($query) use ($request) {
                    $query->where('a.tso_id', $request->tso_id);
                })
                ->when($request->shop_id != null, function ($query) use ($request) {
                    $query->where('a.shop_id', $request->shop_id);
                })
                ->when($request->product_id != null, function ($query) use ($request) {
                    $query->where('d.product_id', $request->product_id);
                })
                ->when($request->execution != null, function ($query) use ($request) {
                    $query->where('a.excecution', $request->execution);
                })
                ->whereBetween('a.dc_date', [$from, $to])
                ->where('a.status', 0)
                ->where('c.status', 1)
                ->select(
                    'a.id',
                    'b.distributor_name',
                    'c.name as tso',
                    'c.id as tso_id',
                    'c.user_id',
                    'a.dc_date',
                    'shops.company_name as shop_name',
                    'users.name as user_name',
                    'routes.route_name',
                    'cities.name as city',
                    'products.product_name',
                    'd.total',
                    'd.qty',
                    'a.excecution',
                    'a.invoice_no'
                )
                ->orderBy('a.invoice_no', 'DESC')
                ->orderBy('products.orderby', 'ASC')
                ->get();
 
                // dd($data);
            return view($this->page . 'cancelledOrders.cancelled_orders_ajax', compact('data', 'from', 'to', 'distributor_id', 'tso_id', 'city'));
        endif;
        return view($this->page . 'cancelledOrders.cancelled_orders');
    }
    
    public function app_version_report(Request $request)
    {
        if ($request->ajax()) :

            $from_date = $request->from_date;
            $to_date = $request->to_date;

            $attendences = TSO::status()->where('active', 1)
                ->join('users_distributors', 'users_distributors.user_id', 'tso.user_id')
                ->join('user_versions', function ($join) use ($from_date, $to_date) {
                    $join->on('user_versions.user_id', '=', 'tso.user_id')
                        ->whereBetween('user_versions.created_at', [
                            Carbon::parse($from_date)->startOfDay(),
                            Carbon::parse($to_date)->endOfDay()
                        ]);
                })
                ->whereIn('users_distributors.distributor_id', $this->master->get_users_distributors(Auth::user()->id))
                
                // ✅ Route filter (via shops + sale_orders)
                ->when($request->route_id, function ($query) use ($request, $from_date, $to_date) {
                    $query->whereExists(function ($subQuery) use ($request, $from_date, $to_date) {
                        $subQuery->select(DB::raw(1))
                            ->from('shops')
                            ->join('sale_orders', 'sale_orders.shop_id', '=', 'shops.id')
                            ->whereRaw('sale_orders.tso_id = tso.id')
                            ->where('shops.route_id', $request->route_id)
                            ->whereBetween('sale_orders.dc_date', [$from_date, $to_date]);
                    });
                })
                
                // ✅ Shop filter (via sale_orders)
                ->when($request->shop_id, function ($query) use ($request, $from_date, $to_date) {
                    $query->whereExists(function ($subQuery) use ($request, $from_date, $to_date) {
                        $subQuery->select(DB::raw(1))
                            ->from('sale_orders')
                            ->whereRaw('sale_orders.tso_id = tso.id')
                            ->where('sale_orders.shop_id', $request->shop_id)
                            ->whereBetween('sale_orders.dc_date', [$from_date, $to_date]);
                    });
                })
                ->select(
                    'tso.id',
                    'tso.emp_id',
                    'tso.name',
                    'tso.tso_code',
                    'tso.cnic',
                    'users_distributors.distributor_id',
                    'tso.designation_id',
                    'tso.city',
                    'user_versions.app_version as user_version',
                    'user_versions.updated_at as updated_at'
                )

                ->when($request->distributor_id != null, function ($query) use ($request) {
                    $query->where('users_distributors.distributor_id', $request->distributor_id);
                })
                ->when($request->tso_id != null, function ($query) use ($request) {
                    $query->where('tso.id', $request->tso_id);
                })
                ->when($request->designation != null, function ($query) use ($request) {
                    $query->where('tso.designation_id', $request->designation);
                })
                ->when($request->city != null, function ($query) use ($request) {
                    $query->where('tso.city', $request->city);
                })
                ->with(['designation:id,name', 'distributor:id,distributor_name', 'cities:id,name'])
                ->get()
                ->toArray();

            return view($this->page . 'attendenceReport.app_version_ajax', compact('attendences', 'from_date', 'to_date'));

        endif;

        return view($this->page . 'attendenceReport.app_version');
    }

    public function attendence_report_detail(Request $request)
    {
        // dd($request->all());
        $tsoName = TSO::find($request->id)->name ?? '';
        $attendences = Attendence::where('tso_id', $request->id);
        // ->where('distributor_id', $request->distributor_id);
        if ($request->date) {
            $attendences = $attendences->whereMonth('in', '=', $request->date[1])
                ->whereYear('in', '=', $request->date[0]);
        }
        else if($request->from_date && $request->to_date ){
            $from = Carbon::parse($request->from_date);
            $to = Carbon::parse($request->to_date);
            $attendences = $attendences->whereBetween('in', [$from, $to]);
        }

        $attendences = $attendences->get();
        return view($this->page . 'attendenceReport.attendence_report_detail', compact('attendences', 'tsoName'));
    }


    public function item_wise_sale(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $distributor_id = $request->distributor_id;
        $tso_id = $request->tso_id;
        $city = $request->city;

        if ($request->ajax()) :
            if ($request->type == 'Detail') {
                $data =   DB::table('sale_orders as a')
                ->join('sale_order_data as b', 'b.so_id', 'a.id')
                ->join('tso as c', 'c.id' , 'a.tso_id')
                ->join('products as d', 'd.id' , 'b.product_id')
                ->join('cities as e', 'e.id' , 'c.city')
                ->join('distributors as f', 'f.id', '=', 'a.distributor_id') // Join with distributors table
                ->whereBetween('a.dc_date', [$from, $to])
                ->when($request->distributor_id != null, function ($query) use ($request) {

                    $query->where('a.distributor_id', $request->distributor_id);
                })
                ->when($request->tso_id != null, function ($query) use ($request) {

                    $query->where('a.tso_id', $request->tso_id);
                })
                ->when($request->city != null, function ($query) use ($request) {

                    $query->where('e.id', $request->city);
                })
                ->when($request->product_id != null, function ($query) use ($request) {

                    $query->where('d.id', $request->product_id);
                })
                ->where('a.status',1)
                ->where('c.status',1)
                ->select('c.name','c.cnic', 'f.distributor_name as distributor_name', 'f.id as distributor_id','a.tso_id','d.product_name','b.product_id','b.flavour_id',DB::raw('sum(b.total) as total'),DB::raw('sum(b.qty) as qty'),'e.name as city_name')
                ->groupBy('a.tso_id','b.product_id','b.flavour_id')
                // ->groupBy('a.tso_id','b.product_id')
                ->orderBy('a.tso_id')
                ->get();

                return view($this->page . 'itemWiseSales.Item_wise_list_ajax',compact('data','from','to','distributor_id','tso_id','city'));
            }
            else{
                $data = DB::table('products')
                ->join('product_flavours', 'product_flavours.product_id', '=', 'products.id')
                ->join('product_prices', 'product_prices.product_id', '=', 'products.id')
                ->where('products.status', 1)
                ->select(
                    'products.*', 
                    'product_flavours.*', 
                    'product_flavours.id as flavour_id',
                    'product_prices.uom_id',
                    DB::raw('SUM(product_prices.trade_price) as total')
                )
  	->when($request->product_id != null, function ($query) use ($request) {

                    $query->where('products.id', $request->product_id);
                })
                ->groupBy(
                    'products.id',
                    'product_flavours.id',
                    'product_flavours.product_id',
                    'products.product_name',  
                    'products.status'  
                )
                ->get();

		$sales_return = SalesReturn::query()
        ->when($request->city, function ($query) use ($request) {
            $query->whereHas('salesorder.tso.cities', function ($cityQuery) use ($request) {
                $cityQuery->where('id', $request->city);
            });
        })
        ->when($request->distributor_id, function ($query) use ($request) {
            $query->whereHas('salesorder', function ($orderQuery) use ($request) {
                $orderQuery->where('distributor_id', $request->distributor_id);
            });
        })
        ->when($request->tso_id, function ($query) use ($request) {
            $query->whereHas('salesorder.tso', function ($tsoQuery) use ($request) {
                $tsoQuery->where('id', $request->tso_id);
            });
        })
	->when($request->product_id, function ($query) use ($request) {
            $query->whereHas('salesorder.saleOrderData', function ($orderQuery) use ($request) {
                $orderQuery->where('product_id', $request->product_id);
            });
        })
        ->when($request->from && $request->to, function ($query) use ($request) {
            $query->whereHas('salesorder', function ($orderQuery) use ($request) {
                $orderQuery->whereBetween('dc_date', [$request->from, $request->to])
		->where('excecution',1);
            });
        })->where('excute',1)
        ;




                return view($this->page . 'itemWiseSales.Item_wise_summary_list_ajax',compact('data','from','to','distributor_id','city','sales_return'));
            }
        endif;
        return view($this->page . 'itemWiseSales.Item_wise_list');
    }


    public function stock_report(Request $request)
    {
        if ($request->ajax()) :

            $result = Product::join('stocks','stocks.product_id','products.id')
            ->leftJoin('distributors', 'distributors.id', '=', 'stocks.distributor_id')
           ->select(
                    DB::raw('SUM(CASE WHEN stock_type = 0 THEN stocks.qty ELSE 0 END) AS purchase_qty'),
                    DB::raw('SUM(CASE WHEN stock_type = 1 THEN stocks.qty ELSE 0 END) AS opening_qty'),
                    DB::raw('SUM(CASE WHEN stock_type = 2 THEN stocks.qty ELSE 0 END) AS transfer_received_qty'),
                    DB::raw('SUM(CASE WHEN stock_type = 3 THEN stocks.qty ELSE 0 END) AS sales_qty'),
                    DB::raw('SUM(CASE WHEN stock_type = 4 THEN stocks.qty ELSE 0 END) AS sales_return_qty'),
                    DB::raw('SUM(CASE WHEN stock_type = 5 THEN stocks.qty ELSE 0 END) AS transfer_qty'),
                   'products.product_name','products.packing_size','distributors.max_discount','products.sales_price',
                   'products.id as product_id','stocks.flavour_id','distributors.id as distributor_id'
            );
            // $result = Product::join('stocks','stocks.product_id','products.id')
            // ->leftJoin('distributors', 'distributors.id', '=', 'stocks.distributor_id')
            // ->select('products.product_name','products.packing_size','distributors.max_discount','products.sales_price');
                if(!empty($request->distributor_id))
                {
                    $result->where('distributor_id',$request->distributor_id);
                }
 		if(!empty($request->from))
                {
                    $result->where('voucher_date','>=',$request->from);
                }
                if(!empty($request->to))
                {
                    $result->where('voucher_date','<=',$request->to);
                }

                $result = $result->where('products.status',1)
                ->where('stocks.status',1)
                // ->where('products.product_type_id',1)
                ->groupBy('products.id','stocks.flavour_id')
                ->get();
                // dd($result->toArray());
                return view($this->page . 'stock.stock_report_Ajax',compact('result'));
        endif;

        return view($this->page . 'stock.stock_report');
    }

    public function top_tso_report(Request $request , $id){
        // dd($request);
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        $tso = TSO::find($id);
        $sales =SaleOrder::
        // join('tso','tso.id','sale_orders.tso_id')
        // ->join('sale_order_data', 'sale_order_data.so_id', '=', 'sale_orders.id')
        // ->where('tso.status',1)
        where('status',1)
        ->where('tso_id' , $id)
        ->whereBetween('dc_date', [$currentMonthStart, $currentMonthEnd])
        // ->where('excecution' , 1)
        ->orderby('dc_date' , 'desc')
        ->get();

        return view($this->page . 'dashboard.top_tso_report' , compact('sales' , 'tso'));
    }

    public function top_distributor_report(Request $request , $id){
        // dd($request);
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        $distributor = Distributor::find($id);
        $sales =SaleOrder::
        // join('tso','tso.id','sale_orders.tso_id')
        // ->join('sale_order_data', 'sale_order_data.so_id', '=', 'sale_orders.id')
        // ->where('tso.status',1)
        where('status',1)
        ->whereBetween('dc_date', [$currentMonthStart, $currentMonthEnd])
        ->where('distributor_id' , $id)
        // ->where('excecution' , 1)
        ->orderby('dc_date' , 'desc')
        ->get();

        // dd($sales->toArray());
        return view($this->page . 'dashboard.top_distributor_report' , compact('sales' , 'distributor'));
    }
    public function top_product_report(Request $request , $id){
        // dd($request);

        $product = Product::find($id);


        if ($request->ajax()) {
            # code...
            $from = $request->from;
            $to = $request->to;
            $sales = SaleOrder::join('sale_order_data as sod', 'sod.so_id', '=', 'sale_orders.id')
            ->where('sod.product_id' , $id)
            // ->where('sale_orders.excecution' , 1)
            ->whereBetween('sale_orders.dc_date', [$from, $to])
            ->where('sale_orders.status' , 1)
            ->select('sale_orders.*' ,'sod.total', 'sod.qty')
            ->orderby('sale_orders.dc_date' , 'desc')
            ->groupBy('sod.id')
            ->get();

            return view($this->page . 'dashboard.ajax.top_product_report' , compact('sales' , 'product' , 'id'));

        }


        // dd($sales->toArray());
        return view($this->page . 'dashboard.top_product_report' , compact('product' , 'id'));
        // return view($this->page . 'dashboard.top_product_report' , compact('sales' , 'product' , 'id'));
    }
    public function top_shop_report(Request $request , $id){
        // dd($request);
        $shop = Shop::find($id);
        $sales = null;
        if ($request->type == 'top_sale') {
            $currentMonthStart = Carbon::now()->startOfMonth();
            $currentMonthEnd = Carbon::now()->endOfMonth();

            $sales =SaleOrder::
            // join('tso','tso.id','sale_orders.tso_id')
            // ->join('sale_order_data', 'sale_order_data.so_id', '=', 'sale_orders.id')
            // ->where('tso.status',1)
            where('status',1)
            ->whereBetween('dc_date', [$currentMonthStart, $currentMonthEnd])
            ->where('shop_id' , $id)
            // ->where('excecution' , 1)
            ->orderby('dc_date' , 'desc')
            ->get();
        }
        elseif($request->type == 'non_productive'){
            $sales =SaleOrder::
            // join('tso','tso.id','sale_orders.tso_id')
            // ->join('sale_order_data', 'sale_order_data.so_id', '=', 'sale_orders.id')
            // ->where('tso.status',1)
            where('status',1)
            // ->whereBetween('dc_date', [$currentMonthStart, $currentMonthEnd])
            ->where('shop_id' , $id)
            // ->where('excecution' , 1)
            ->orderby('dc_date' , 'desc')
            ->get();
        }


        // dd($sales->toArray());
        return view($this->page . 'dashboard.top_shop_report' , compact('sales' , 'shop' , 'request'));
    }

    public function sales_report(Request $request)
    {
        // dd($request);
        $sales = null;
        if ($request->ajax()){

            if ($request->type == 'today') {
                $sales = SaleOrder::join('users_distributors as b', 'b.distributor_id', '=', 'sale_orders.distributor_id')
                ->where('b.user_id', auth()->user()->id)
                ->whereDate('sale_orders.dc_date', '=', date('Y-m-d'))
                ->select('sale_orders.*')
                // ->get()
                ;
            }
            else if($request->type  == 'yesterday'){
                $yesterdayDate = Carbon::yesterday();
                $sales = SaleOrder::join('users_distributors as b', 'b.distributor_id', '=', 'sale_orders.distributor_id')
                ->where('b.user_id', auth()->user()->id)
                ->whereDate('sale_orders.dc_date', '=', $yesterdayDate->toDateString())
                ->select('sale_orders.*')
                // ->get()
                ;
            }
            else if($request->type  == 'last_Month'){
                $previousMonthStart = Carbon::now()->subMonth()->startOfMonth();
                $previousMonthEnd = Carbon::now()->subMonth()->endOfMonth();

                $sales = SaleOrder::join('users_distributors as b', 'b.distributor_id', '=', 'sale_orders.distributor_id')
                ->where('b.user_id', auth()->user()->id)
                ->whereBetween('sale_orders.dc_date', [$previousMonthStart, $previousMonthEnd])
                ->select('sale_orders.*')
                // ->get()
                ;

            }
            else if($request->type  == 'current_Month'){
                $currentMonthStart = Carbon::now()->startOfMonth();
                $currentMonthEnd = Carbon::now()->endOfMonth();

                $sales = SaleOrder::join('users_distributors as b', 'b.distributor_id', '=', 'sale_orders.distributor_id')
                ->where('b.user_id', auth()->user()->id)
                ->whereBetween('sale_orders.dc_date', [$currentMonthStart, $currentMonthEnd])
                ->select('sale_orders.*')
                // ->get()
                ;

            }
            // dd($request->all() , $request->type , $sales);
            return DataTables::of($sales)
                ->addIndexColumn()
                ->editColumn('dc_date', function($row) {
                    return date('d-m-Y', strtotime($row->dc_date));
                })
                ->editColumn('distributor', function($row) {
                    return $row->distributor->distributor_name;
                })
                ->editColumn('tso', function($row) {
                    return $row->tso->name;
                })
                ->editColumn('city', function($row) {
                    return $row->tso->cities->name;
                })
                ->editColumn('shop', function($row) {
                    return $row->shop->company_name;
                })
                ->editColumn('excecution', function($row) {
                    return $row->excecution ? 'YES' : 'NO' ;
                })
                ->editColumn('action', function($row) {
                    $action = '<div class="dropdown">
                    <i class="fa-solid fa-ellipsis-vertical dropdown-toggle action_cursor"
                        id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false"></i>
                    <div class="dropdown-menu dropdown-menu_sale_order_list"
                        aria-labelledby="dropdownMenuButton">';

                    // Add 'View' action if permission exists
                    if (auth()->user()->can('Sale_Order_VIew')) {
                        $action .= '<a target="_blank" data-url="' . route('sale.show', $row->id) . '"
                                        data-title="View Sale Order"
                                        class="dropdown-item_sale_order_list dropdown-item launcher">View</a>';
                    }

                    // Add 'Edit' and 'Delete' actions if conditions are met
                    if (!$row->excecution) {
                        if (auth()->user()->can('Sale_Order_Edit')) {
                            $action .= '<a href="' . route('sale.edit', $row->id) . '"
                                            class="dropdown-item_sale_order_list dropdown-item">Edit</a>';
                        }

                        if (auth()->user()->can('Sale_Order_Delete')) {
                            $action .= '<a href="javascript:void(0);" data-url="' . route('sale.destroy', $row->id) . '"
                                            id="delete-user" class="dropdown-item_sale_order_list dropdown-item">Delete</a>';
                        }
                    }

                    $action .= '</div>
                    </div>';

                    return $action;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        // dd($sales);
        return view($this->page . 'dashboard.sales_report' , compact('sales' , 'request'));

    }


    public function unit_sold_report(Request $request)
    {
        $sales = null;
        if ($request->type == 'currentMonth') {
            $currentMonthStart = Carbon::now()->startOfMonth();
            $currentMonthEnd = Carbon::now()->endOfMonth();

         $sales = DB::table('sale_orders')
    ->join('tso', 'tso.id', '=', 'sale_orders.tso_id')
    ->join('sale_order_data', 'sale_order_data.so_id', '=', 'sale_orders.id')
    ->join('products', 'products.id', '=', 'sale_order_data.product_id')
    ->join('shops', 'shops.id', '=', 'sale_orders.shop_id')
    ->select(
        'shops.company_name',
        'sale_orders.dc_date',
        'shops.id as shop_id',
        'sale_order_data.product_id',
        DB::raw('SUM(sale_order_data.qty) as product_count'),
        'products.product_name'
    )
    ->whereBetween('sale_orders.dc_date', [$currentMonthStart, $currentMonthEnd])
    ->where('sale_orders.status', 1)
    ->where('sale_orders.excecution', 1) // Add this if needed
    ->where('tso.status', 1)
    ->groupBy(
        'shops.company_name',
        'sale_orders.dc_date',
        'shops.id',
        'sale_order_data.product_id',
        'products.product_name'
    )
    ->get();

            // dd($sales->toArray());
        }

        return view($this->page . 'dashboard.unit_sold_report' , compact('sales' , 'request'));

    }
    public function top_shop_balance_report(Request $request)
    {
        $shops= Shop::where('status',1)
                // ->when($request->shop_id != null, function ($query) use ($request) {
                //     return $query->where('id', $request->shop_id);
                // })
                ->get();
            return view($this->page . 'dashboard.top_shop_balance_report' , compact('request' , 'shops'));

    }


    public function unproductive_shop_report(Request $request) //-----------------------------------------------
    {
        $from           = $request->from;
        $to             = $request->to;
        $distributor_id = $request->distributor_id;
        $tso_id         = $request->tso_id;
        $city           = $request->city;
 
        if ($request->ajax()) :
            $tsoIds = null;
            if ($distributor_id && !$tso_id) {
                $tsoIds = DB::table('tso')
                    ->where('distributor_id', $distributor_id)
                    ->pluck('id')
                    ->toArray();
            }

            $data = DB::table('shops as a')
                ->leftJoin('shop_visits as sv', function ($join) use ($from, $to, $request) {
                    $join->on('a.id', '=', 'sv.shop_id');
                    $join->where('sv.type', 0);
                    if ($from && $to) {
                        $join->whereBetween('sv.visit_date', [$from, $to]);
                    }
                })
                ->leftJoin('sale_orders as so', function ($join) use ($from, $to) {
                    $join->on('a.id', '=', 'so.shop_id');
                    if ($from && $to) {
                        $join->whereBetween(DB::raw('DATE(so.created_at)'), [$from, $to]);
                    }
                })
                ->join('routes', 'routes.id', '=', 'a.route_id')
                ->join('distributors as b', 'a.distributor_id', '=', 'b.id')
                ->join('shop_tso as st', 'st.shop_id', '=', 'a.id')
                ->join('tso as c', 'c.id', '=', 'st.tso_id')
                ->join('users as d', 'd.id', '=', 'c.manager')
                ->when($request->distributor_id != null, fn($q) => $q->where('a.distributor_id', $request->distributor_id))
                ->when($request->tso_id != null, fn($q) => $q->where('st.tso_id', $request->tso_id))
                ->when($tsoIds, fn($q) => $q->whereIn('st.tso_id', $tsoIds))
                ->when($request->route_id != null, fn($q) => $q->where('a.route_id', $request->route_id))
                ->when($request->shop_id != null, fn($q) => $q->where('a.id', $request->shop_id))
                ->where('a.status', 1)
                // this line ensures only shops with NO sale orders are returned
                ->whereNull('so.id')
                ->select(
                    'a.id',
                    'b.distributor_name',
                    'c.name as tso',
                    'c.id as tso_id',
                    'c.user_id',
                    'd.name as manager_name',
                    'sv.visit_date',
                    'a.shop_code',
                    'a.company_name as shop_name',
                    'routes.route_name',
                    DB::raw('COUNT(DISTINCT sv.id) as total_visit'),
                    DB::raw('COUNT(DISTINCT a.id) as total_shop'),
                    // DB::raw('(
                    //     SELECT COUNT(DISTINCT st2.shop_id)
                    //     FROM shop_tso st2
                    //     JOIN shops s2 ON s2.id = st2.shop_id
                    //     WHERE s2.distributor_id = a.distributor_id
                    //     AND s2.status = 1
                    //     ' . ($request->tso_id ? 'AND st2.tso_id = '.$request->tso_id : '') . '
                    //     ' . ($request->route_id ? 'AND s2.route_id = '.$request->route_id : '') . '
                    //     ' . ($request->shop_id ? 'AND s2.id = '.$request->shop_id : '') . '
                    // ) as total_shop')
                )
                ->groupBy(
                    // 'a.id',
                    // 'b.distributor_name',
                    'a.shop_code',
                    // 'a.company_name',
                    // 'routes.route_name',
                    // 'c.name',
                    // 'c.id',
                    // 'c.user_id',
                    // 'd.name',
                    // 'sv.visit_date'
                )
                ->orderBy('a.id', 'ASC')
                ->get();

                // dd($data);
            return view($this->page . 'unproductiveShop.unproductive_shop_list_ajax', compact('data', 'from', 'to', 'distributor_id', 'tso_id'));
        endif;
 
        return view($this->page . 'unproductiveShop.unproductive_shop_list');

    //  $data = DB::table('shops as a')
    //             ->join('shop_visits as sv', function ($join) use ($from, $to, $request) {
    //                 $join->on('a.id', '=', 'sv.shop_id');
 
    //                 $join->where('sv.type', 0);
 
    //                 if ($from && $to) {
    //                     $join->whereBetween('sv.visit_date', [$from, $to]);
    //                 }
    //                 // if ($request->visit_date != null) {
    //                 //     $join->where('sv.visit_date', $request->visit_date);
    //                 // }
    //             })
    //             ->join('routes', 'routes.id', '=', 'a.route_id')
    //             ->join('distributors as b', 'a.distributor_id', '=', 'b.id')
    //             ->join('shop_tso as st', 'st.shop_id', '=', 'a.id') // pivot table
    //             ->join('tso as c', 'c.id', '=', 'st.tso_id')       // get tso from pivot
    //             ->join('users as d', 'd.id', '=', 'c.manager')
    //             ->when($request->distributor_id != null, function ($query) use ($request) {
    //                 $query->where('a.distributor_id', $request->distributor_id);
    //             })
    //             ->when($request->shop_id != null, function ($query) use ($request) {
    //                 $query->where('a.id', $request->shop_id);
    //             })
    //             ->when($request->tso_id != null, fn($q) =>
    //                 $q->where('st.tso_id', $request->tso_id)
    //             )
    //             ->where('a.status', 1)
    //             ->select(
    //                 'a.id',
    //                 'b.distributor_name',
    //                 'c.name as tso',
    //                 'c.id as tso_id',
    //                 'c.user_id',
    //                 'd.name as manager_name',
    //                 'sv.visit_date',
    //                 'a.shop_code as shop_code',
    //                 'a.company_name as shop_name',
    //                 'routes.route_name',
    //                 'sv.shop_id as shop_visit_id',
    //                 DB::raw('COUNT(DISTINCT sv.id) as total_visit')
		
    //             )
    //             ->groupBy(
    //                 'a.id',
    //                 'b.distributor_name',
    //                 'a.shop_code',
    //                 'a.company_name',
    //                 'routes.route_name'
    //             )
    //             ->orderBy('a.id', 'ASC')
    //             ->get();


    }


}
