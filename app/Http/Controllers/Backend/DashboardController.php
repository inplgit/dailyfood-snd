<?php

namespace App\Http\Controllers\Backend;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\TSO;
use App\Models\Product;
use Carbon\Carbon;
use DB;
use DateTime;
use App\Helpers\MasterFormsHelper;

class DashboardController extends Controller
{
    public $master;

    public function __construct(){
        $this->master = new MasterFormsHelper();
    }
    public function index()
    {
        return view('pages.dashboard.index');
    }

    public function dashboarData()
    {
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();
        $previousMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $previousMonthEnd = Carbon::now()->subMonth()->endOfMonth();
        $currentDate = new DateTime();
        $previousMonth = clone $currentDate;
        $previousMonth->modify('first day of last month');
        $previousMonthFormatted = $previousMonth->format('F-Y');
        $yesterdayDate = Carbon::yesterday();


        $active_shop_count = $this->master->get_shop_distribuor_wise_count()->where('active' , 1)->count();
        $inactive_shop_count =  $this->master->get_shop_distribuor_wise_count()->where('active' , 0)->count();
        $pending_shop_count =  $this->master->get_shop_distribuor_wise_count()->whereIn('active' , [2,3,4])->count();
        // $tso_count = TSO::where('status',1)->count();
        $tso_active_count = $this->master->get_tso_distribuor_wise()->where('active',1)->count();
        $tso_inactive_count = $this->master->get_tso_distribuor_wise()->where('active',0)->count();
        $tso_pending_count =  $this->master->get_tso_distribuor_wise()->whereIn('active' , [2,3,4])->count();
        // dd($tso_count);
        $total_product = Product::where(['status'=>1])->count();
        // $current_month_sale_amount = DB::table('sale_orders')
        //     ->join('users_distributors as b', 'b.distributor_id', '=', 'sale_orders.distributor_id')
        //     ->where('b.user_id', auth()->user()->id)
        //     ->join('tso','tso.id','sale_orders.tso_id')
        //     // ->join('sale_order_data', 'sale_order_data.so_id', '=', 'sale_orders.id')
        //     // ->select(DB::raw('SUM(total) as amount'))
        //     ->select(DB::raw('SUM(total_amount) as amount'))
        //     ->whereBetween('sale_orders.dc_date', [$currentMonthStart, $currentMonthEnd])
        //     ->where('sale_orders.status',1)
        //     ->where('tso.status',1)
        //     ->where('sale_orders.excecution' , 1)
        //     ->first();


        //     $current_month_sale_return_amount = DB::table('sales_returns')
        //     ->join('users as b', 'b.id', '=', 'sales_returns.user_id')
        //     ->where('b.id', auth()->user()->id)
        //     ->select(DB::raw('SUM(amount) as amount'))
        //     ->whereBetween('sales_returns.created_at', [$currentMonthStart, $currentMonthEnd])
        //     ->where('sales_returns.status',1)
        //     ->where('sales_returns.excute' , 1)
        //     ->first();


        // Get the total sale amount for the current month
        $current_month_sale_amount = DB::table('sale_orders')
        ->join('users_distributors as b', 'b.distributor_id', '=', 'sale_orders.distributor_id')
        ->where('b.user_id', auth()->user()->id)
        ->join('tso', 'tso.id', 'sale_orders.tso_id')
        ->select(DB::raw('SUM(total_amount) as amount'))
        ->whereBetween('sale_orders.dc_date', [$currentMonthStart, $currentMonthEnd])
        ->where('sale_orders.status', 1)
        ->where('tso.status', 1)
        ->where('sale_orders.excecution', 1)
        ->first();

        // Get the total sales return amount for the current month
        $current_month_sale_return_amount = DB::table('sales_returns')
        ->join('users as b', 'b.id', '=', 'sales_returns.user_id')
        ->where('b.id', auth()->user()->id)
        ->select(DB::raw('SUM(amount) as amount'))
        ->whereBetween('sales_returns.created_at', [$currentMonthStart, $currentMonthEnd])
        ->where('sales_returns.status', 1)
        ->where('sales_returns.excute', 1)
        ->first();


        $net_sales = ($current_month_sale_amount->amount ?? 0) - ($current_month_sale_return_amount->amount ?? 0);

  

  
        $previous_month_sale_amount = DB::table('sale_orders')
            ->join('users_distributors as b', 'b.distributor_id', '=', 'sale_orders.distributor_id')
            ->where('b.user_id', auth()->user()->id)
            ->join('tso','tso.id','sale_orders.tso_id')
            // ->join('sale_order_data', 'sale_order_data.so_id', '=', 'sale_orders.id')
            // ->select(DB::raw('SUM(total) as amount'))
            ->select(DB::raw('SUM(total_amount) as amount'))
            ->whereBetween('sale_orders.dc_date', [$previousMonthStart, $previousMonthEnd])
            ->where('sale_orders.status', 1)
            ->where('tso.status',1)
            ->where('sale_orders.excecution' , 1)
            ->first();
        $today_sale_amount = DB::table('sale_orders')
            ->join('users_distributors as b', 'b.distributor_id', '=', 'sale_orders.distributor_id')
            ->where('b.user_id', auth()->user()->id)
            ->join('tso','tso.id','sale_orders.tso_id')
            // ->join('sale_order_data', 'sale_order_data.so_id', '=', 'sale_orders.id')
            // ->select(DB::raw('SUM(total) as amount'))
            ->select(DB::raw('SUM(total_pcs) as amount'))
            ->whereDate('sale_orders.dc_date', '=', date('Y-m-d'))
            ->where('sale_orders.status', 1)
            ->where('tso.status',1)
            // ->where('sale_orders.excecution' , 1)
            ->first();

        $yesterday_sale_amount = DB::table('sale_orders')
                ->join('users_distributors as b', 'b.distributor_id', '=', 'sale_orders.distributor_id')
                ->where('b.user_id', auth()->user()->id)
                ->join('tso','tso.id','sale_orders.tso_id')
                // ->join('sale_order_data', 'sale_order_data.so_id', '=', 'sale_orders.id')
                // ->select(DB::raw('SUM(total) as amount'))
                ->select(DB::raw('SUM(total_amount) as amount'))
                ->whereDate('sale_orders.dc_date', '=', $yesterdayDate->toDateString())
                ->where('sale_orders.status', 1)
                ->where('tso.status',1)
                ->where('sale_orders.excecution' , 1)
                ->first();


        // $yesterday_sale_amount = DB::table('sale_orders')
        // ->join('users_distributors as b', 'b.distributor_id', '=', 'sale_orders.distributor_id')
        // ->join('tso', 'tso.id', '=', 'sale_orders.tso_id')
        // ->leftJoin('sales_returns', 'sales_returns.so_id', '=', 'sale_orders.id') // Join with sales_returns
        // ->select(
        //     DB::raw('SUM(sale_orders.total_amount) as total_amount'), // Total sales amount
        //     DB::raw('SUM(COALESCE(sales_returns.amount, 0)) as return_amount'), // Total return amount
        //     DB::raw('SUM(sale_orders.total_amount) - SUM(COALESCE(sales_returns.amount, 0)) as net_amount') // Net amount
        // )
        // ->where('b.user_id', auth()->user()->id)
        // ->whereDate('sale_orders.dc_date', '=', $yesterdayDate->toDateString())
        // ->where('sale_orders.status', 1)
        // ->where('tso.status', 1)
        // ->where('sale_orders.excecution', 1)
        // ->first();
    


        //         dd($yesterday_sale_amount);

        $product_count = DB::table('sale_orders')
                ->join('users_distributors as b', 'b.distributor_id', '=', 'sale_orders.distributor_id')
                ->where('b.user_id', auth()->user()->id)
                ->join('tso','tso.id','sale_orders.tso_id')
                ->join('sale_order_data', 'sale_order_data.so_id', '=', 'sale_orders.id')
                ->join('products','products.id','sale_order_data.product_id')
                ->select('sale_order_data.product_id', DB::raw('sum(sale_order_data.qty) as product_count'),'products.product_name')
                ->whereBetween('sale_orders.dc_date', [$currentMonthStart, $currentMonthEnd])
                ->where('sale_orders.status', 1)
                ->where('tso.status',1)
                ->where('sale_orders.excecution' , 1)
                ->first();





                $product_count_new_return = DB::table('sale_orders')
    ->join('users_distributors as b', 'b.distributor_id', '=', 'sale_orders.distributor_id')
    ->where('b.user_id', auth()->user()->id)
    ->join('tso', 'tso.id', '=', 'sale_orders.tso_id')
    ->join('sale_order_data', 'sale_order_data.so_id', '=', 'sale_orders.id')
    ->join('products', 'products.id', '=', 'sale_order_data.product_id')
    ->leftJoin(DB::raw('
        (SELECT sales_order_data_id, SUM(qty) as return_qty 
         FROM sales_return_data 
         GROUP BY sales_order_data_id) as return_data
    '), 'return_data.sales_order_data_id', '=', 'sale_order_data.id')
    ->select(
        DB::raw('SUM(sale_order_data.qty) as total_product_count'),
        DB::raw('SUM(COALESCE(return_data.return_qty, 0)) as total_return_count'),
        DB::raw('SUM(sale_order_data.qty) - SUM(COALESCE(return_data.return_qty, 0)) as net_qty')
    )
    ->whereBetween('sale_orders.dc_date', [$currentMonthStart, $currentMonthEnd])
    ->where('sale_orders.status', 1)
    ->where('tso.status', 1)
    ->where('sale_orders.excecution', 1)
    ->first();

         
        

            $data=[
                'active_shop_count' =>$active_shop_count,
                'inactive_shop_count' =>$inactive_shop_count,
                'pending_shop_count' =>$pending_shop_count,
                'tso_active_count' =>$tso_active_count,
                'tso_inactive_count' =>$tso_inactive_count,
                'tso_pending_count' =>$tso_pending_count,
                'current_month_sale_amount' =>$net_sales,
             
                'previous_month_sale_amount' =>$previous_month_sale_amount,
                'previousMonthFormatted' =>$previousMonthFormatted,
                'today_sale_amount'=>$today_sale_amount,
                'yesterday_sale_amount'=>$yesterday_sale_amount,
                'product_count'=>$product_count,
                'product_count_new_return'=>$product_count_new_return,
                'total_product'=>$total_product,
                
            ];

            return $data;
    }


    public function notification_redirect($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);

        // Mark the notification as read
        $notification->markAsRead();

        // Redirect based on notification data
        return redirect($notification->data['url'] ?? '/'); // 'url' should be a key in your notification data
    }
}
