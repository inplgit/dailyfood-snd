<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SaleOrder;
use App\Models\User;
use App\Models\Shop;
use App\Models\TSOTarget;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\MasterFormsHelper;


class DashboardController extends BaseController
{
    // public function getDashboardSummary(Request $request)
    // {


    //     $user = User::find(Auth::id());
    //     $sales = DB::table('sale_orders')
    //     ->join('sale_order_data', 'sale_orders.id', '=', 'sale_order_data.so_id')
    //     ->where('sale_orders.status', 1)
    //     ->where('sale_orders.user_id', $user->id)
    //     ->where('sale_order_data.status', 1)
    //     ->whereDate('sale_orders.created_at', Carbon::today());


    //     $data['total_shop'] = DB::table('shops')
    //         ->join('shop_tso', 'shops.id', '=', 'shop_tso.shop_id')
    //         ->where('shop_tso.tso_id', $user->tso->id)
    //         ->where('shops.status', 1)
    //         ->where('shops.route_id', $request->route_id)
    //         ->count();


    //     //$data['total_shop'] = $user->tso->shop()->where('status', 1)->where('route_id',$request->route_id)->count();
    //     // dd($data['total_shop']);
    //     $data['total_product'] = Product::Status()->count();
    //     $data['total_order'] = $user->salesOrder()->status()->whereDate('dc_date', Carbon::today())->get()->count();
    //     //dd($data['total_order']);
    //     $data['non_productive'] = Shop::status()->where('route_id',$request->route_id)->count() - $data['total_order'] ;
    //     $data['execute_order'] = $user->salesOrder()->execute(1)->whereDate('dc_date', Carbon::today())->count();
    //     $data['total_sales'] = $sales->sum('sale_order_data.total');
    //     $data['total_sales_un_execute'] = $sales->where('excecution',0)->sum('sale_order_data.total');

    //     $data['total_order_quantity'] = $sales->sum('sale_order_data.qty');

    //     $data['total_shop_visit'] = $user->shopVisit()->whereDate('created_at', Carbon::today())->count();
    //     $data['total_return'] = $user->salesReturn()->whereDate('created_at', Carbon::today())->count();
    //     return $this->sendResponse($data,'Dashboard Summary Fetch...');
    // }


    public function getDashboardSummary(Request $request)
{
    $user = User::find(Auth::id());

    $sales = DB::table('sale_orders')
        ->join('sale_order_data', 'sale_orders.id', '=', 'sale_order_data.so_id')
        ->where('sale_orders.status', 1)
        ->where('sale_orders.user_id', $user->id)
        ->where('sale_order_data.status', 1)
        ->whereDate('sale_orders.created_at', Carbon::today());

    // ✅ Total Shops (status + active dono check)
    $data['total_shop'] = DB::table('shops')
        ->join('shop_tso', 'shops.id', '=', 'shop_tso.shop_id')
        ->where('shop_tso.tso_id', $user->tso->id)
        ->where('shops.status', 1)
        ->where('shops.active', 1)
        ->where('shops.route_id', $request->route_id)
        ->count();

    $data['total_product'] = Product::Status()->count();

    $data['total_order'] = $user->salesOrder()
        ->status()
        ->whereDate('dc_date', Carbon::today())
        ->count();

    // ✅ Non Productive = wohi shop count minus total orders
    $data['non_productive'] = Shop::where('status', 1)
        ->where('active', 1)
        ->where('route_id', $request->route_id)
        ->count() - $data['total_order'];

    $data['execute_order'] = $user->salesOrder()
        ->execute(1)
        ->whereDate('dc_date', Carbon::today())
        ->count();

    $data['total_sales'] = $sales->sum('sale_order_data.total');

    $data['total_sales_un_execute'] = $sales
        ->where('excecution', 0)
        ->sum('sale_order_data.total');

    $data['total_order_quantity'] = $sales->sum('sale_order_data.qty');

    $data['total_shop_visit'] = $user->shopVisit()
        ->whereDate('created_at', Carbon::today())
        ->count();

    $data['total_return'] = $user->salesReturn()
        ->whereDate('created_at', Carbon::today())
        ->count();

    return $this->sendResponse($data,'Dashboard Summary Fetch...');
}

    public function getTsoTarget(Request $request)
    {
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        $data['product_target'] = 0;
        $data['amount_target'] = 0;
        // $data['shop_target'] = 0;
        $tso_id = Auth::user()->tso->id;
        $product_target_issue = TSOTarget::where('tso_id' , $tso_id)
        ->where('type' , 1)
        ->whereBetween('month', [$currentMonthStart, $currentMonthEnd])->sum('qty');

        $amount_target_issue = TSOTarget::where('tso_id' , $tso_id)
        ->where('type' , 2)
        ->where('product_id' , null)
        ->whereBetween('month', [$currentMonthStart, $currentMonthEnd])->sum('amount');


        $target_achive = DB::table('sale_orders')
        ->join('sale_order_data', 'sale_order_data.so_id', 'sale_orders.id')
        ->where('sale_orders.tso_id', $tso_id)
        ->whereBetween('sale_orders.created_at', [$currentMonthStart, $currentMonthEnd])
        ->select(
            DB::raw('sum(sale_order_data.qty) as achieved_qty'),
            DB::raw('sum(sale_order_data.total) as achieved_amount'),
        )
        // ->groupBy('sale_order_data.product_id')
        ->first()
        // ->sum('sale_order_data.qty')
        ;

        // $shop_target_issue = TSOTarget::where('tso_id' , $tso_id)
        // ->where('type' , 3)
        // ->where('product_id' , null)
        // ->whereBetween('month', [$currentMonthStart, $currentMonthEnd])
        // ->select(
        //     DB::raw('sum(tso_targets.shop_qty) as shop_qty'),
        //     DB::raw('(SELECT COUNT(*)
        // FROM shops
        // JOIN shop_types ON shop_types.id = shops.shop_type_id
        // WHERE shops.tso_id = tso_targets.tso_id
        // AND MONTH(shops.created_at) BETWEEN MONTH("' . $currentMonthStart . '") AND MONTH("' . $currentMonthEnd . '")) as achieved_shop'))
        // ->first();
        // // ->sum('shop_qty');
        // dd($shop_target_issue);

        // $shop_target_achive = DB::table('shops')->where();

        // dd($target_achive);
        if ($product_target_issue > 0) {
            $data['product_target'] =  (float)number_format((($target_achive ? $target_achive->achieved_qty : 0) * 100) / $product_target_issue , 2);
        }
        if ($amount_target_issue > 0) {
            $data['amount_target'] =  (float)number_format((($target_achive ? $target_achive->achieved_amount : 0) * 100) / $amount_target_issue , 2);
        }
        // dd($tso_id , $product_target_issue , $amount_target_issue, $target_achive , $product_target , $amount_target);
        return $this->sendResponse($data,'Target Fetch...');
    }

    public function getDetailTsoTarget(Request $request)
    {
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        
        $tso_id = Auth::user()->tso->id;
        // $currentMonthStart = '2024-07-01';
        // $currentMonthEnd = '2024-07-31';
        // dd($currentMonthStart);

        $data['product_target_detail'] = TSOTarget::where('tso_id', $tso_id)
            ->where('type', 1)
            ->whereBetween('month', [$currentMonthStart, $currentMonthEnd])
            ->join('products' , 'products.id' , 'tso_targets.product_id')
            ->leftJoin(DB::raw('(
                SELECT
                    sale_order_data.product_id,
                    SUM(sale_order_data.qty) as achieved_qty
                FROM sale_orders
                JOIN sale_order_data ON sale_order_data.so_id = sale_orders.id
                WHERE sale_orders.tso_id = ' . $tso_id . '
               AND sale_orders.status = 1
                AND sale_orders.dc_date BETWEEN "' . $currentMonthStart . '" AND "' . $currentMonthEnd . '"
                GROUP BY sale_order_data.product_id
            ) as achieved_targets'), 'tso_targets.product_id', '=', 'achieved_targets.product_id')
            ->select(
                'products.product_name',
                'tso_targets.qty',
                DB::raw('IFNULL(achieved_targets.achieved_qty, 0) as achieved_qty')
            )
            ->get();



        $data['amount_target_detail'] = TSOTarget::where('tso_id', $tso_id)
            ->where('type', 2)
            ->whereNull('product_id')
            ->whereBetween('month', [$currentMonthStart, $currentMonthEnd])
            ->select(
                DB::raw('SUM(amount) as total_target_amount'),

                DB::raw('(SELECT SUM(sale_orders.total_amount)
                        FROM sale_orders
                        WHERE sale_orders.tso_id = ' . $tso_id . '
                         AND sale_orders.status = 1
                        AND sale_orders.dc_date BETWEEN "' . $currentMonthStart . '" AND "' . $currentMonthEnd . '") as achieved_amount')
            )
            ->first();


        $data['shop_target_detail'] = TSOTarget::where('tso_id', $tso_id)
                ->where('type', 3)
                ->whereNull('product_id')
                ->whereBetween('month', [$currentMonthStart, $currentMonthEnd])
                ->join('shop_types' , 'shop_types.id' , 'tso_targets.shop_type')
                ->select(
                    'shop_types.shop_type_name',
                    'tso_targets.shop_qty',
                    DB::raw('(SELECT COUNT(*)
                            FROM shops
                            JOIN shop_types ON shop_types.id = shops.shop_type_id
                            WHERE shops.tso_id = tso_targets.tso_id
                            AND MONTH(shops.created_at) BETWEEN MONTH("' . $currentMonthStart . '") AND MONTH("' . $currentMonthEnd . '")) as achieved_shop')
                )
                ->get();

                return $this->sendResponse($data,'Target Fetch...');

        //     dd($shop_target_issue->toArray() , $achived);
        // dd($product_target_issue->toarray() , $target_achive , $currentMonthStart , $currentMonthEnd);

    }
}
