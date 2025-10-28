<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;

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

use Illuminate\Support\Facades\Cache;

class ReportController extends BaseController
{
    public $page;
    public function __construct()
    {

        $this->master = new MasterFormsHelper();
        $this->page = 'pages.Reports.';

    }
   
  
  
  
  public function shop_wise_sales_report(Request $request) 

  
    {

        dd("test");
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


}