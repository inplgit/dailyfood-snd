<?php

namespace App\Helpers;

use App\Models\Distributor;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\ProductFlavour;
use App\Models\UOM;
use App\Models\TSO;
use App\Models\User;
use App\Models\Zone;
use App\Models\ShopType;
use App\Models\Shop;
use App\Models\Config;
use App\Models\Type;
use App\Models\Stock;
use App\Models\Route;
use App\Models\Rack;
use App\Models\ActivityLog;
use App\Models\UsersLocation;
use App\Models\SalesReturnData;
use App\Models\SalesReturn;
use App\Models\SaleOrder;
use App\Models\SaleOrderData;
use App\Models\ReceiptVoucher;
use App\Models\City;
use App\Models\UsersDistributors;
use App\Models\SubRoutes;
use Spatie\Permission\Models\Role;
use DB;
use Illuminate\Support\Facades\Storage;
use PDF;

use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use App\Models\SaleOrderReturn;

class MasterFormsHelper
{


    public function __construct()
    {
    }
    public static function changeDateFormat($param1)
    {
        $date = date_create($param1);
        return date_format($date, "d-m-Y");
    }
    public static function changeDateFormat2($param1 , $format)
    {
        $date = date_create($param1);
        return date_format($date, $format);
    }
    public static function userType()
    {
        return Type::where('type', '!=', 'TSO')->get();
    }

   public static function get_sale_qty_new($from , $to , $product_id , $flavour_id , $uom_id , $tso , $distributor , $execution)
    {
      // dd($from , $to , $product_id , $flavour_id , $uom_id , $tso , $distributor , $execution);
        $qty = DB::table('sale_orders')->join('sale_order_data', 'sale_order_data.so_id', 'sale_orders.id')
        ->where('sale_orders.status', 1)
        ->where('sale_order_data.product_id', $product_id)
      
        ->whereBetween('sale_orders.dc_date', [$from, $to]);
        if (isset($tso)) {
            $qty = $qty->where('sale_orders.tso_id', $tso);
        }
        if (isset($distributor)) {
            $qty = $qty->where('sale_orders.distributor_id', $distributor);
        }
        // if (isset($execution)) {
        //     $qty = $qty->where('sale_orders.excecution', $execution);
        // }
        $qty = $qty->sum('sale_order_data.qty');
      //  dd($qty);
        return $qty;
    }


 public static function get_sale_product_discount($from, $to, $product_id, $flavour_id, $uom_id, $tso, $distributor, $execution)
    {
        $discount = DB::table('sale_orders')->join('sale_order_data', 'sale_order_data.so_id', 'sale_orders.id')
            ->where('sale_orders.status', 1);
        if (isset($flavour_id) && $flavour_id != 0) {
            $discount = $discount->where('sale_order_data.flavour_id', $flavour_id);
        }
        $discount = $discount->where('sale_order_data.sale_type', $uom_id)
            ->whereBetween('sale_orders.dc_date', [$from, $to]);
        if (isset($product_id)) {
            $discount = $discount->where('sale_order_data.product_id', $product_id);
        }
         if (isset($tso)) {
            $discount = $discount->where('sale_orders.tso_id', $tso);
        }
        if (isset($distributor)) {
            $discount = $discount->where('sale_orders.distributor_id', $distributor);
        }
        if (isset($execution)) {
            $discount = $discount->where('sale_orders.excecution', $execution);
        }
        return $discount->sum('sale_order_data.discount');
    }
    public function Days()
    {
        return $days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
    }

    public static function get_all_distributors()
    {
        return Distributor::status()->get();
    }
    public static function get_distributor_name($id)
    {
        return Distributor::where('id' , $id)->status()->value('distributor_name');
    }
    public static function get_all_designation()
    {
        return DB::table('designations')->select('id', 'name')
            ->where('status', 1)
            ->get();
    }

    public function get_distributor_by_city($city_ids)
    {
        $distributor_ids = self::get_all_distributor_user_wise_pluck();
        return Distributor::status()
        ->whereIn('id',$distributor_ids)
        ->when($city_ids , function($q) use ($city_ids)
        {
            // $q->whereIn('city_id', $city_ids);
            if (is_array($city_ids)) {
                $q->whereIn('city_id', $city_ids);
            } else {
                $q->where('city_id', $city_ids);
            }

        })->get();
    }


 public static function get_tso_distribuor_wise_check()
    {

        return  TSO::whereIn('user_id', MasterFormsHelper::get_assign_user_check())->Status()->get();
    }

     public static function get_assign_user_check()
    {
        $distributors = MasterFormsHelper::get_users_distributors(Auth::user()->id);
        return UsersDistributors::whereIn('distributor_id',$distributors)->groupBy('user_id')->pluck('user_id');
    }

public static function get_sales_orders_return_shop_wise_excute($request)
{
    return SaleOrderReturn::with('returnDetails')
        ->when($request->distributor_id, function ($query) use ($request) {
            $query->where('distributor_id', $request->distributor_id);
        })
        ->when($request->tso_id, function ($query) use ($request) {
            $query->whereHas('tso', function ($q) use ($request) {
                $q->where('id', $request->tso_id);
            });
        })
        ->when($request->from && $request->to, function ($query) use ($request) {
            $query->whereBetween('return_date', [$request->from, $request->to]);
        })
        ->where('excecution', 0)
        ->where('status', 1)
        ->get();
}




public static function get_sale_qty2_summary_details_with_city($from , $to , $product_id , $flavour_id , $uom_id,$distributor_id,$city)
    {
        // dd($from , $to , $product_id , $flavour_id , $uom_id , $tso , $distributor , $city);
        $qty = DB::table('sale_orders')->join('sale_order_data', 'sale_order_data.so_id', 'sale_orders.id')
        ->join('tso', 'tso.id' , 'sale_orders.tso_id')
	->join('cities', 'cities.id' , 'tso.city')
        ->where('sale_orders.status', 1);
	//->when(!empty($distributor_id), function ($query) use ($distributor_id) {
        	//return $query->where('tso.distributor_id', $distributor_id);
    	//}, function ($query) {
        	//return $query->whereNotNull('tso.distributor_id');
    	//})
	
     
        if (isset($distributor_id)) {
            $qty = $qty->where('sale_orders.distributor_id', $distributor_id);
        }
	if (isset($tso_id)) {
            $qty = $qty->where('sale_orders.tso_id', $tso_id);
        }
        if (isset($product_id)) {
            $qty = $qty->where('sale_order_data.product_id', $product_id);
        }
        if (isset($flavour_id)) {
            $qty = $qty->where('sale_order_data.flavour_id', $flavour_id);
        }
	if (isset($city)) {
            $qty = $qty->where('tso.city', $city);
        }
        if (isset($uom_id)) {
            $qty = $qty->where('sale_order_data.sale_type', $uom_id);
        }
        if (isset($from)) {
            $qty = $qty->whereBetween('sale_orders.dc_date', [$from, $to]);
        }
       

        
        $qty = $qty->sum('sale_order_data.qty');
        return $qty;
    }
    

    
public static function get_sale_qty2_excute_suumary_details_with_city($from , $to , $product_id , $flavour_id , $uom_id,$distributor_id,$city)
    {
        // dd($from , $to , $product_id , $flavour_id , $uom_id , $tso , $distributor , $city);
        $qty = DB::table('sale_orders')->join('sale_order_data', 'sale_order_data.so_id', 'sale_orders.id')
        ->join('tso', 'tso.id' , 'sale_orders.tso_id')
	->join('cities', 'cities.id' , 'tso.city')
        ->where('sale_orders.status', 1)
    	->where('tso.status', 1) 
    	//->when(!empty($distributor_id), function ($query) use ($distributor_id) {
        	//return $query->where('tso.distributor_id', $distributor_id);
    	//}, function ($query) {
        	//return $query->whereNotNull('tso.distributor_id');
    	//})
        ->where('sale_orders.status', 1)
        ->where('sale_orders.excecution', 1);
        if (isset($product_id)) {
            $qty = $qty->where('sale_order_data.product_id', $product_id);
        }
        if (isset($flavour_id)) {
            $qty = $qty->where('sale_order_data.flavour_id', $flavour_id);
        }
        if (isset($uom_id)) {
            $qty = $qty->where('sale_order_data.sale_type', $uom_id);
        }

        if (isset($city)) {
            $qty = $qty->where('tso.city', $city);
        }
        if (isset($distributor_id)) {
            $qty = $qty->where('sale_orders.distributor_id', $distributor_id);
        }
        if (isset($from)) {
            $qty = $qty->whereBetween('sale_orders.dc_date', [$from, $to]);
        }
     
        $qty = $qty->sum('sale_order_data.qty');
        return $qty;
    }

    public static function get_sale_qty2_summary_details_amount_with_city($from , $to , $product_id , $flavour_id , $uom_id,$distributor_id,$city)
    {
        // dd($from , $to , $product_id , $flavour_id , $uom_id , $tso , $distributor , $city);
         $qty = DB::table('sale_orders')->join('sale_order_data', 'sale_order_data.so_id', 'sale_orders.id')
        ->join('tso', 'tso.id' , 'sale_orders.tso_id')
	->join('cities', 'cities.id' , 'tso.city')
        ->where('sale_orders.status', 1);
     
        if (isset($distributor_id)) {
            $qty = $qty->where('sale_orders.distributor_id', $distributor_id);
        }
        if (isset($product_id)) {
            $qty = $qty->where('sale_order_data.product_id', $product_id);
        }
        if (isset($flavour_id)) {
            $qty = $qty->where('sale_order_data.flavour_id', $flavour_id);
        }
	if (isset($city)) {
            $qty = $qty->where('tso.city', $city);
        }
        if (isset($uom_id)) {
            $qty = $qty->where('sale_order_data.sale_type', $uom_id);
        }
        if (isset($from)) {
            $qty = $qty->whereBetween('sale_orders.dc_date', [$from, $to]);
        }
       

        
        $amount = $qty->sum('sale_order_data.total');
        return $amount;



    }


    public static function get_sale_qty2_summary_details_execution_amount_with_city($from , $to , $product_id , $flavour_id , $uom_id,$distributor_id,$city)
    {
        // dd($from , $to , $product_id , $flavour_id , $uom_id , $tso , $distributor , $city);
         $qty = DB::table('sale_orders')->join('sale_order_data', 'sale_order_data.so_id', 'sale_orders.id')
        ->join('tso', 'tso.id' , 'sale_orders.tso_id')
	->join('cities', 'cities.id' , 'tso.city')
        ->where('sale_orders.status', 1)
	->where('sale_orders.excecution', 1);
     
        if (isset($distributor_id)) {
            $qty = $qty->where('sale_orders.distributor_id', $distributor_id);
        }
        if (isset($product_id)) {
            $qty = $qty->where('sale_order_data.product_id', $product_id);
        }
        if (isset($flavour_id)) {
            $qty = $qty->where('sale_order_data.flavour_id', $flavour_id);
        }
	if (isset($city)) {
            $qty = $qty->where('tso.city', $city);
        }
        if (isset($uom_id)) {
            $qty = $qty->where('sale_order_data.sale_type', $uom_id);
        }
        if (isset($from)) {
            $qty = $qty->whereBetween('sale_orders.dc_date', [$from, $to]);
        }
       

        
        $amount = $qty->sum('sale_order_data.total');
        return $amount;



    }


       public static function get_flavour_sku($id)
    {
        // dd($id);
        $data = ProductFlavour::where('id',$id)->value('sku_code');
        // dd($data);
        return $data;
    }


    public static function get_all_distributor_user_wise()
    {
        return User::find(Auth::user()->id)->distributors()->select('distributors.id', 'distributor_name', 'distributor_code', 'city', 'contact_person', 'phone', 'distributor_sub_code', 'status')->where('status', 1)->sort()->get();
    }
    public static function get_all_distributor_not_user_wise()
    {
        if (Auth::user()->user_type === 1) {
            return User::find(Auth::user()->id)->distributors()->select('distributors.id', 'distributor_name', 'distributor_code', 'city', 'contact_person', 'phone', 'distributor_sub_code', 'status')->where('status', 1)->sort()->get();
        } else {
            return DB::table('distributors')
                ->select(
                    'distributors.id',
                    'distributor_name',
                    'distributor_code',
                    'city',
                    'contact_person',
                    'phone',
                    'distributor_sub_code',
                    'status'
                )
                ->where('status', 1)
                ->whereNotIn('distributors.id', function ($query) {
                    $query->select('distributor_id')
                        ->from('users_distributors')
                        ->where('user_id', Auth::user()->id);
                })
                ->orderBy('distributors.id', 'asc')
                ->get();
        }
    }
    public static function get_all_distributor_user_wise_pluck()
    {
        return User::find(Auth::user()->id)->distributors()->pluck('distributors.id');
    }
    public function get_all_tso()
    {
        return TSO::all();
    }

    public function get_all_tso_by_distributor_id($id , $active = true)
    {
        $data = TSO::status();
        if ($active) {
            $data = $data->Active();
        }
        $data = $data->whereHas('UserDistributor', function ($query) use ($id) {
            $query->where('distributor_id', $id)
                ->groupBy('user_id');
        })->get();
        return  $data;

        // return $tso = TSO::whereHas()->status()->whereIn('distributor_id',$id)->select('name','id')->get();

    }

    public function get_all_tso_by_distributor_id_multi($id, $active = true)
    {
        $data = TSO::status(); // Assuming this scope filters based on status
    
        if ($active) {
            $data = $data->Active();
        }
        $data = $data->whereHas('UserDistributor', function ($query) use ($id) {
            $query->where('distributor_id', $id);
        })->groupBy('user_id')->select('id', 'name')->get();
        return $data;
    }


    public function get_all_tso_by_distributor_ids($id)
    {
        // dd($id);
        return  TSO::status()->whereHas('UserDistributor', function ($query) use ($id) {
            $query->whereIn('distributor_id', $id)
                ->groupBy('user_id');
        })->get();

        // return $tso = TSO::whereHas()->status()->whereIn('distributor_id',$id)->select('name','id')->get();

    }
    public function get_all_route_by_shop_ids($id)
    {
        // Ensure $id is an array
        $id = is_array($id) ? $id : [$id];
    
        // Fetch routes based on $id
        $data = Route::whereIn('id', $id)->get();
    
        return $data;
    }
    
    

    public static function get_all_product()
    {
        return Product::status()->get();
    }
    public static function get_product_by_id($id)
    {
        return Product::status()->where('id' , $id)->first();
    }
    public static function get_product_name_by_id($id)
    {
        return Product::status()->where('id' , $id)->value('product_name');
    }
    public static function get_all_sheme_product()
    {
        return Product::where('status', 1)->where('product_type_id', 2)->get();
    }
    public function get_all_route_by_tso($id)
    {
        $tso = [];
        if ($id != null) :
            $tso = TSO::find($id)->route()->where('status', 1)->select('day', 'id', 'route_name')->get();
        endif;
        return $tso;
    }


    public static function get_all_route_by_tso_multi($tso_id)
{
    if (!$tso_id) {
        return collect(); 
    }

    $routeIds = \App\Models\RouteTso::where('tso_id', $tso_id)
                ->pluck('route_id');

    return \App\Models\Route::whereIn('id', $routeIds)
                ->where('status', 1)
                ->select('id', 'route_name')
                ->get();
}

    public function get_all_routes()
    {

        $routes = Route::status()->get();
        return $routes;
    }
    public function get_all_sub_routes_by_route($id)
    {

        $sub_routes = SubRoutes::status()->where('route_id',$id)->get();
        return $sub_routes;
    }

    public function get_all_zone()
    {
        return  Zone::status()->get();
    }

    public function get_all_racks()
    {
        return  Rack::status()->get();
    }

    public function get_all_shop_type()
    {
        return  ShopType::status()->get();
    }
    public function shop_type_name($id)
    {
        return  ShopType::status()->where('id' , $id)->value('shop_type_name');
    }

    public function getReturnQty($id)
    {
        return SalesReturnData::where('sales_order_data_id', $id)->sum('qty');
    }

    public function get_distributor_level_wise()
    {
        return Distributor::status()
            ->where('level3', 0)
            ->orderBy('level1', 'ASC')
            ->orderBy('level2', 'ASC')
            ->get();
    }



    public static function InStock($product_id, $distributor, $qty)
    {
        $data = false;
        $in =  Stock::whereIn('stock_type', [0 ,1, 2, 4])->where('status', 1)->where('product_id', $product_id)->where('distributor_id', $distributor)->sum('qty');
        $out =  Stock::whereIn('stock_type', [3,5])->where('status', 1)->where('product_id', $product_id)->where('distributor_id', $distributor)->sum('qty');
        $qty = $in - $out - $qty;

        if ($qty >= 0) :
            $data = true;
        endif;
        return $data;
    }

    public static function qtyInStock($product_id, $distributor, $qty)
    {
        $data = false;
        $in =  Stock::whereIn('stock_type', [0, 1, 2, 4])->where('status', 1)->where('product_id', $product_id)->where('distributor_id', $distributor)->sum('qty');
        $out =  Stock::whereIn('stock_type', [3,5])->where('status', 1)->where('product_id', $product_id)->where('distributor_id', $distributor)->sum('qty');
        $qty = $in - $out - $qty;

        return $qty;
    }

    public static function get_Stock_opening($product_id, $flavour_id , $uom_id , $distributor , $from)
    {
        $data = false;
        $in =  Stock::whereIn('stock_type', [0,1,2,4,6])->where('status', 1)
        ->where('voucher_date' , '<', $from)
        ->where('product_id', $product_id)->where('flavour_id' , $flavour_id)->where('uom_id' , $uom_id)
        ->when($distributor != null, function($q) use ($distributor){
            $q->where('distributor_id', $distributor);
        })->sum('qty');
        $out =  Stock::whereIn('stock_type', [3,5,7])->where('status', 1)
        ->where('voucher_date' , '<', $from)
        ->where('product_id', $product_id)->where('flavour_id' , $flavour_id)->where('uom_id' , $uom_id)
        ->when($distributor != null, function($q) use ($distributor){
            $q->where('distributor_id', $distributor);
        })->sum('qty');
        $qty = $in - $out;

        return $qty;
    }

    public static function get_InStock($product_id, $flavour_id , $uom_id ,$distributor , $qty)
    {
        $data = false;
        $in =  Stock::whereIn('stock_type', [0,1,2,4,6])->where('status', 1)->where('product_id', $product_id)->where('flavour_id' , $flavour_id)->where('uom_id' , $uom_id)->where('distributor_id', $distributor)->sum('qty');
        $out =  Stock::whereIn('stock_type', [3,5,7])->where('status', 1)->where('product_id', $product_id)->where('flavour_id' , $flavour_id)->where('uom_id' , $uom_id)->where('distributor_id', $distributor)->sum('qty');
        $qty = $in - $out - $qty;

        if ($qty >= 0) :
            $data = true;
        endif;
        return $data;
    }
    public static function get_Stock($product_id, $flavour_id , $uom_id ,$distributor)
    {
        $data = false;

        $in =  Stock::whereIn('stock_type', [0,1,2,4,6])->where('status', 1)->where('product_id', $product_id)->where('flavour_id' , $flavour_id)->where('uom_id' , $uom_id)
        ->when($distributor != null , function($q) use ($distributor) {
            $q->where('distributor_id', $distributor);
        })
        ->sum('qty');


        $out =  Stock::whereIn('stock_type', [3,5,7])->where('status', 1)->where('product_id', $product_id)->where('flavour_id' , $flavour_id)->where('uom_id' , $uom_id)
        ->when($distributor != null , function($q) use ($distributor) {
            $q->where('distributor_id', $distributor);
        })
        ->sum('qty');
        $qty = $in - $out;

        return $qty;
    }



    public static function get_Opining_Stock($product_id, $flavour_id , $uom_id ,$distributor)
    {
        $data = false;

        $qty =  Stock::where('stock_type',1)->where('status', 1)->where('product_id', $product_id)->where('flavour_id' , $flavour_id)->where('uom_id' , $uom_id)
        ->when($distributor != null , function($q) use ($distributor) {
            $q->where('distributor_id', $distributor);
        })
        ->latest('updated_at')
        ->first();


    // $test = $qty->qty;

        return $qty->qty ?? 0;
    }

    

    public static function get_Stock_by_stock_type($product_id, $flavour_id , $uom_id ,$distributor , $stock_type , $from = null , $to = null)
    {
        $data = false;
        $qty =  Stock::whereIn('stock_type', $stock_type)->where('status', 1)->where('product_id', $product_id)
        ->where('flavour_id' , $flavour_id)->where('uom_id' , $uom_id)
        ->when($distributor != null , function($q) use ($distributor) {
            $q->where('distributor_id', $distributor);
        })
        ->when($from != null && $to != null, function($q) use ($from , $to){
            $q->whereBetween('voucher_date', [$from, $to]);
        })
        ->sum('qty');

        return $qty;
    }
    public static function get_Stock_by_stock_type_report($product_id, $flavour_id , $uom_id ,$distributor , $stock_type , $from = null , $to = null)
    {
        $data = false;
        $qty =  Stock::whereIn('stock_type', $stock_type)->where('status', 1)->where('product_id', $product_id)
        ->where('flavour_id' , $flavour_id)->where('uom_id' , $uom_id)
        ->when($distributor != null , function($q) use ($distributor) {
            $q->where('distributor_id', $distributor);
        })
        ->when($from != null && $to != null, function($q) use ($from , $to){
            $q->whereBetween('voucher_date', [$from, $to]);
        })
        ->latest('updated_at')
        ->first();

        return $qty->qty ?? 0;
    }
  public static function get_stock_type_wise_data_without_uom($product_id, $flavour_id ,$distributor , $stock_type)
    {
        $main_qty = '';
        $main_amount = 0;
        foreach (self::get_product_price($product_id) as $k => $productPrice) {
            $qty = self::get_Stock_by_stock_type($product_id, $flavour_id , $productPrice->uom_id , $distributor , $stock_type);

            // $uom_name = self::uom_name($productPrice->uom_id); // Get UOM name for each product_price UOM
            //if ($qty > 0) {
                $main_qty .= ($main_qty ? ' , ' : '') . $qty;
                $value = $qty * $productPrice->trade_price;
                $main_amount += $value;
            //}
        }

        return ['main_qty' => $main_qty , 'main_amount' => $main_amount];

    }
    public static function get_stock_type_wise_data_opening($product_id, $flavour_id ,$distributor , $stock_type)
    {
        $main_qty = '';
        $main_amount = 0;
        foreach (self::get_product_price($product_id) as $k => $productPrice) {
            $qty = self::get_Stock_by_stock_type_opening($product_id, $flavour_id , $productPrice->uom_id , $distributor , $stock_type);

            $uom_name = self::uom_name($productPrice->uom_id); // Get UOM name for each product_price UOM
            //if ($qty > 0) {
		$pcs_per_carton = ProductPrice::select('pcs_per_carton')->where('product_id' , $product_id)->where('uom_id','!=',7)->where('status', 1)->where('start_date' ,'<=', date('Y-m-d'))->orderBy('start_date','desc')->value('pcs_per_carton');
               
		$main_qty .= ($main_qty ? ' , ' : '') . $qty . '=>' . $uom_name . '=>'. $pcs_per_carton;
                $value = $qty * $productPrice->trade_price;
                $main_amount += $value;
            //}
        }

        return ['main_qty' => $main_qty , 'main_amount' => $main_amount];

    }

  public static function get_stock_type_wise_data_opening_with_date($product_id, $flavour_id ,$distributor , $stock_type,$from,$to)
    {
        $main_qty = '';
        $main_amount = 0;
        foreach (self::get_product_price($product_id) as $k => $productPrice) {
            $qty = self::get_Stock_by_stock_type_opening_with_date($product_id, $flavour_id , $productPrice->uom_id , $distributor , $stock_type,$from,$to);

            $uom_name = self::uom_name($productPrice->uom_id); // Get UOM name for each product_price UOM
            //if ($qty > 0) {
		$pcs_per_carton = ProductPrice::select('pcs_per_carton')->where('product_id' , $product_id)->where('uom_id','!=',7)->where('status', 1)->where('start_date' ,'<=', date('Y-m-d'))->orderBy('start_date','desc')->value('pcs_per_carton');
               
		$main_qty .= ($main_qty ? ' , ' : '') . $qty . '=>' . $uom_name . '=>'. $pcs_per_carton;
                $value = $qty * $productPrice->trade_price;
                $main_amount += $value;
            //}
        }

        return ['main_qty' => $main_qty , 'main_amount' => $main_amount];

    }

 public static function prepare_stock_data($result, $from, $to,$type)
    {
        $preparedData = [];

        
        $productIds = $result->pluck('product_id')->unique()->toArray();
        $flavourIds = $result->pluck('flavour_id')->unique()->toArray();
        if($type=='detail'){
            $distributorIds = $result->pluck('distributor_id')->unique()->toArray();
        }
        else{
            $cityIds = $result->pluck('city_id')->unique()->toArray();
        }
       
        $productPrices = ProductPrice::
             whereIn('product_id', $productIds)
            ->where('status', 1)
            ->where('start_date','<=', date('Y-m-d'))
            ->orderBy('start_date', 'desc')
            ->get()
            ->groupBy('product_id');

        $productPcsPerCarton = ProductPrice::select('product_id', 'uom_id', 'pcs_per_carton')
        ->whereIn('product_id', $productIds)
        ->where('uom_id', '!=', 7)
        ->where('status', 1)
        ->where('start_date','<=', date('Y-m-d'))
        ->orderBy('start_date', 'desc')
        ->get()
        ->groupBy('product_id');    

        $uoms = UOM::whereIn('id', $productPrices->pluck('uom_id')->unique())->get()->keyBy('id');

        if($type=='detail'){
            $stocks = Stock::whereIn('product_id', $productIds)
                ->whereIn('flavour_id', $flavourIds)
                ->whereIn('distributor_id', $distributorIds)
                ->where('status', 1)
                ->whereIn('stock_type', [1])
                ->when(!empty($from), function($q) use ($from){
                    $q->where('voucher_date','>=', $from);
                })
                ->when(!empty($to), function($q) use ($to){
                    $q->where('voucher_date','<=', $to);
                })
                ->get()
                ->groupBy(function($item) {
                    return $item->product_id . '-' . $item->flavour_id . '-' . $item->uom_id . '-' . $item->distributor_id;
                })
                ->map(function($group) {
                    return $group->sortByDesc('voucher_date')->first(); // still keeps latest if multiple in same date
                });
                //->values();
        }else{

            $stocks = Stock::Join('distributors', 'distributors.id', '=', 'stocks.distributor_id')
                ->select('stocks.*')
                ->whereIn('stocks.product_id', $productIds)
                ->whereIn('stocks.flavour_id', $flavourIds)
                ->where('stocks.status', 1)
                ->whereIn('stocks.stock_type', [1])
                ->whereIn('distributors.city_id', $cityIds)
                ->when(!empty($from), function($q) use ($from){
                    $q->where('stocks.voucher_date','>=', $from);
                })
                ->when(!empty($to), function($q) use ($to){
                    $q->where('stocks.voucher_date','<=', $to);
                })
                ->orderByDesc('stocks.voucher_date') 
                ->get()
                // ->unique(function ($item) {
                //     return $item->product_id . '-' . $item->flavour_id . '-' . $item->uom_id;
                // })
                ->groupBy(function ($item) {
                    return $item->product_id . '-' . $item->flavour_id . '-' . $item->uom_id;
                })->map(function($group) {
                    return $group->sortByDesc('stocks.voucher_date')->first(); 
                });
            //->values();

            // echo "<pre>";
            // print_r($stocks);
            // exit();
        }
       
        foreach ($result as $item) {
            $main_qty = '';
            $main_amount = 0;
            if(in_array($item->stock_type,[1])){
                
                 $productPriceList = $productPrices[$item->product_id] ?? collect();

                
                $filteredPrices = $productPriceList
                    ->where('start_date', '<=', $item->voucher_date)
                    ->groupBy('uom_id')
                    ->map(function ($prices) {
                        
                        return $prices->sortByDesc('start_date')->first();
                    });
            
                foreach ($filteredPrices as $productPrice) {
                    
                    if($type=='detail'){
                        $stock_key = $item->product_id . '-' . $item->flavour_id . '-' . $productPrice->uom_id . '-' . $item->distributor_id;
                    }else{
                        $stock_key = $item->product_id . '-' . $item->flavour_id . '-' . $productPrice->uom_id;
                    }
                    $qty = $stocks[$stock_key]->qty ?? 0;
                    $uom_name = $uoms[$productPrice->uom_id]->uom_name ?? '--';
                    
                    $pcs_per_carton = $productPcsPerCarton[$item->product_id][0]->pcs_per_carton ?? 0;

                    $main_qty .= ($main_qty ? ' , ' : '') . $qty . '=>' . $uom_name . '=>'. $pcs_per_carton;
                    $value = $qty * $productPrice->trade_price;
                    $main_amount += $value;
                    
                
                }
                if($type=='detail'){
                    $preparedData[$item->product_id][$item->flavour_id][$item->distributor_id] = [
                        'main_qty' => $main_qty,
                        'main_amount' => $main_amount,
                    ];
                }else{
                    $preparedData[$item->product_id][$item->flavour_id] = [
                        'main_qty' => $main_qty,
                        'main_amount' => $main_amount,
                    ];
                }
            } 
        }
        
        
        return $preparedData;
    }

    public static function prepare_stock_data_summary($result, $from, $to)
    {
        $preparedData = [];

        // Product IDs unique collect kar lo
        $productIds = $result->pluck('product_id')->unique()->toArray();
        $flavourIds = $result->pluck('flavour_id')->unique()->toArray();
        

        // Pehle saari necessary data array mein le lo
        $productPrices = ProductPrice::
             whereIn('product_id', $productIds)
            ->where('status', 1)
            ->where('start_date','<=', date('Y-m-d'))
            ->orderBy('start_date', 'desc')
            ->get()
            ->groupBy('product_id');

        $productPcsPerCarton = ProductPrice::select('product_id', 'uom_id', 'pcs_per_carton')
        ->whereIn('product_id', $productIds)
        ->where('uom_id', '!=', 7)
        ->where('status', 1)
        ->where('start_date','<=', date('Y-m-d'))
        ->orderBy('start_date', 'desc')
        ->get()
        ->groupBy('product_id');    

        $uoms = UOM::whereIn('id', $productPrices->pluck('uom_id')->unique())->get()->keyBy('id');

        $stocks = Stock::whereIn('product_id', $productIds)
            ->whereIn('flavour_id', $flavourIds)
            ->where('status', 1)
            ->whereIn('stock_type', [1])
            ->when(!empty($from), function($q) use ($from){
                $q->where('voucher_date','>=', $from);
            })
            ->when(!empty($to), function($q) use ($to){
                $q->where('voucher_date','<=', $to);
            })
            ->get()
            ->groupBy(function($item) {
                return $item->product_id . '-' . $item->flavour_id . '-' . $item->uom_id;
            })
            ->map(function($group) {
                return $group->sortByDesc('voucher_date')->first(); // still keeps latest if multiple in same date
            });
            //->values();
        
        // Ab data array bana do
        foreach ($result as $item) {
            $main_qty = '';
            $main_amount = 0;
            if(in_array($item->stock_type,[1])){
                
                 $productPriceList = $productPrices[$item->product_id] ?? collect();

                // Filter only prices which are valid for voucher_date
                $filteredPrices = $productPriceList
                    ->where('start_date', '<=', $item->voucher_date)
                    ->groupBy('uom_id')
                    ->map(function ($prices) {
                        // Get the latest price by start_date
                        return $prices->sortByDesc('start_date')->first();
                    });
            
                foreach ($filteredPrices as $productPrice) {
                    
                    $stock_key = $item->product_id . '-' . $item->flavour_id . '-' . $productPrice->uom_id;
                    $qty = $stocks[$stock_key]->qty ?? 0;
                    $uom_name = $uoms[$productPrice->uom_id]->uom_name ?? '--';
                    
                    $pcs_per_carton = $productPcsPerCarton[$item->product_id][0]->pcs_per_carton ?? 0;

                    $main_qty .= ($main_qty ? ' , ' : '') . $qty . '=>' . $uom_name . '=>'. $pcs_per_carton;
                    $value = $qty * $productPrice->trade_price;
                    $main_amount += $value;
                    
                
                }
                
                $preparedData[$item->product_id][$item->flavour_id] = [
                    'main_qty' => $main_qty,
                    'main_amount' => $main_amount,
                ];
            } 
        }
        
        
        return $preparedData;
    }
  public static function get_stock_type_wise_data_opening_detail($product_id, $flavour_id , $stock_type,$from,$to)
    {
        $main_qty = '';
        $main_amount = 0;
        foreach (self::get_product_price($product_id) as $k => $productPrice) {
            $qty = self::get_Stock_by_stock_type_opening_detail($product_id, $flavour_id , $productPrice->uom_id , $stock_type,$from,$to);

            $uom_name = self::uom_name($productPrice->uom_id); // Get UOM name for each product_price UOM
            //if ($qty > 0) {
		$pcs_per_carton = ProductPrice::select('pcs_per_carton')->where('product_id' , $product_id)->where('uom_id','!=',7)->where('status', 1)->where('start_date' ,'<=', date('Y-m-d'))->orderBy('start_date','desc')->value('pcs_per_carton');
               
		$main_qty .= ($main_qty ? ' , ' : '') . $qty . '=>' . $uom_name . '=>'. $pcs_per_carton;
                $value = $qty * $productPrice->trade_price;
                $main_amount += $value;
            //}
        }

        return ['main_qty' => $main_qty , 'main_amount' => $main_amount];

    }
  public static function get_stock_type_wise_data_opening_detail1($product_id, $flavour_id , $stock_type,$from,$to,$city)
    {
        $main_qty = '';
        $main_amount = 0;
        foreach (self::get_product_price($product_id) as $k => $productPrice) {
            $qty = self::get_Stock_by_stock_type_opening_detail1($product_id, $flavour_id , $productPrice->uom_id , $stock_type,$from,$to,$city);

            $uom_name = self::uom_name($productPrice->uom_id); // Get UOM name for each product_price UOM
            //if ($qty > 0) {
		$pcs_per_carton = ProductPrice::select('pcs_per_carton')->where('product_id' , $product_id)->where('uom_id','!=',7)->where('status', 1)->where('start_date' ,'<=', date('Y-m-d'))->orderBy('start_date','desc')->value('pcs_per_carton');
               
		$main_qty .= ($main_qty ? ' , ' : '') . $qty . '=>' . $uom_name . '=>'. $pcs_per_carton;
                $value = $qty * $productPrice->trade_price;
                $main_amount += $value;
            //}
        }

        return ['main_qty' => $main_qty , 'main_amount' => $main_amount];

    }


      public static function prepare_stock_type_data($result, $stockTypes, $from, $to,$type,$city)
    {
        $preparedData = [];

        $productIds = $result->pluck('product_id')->unique()->toArray();
        $flavourIds = $result->pluck('flavour_id')->unique()->toArray();
        if($type=='detail'){
            $distributorIds = $result->pluck('distributor_id')->unique()->toArray();
        }else{
            //$cityIds = $result->pluck('city_id')->unique()->toArray();
           
        }
        // $cityIds =$city;
        $cityIds = is_array($city) ? $city : (empty($city) ? [] : explode(',', $city));
        $productPrices = ProductPrice::
            whereIn('product_id', $productIds)
            ->where('status', 1)
            ->where('start_date', '<=', date('Y-m-d'))
            ->orderBy('start_date', 'desc')
            ->get()
            ->groupBy('product_id');
         
        // $productPriceGrouped = $productPrices->groupBy(function ($item) {
        //     return $item->product_id . '-' . $item->uom_id;
        // });

        //$productPriceForProduct = $productPrices->groupBy('product_id');

        $uoms = UOM::all()->keyBy('id');

        $pcsPerCartonList = ProductPrice::select('product_id', 'uom_id', 'pcs_per_carton')
            ->whereIn('product_id', $productIds)
            ->where('uom_id', '!=', 7)
            ->where('status', 1)
           ->where('start_date', '<=', date('Y-m-d'))
            ->orderBy('start_date', 'desc')
            ->get()
            ->groupBy('product_id');
        if($type=='detail'){
            if($stockTypes[0]==3){
                
                $stocks = DB::table('sale_orders')
                            ->join('sale_order_data', 'sale_order_data.so_id', '=', 'sale_orders.id')
                            ->Join('distributors', 'distributors.id', '=', 'sale_orders.distributor_id')
                            ->where('sale_orders.status', 1)
                            ->where('sale_orders.excecution', 1)
                            ->when(!empty($productIds), function ($q) use ($productIds) {
                                $q->whereIn('sale_order_data.product_id', $productIds);
                            })
                            ->when(!empty($flavourIds), function ($q) use ($flavourIds) {
                                $q->whereIn('sale_order_data.flavour_id', $flavourIds);
                            })
                            ->when(!empty($cityIds), function ($q) use ($cityIds) {
                                $q->whereIn('distributors.city_id', [$cityIds]);
                            })
                            ->when(!empty($distributorIds), function ($q) use ($distributorIds) {
                                $q->whereIn('sale_orders.distributor_id', $distributorIds);
                            })
                            ->when(!empty($from), function ($q) use ($from) {
                                $q->where('sale_orders.dc_date', '>=', $from);
                            })
                            ->when(!empty($to), function ($q) use ($to) {
                                $q->where('sale_orders.dc_date', '<=', $to);
                            })
                            ->select(
                                'sale_order_data.product_id',
                                'sale_order_data.flavour_id',
                                'sale_order_data.sale_type',
                                'sale_orders.distributor_id',
                    'sale_orders.dc_date',
                    DB::raw('SUM(sale_order_data.total) as total_amount'),
                    DB::raw('SUM(sale_order_data.qty) as qty')
                )
                ->groupBy( 
                    'sale_order_data.product_id',
                    'sale_order_data.flavour_id',
                    'sale_order_data.sale_type',
                    'sale_orders.distributor_id'
                    //'sale_orders.dc_date' // optional: remove if not needed
                )
                ->get()
                ->groupBy(function ($item) {
                    return $item->product_id . '-' . $item->flavour_id . '-' . $item->sale_type . '-' . $item->distributor_id;
                });
            }elseif($stockTypes[0]==4){

                $stocks = DB::table('sale_order_returns')
                    ->join('sale_order_return_details', 'sale_order_returns.id', '=', 'sale_order_return_details.sale_order_return_id')
                    ->join('distributors', 'distributors.id', '=', 'sale_order_returns.distributor_id')
                    ->join('product_prices', 'product_prices.product_id', '=', 'sale_order_return_details.product_id')
                    ->join('product_flavours', 'product_flavours.product_id', '=', 'sale_order_return_details.product_id')
                    ->when(!empty($productIds), function ($q) use ($productIds) {
                        $q->whereIn('sale_order_return_details.product_id', $productIds);
                    })
                    ->when(!empty($flavourIds), function ($q) use ($flavourIds) {
                        $q->whereIn('product_flavours.id', $flavourIds);
                    })
                    ->when(!empty($cityIds), function ($q) use ($cityIds) {
                        $q->whereIn('distributors.city_id', $cityIds);
                    })
                    ->when(!empty($distributorIds), function ($q) use ($distributorIds) {
                        $q->whereIn('sale_order_returns.distributor_id', $distributorIds);
                    })
                    ->when(!empty($from) && !empty($to), function ($q) use ($from, $to) {
                        $q->whereBetween('sale_order_returns.return_date', [$from, $to]);
                    })
                    ->when(!empty($from) && empty($to), function ($q) use ($from) {
                        $q->where('sale_order_returns.return_date', '>=', $from);
                    })
                    ->when(empty($from) && !empty($to), function ($q) use ($to) {
                        $q->where('sale_order_returns.return_date', '<=', $to);
                    })
                    ->where('sale_order_returns.excecution', 1)
                    ->select(
                        'sale_order_return_details.product_id',
                        'product_flavours.id as flavour_id',
                        'sale_order_returns.distributor_id',
                        'product_prices.trade_price as rate',
                        DB::raw('SUM(sale_order_return_details.quantity) as qty')
                    )
                    ->groupBy(
                        'sale_order_return_details.product_id',
                        'product_flavours.id',
                        'sale_order_returns.distributor_id'
                    )
                    ->get()
                    ->groupBy(function ($item) {
                        return $item->product_id . '-' . $item->flavour_id . '-' . 1 . '-' . $item->distributor_id;
                    });
                    // dd($stocks, $from, $to);
                // $stocks = DB::table('sales_returns')
                //     ->join('sales_return_data', 'sales_returns.id', '=', 'sales_return_data.sales_return_id')
                //     ->join('sale_orders', 'sales_returns.so_id', '=', 'sale_orders.id')
                //     ->join('sale_order_data', 'sales_return_data.sales_order_data_id', '=', 'sale_order_data.id')
                //     ->Join('distributors', 'distributors.id', '=', 'sale_orders.distributor_id')
                //     ->when(!empty($productIds), function ($q) use ($productIds) {
                //         $q->whereIn('sale_order_data.product_id', $productIds);
                //     })
                //     ->when(!empty($flavourIds), function ($q) use ($flavourIds) {
                //         $q->whereIn('sale_order_data.flavour_id', $flavourIds);
                //     })
                //     ->when(!empty($cityIds), function ($q) use ($cityIds) {
                //         $q->whereIn('distributors.city_id', [$cityIds]);
                //     })
                //     ->when(!empty($distributorIds), function ($q) use ($distributorIds) {
                //         $q->whereIn('sale_orders.distributor_id', $distributorIds);
                //     })
                //     ->when(!empty($from), function ($q) use ($from) {
                //         $q->where('sale_orders.dc_date', '>=', $from);
                //     })
                //     ->when(!empty($to), function ($q) use ($to) {
                //         $q->where('sale_orders.dc_date', '<=', $to);
                //     })
                //     ->where('sales_returns.excute', 1)
                //     ->where('sale_orders.excecution', 1)
                //     ->select(
                //         'sale_order_data.product_id',
                //         'sale_order_data.flavour_id',
                //         'sale_orders.distributor_id',
                //         'sale_order_data.sale_type',
                //         'sale_orders.dc_date',
                //         'sale_order_data.rate',
                //         //DB::raw('SUM(sales_returns.amount) as total_amount'),
                //         DB::raw('SUM(sales_return_data.qty) as qty')
                //     )
                //     ->groupBy(
                //         'sale_order_data.product_id',
                //         'sale_order_data.flavour_id',
                //         'sale_order_data.sale_type',
                //         'sale_orders.distributor_id',
                //         //'sale_orders.tso_id',
                //         //'sale_orders.dc_date'
                //     )
                //     ->get()
                //     // ->groupBy(function ($item) {
                //     //     return implode('_', [
                //     //         $item->product_id,
                //     //         $item->flavour_id,
                //     //         $item->distributor_id,
                            
                //     //     ]);
                //     // });
                //     ->groupBy(function ($item) {
                //         return $item->product_id . '-' . $item->flavour_id . '-' . $item->sale_type . '-' . $item->distributor_id;
                //     });


            }
            else{
                $stocks = Stock::whereIn('product_id', $productIds)
                ->whereIn('flavour_id', $flavourIds)
                ->whereIn('distributor_id', $distributorIds)
                ->whereIn('stock_type', $stockTypes)
                ->where('status', 1)
                ->when(!empty($from), function ($q) use ($from) {
                    $q->where('voucher_date', '>=', $from);
                })
                ->when(!empty($to), function ($q) use ($to) {
                    $q->where('voucher_date', '<=', $to);
                })
                ->get()
                ->groupBy(function ($item) {
                    return  $item->product_id . '-' . $item->flavour_id . '-' . $item->uom_id . '-' . $item->distributor_id;
                });
            }


            
        }else{

            if($stockTypes[0]==3){
                $stocks = DB::table('sale_orders')
                    ->join('sale_order_data', 'sale_order_data.so_id', '=', 'sale_orders.id')
                    ->Join('distributors', 'distributors.id', '=', 'sale_orders.distributor_id')
                    ->where('sale_orders.status', 1)
                    ->where('sale_orders.excecution', 1)
                    ->when(!empty($productIds), function ($q) use ($productIds) {
                        $q->whereIn('sale_order_data.product_id', $productIds);
                    })
                    ->when(!empty($cityIds), function ($q) use ($cityIds) {
                        $q->whereIn('distributors.city_id', [$cityIds]);
                    })
                    ->when(!empty($flavourIds), function ($q) use ($flavourIds) {
                        $q->whereIn('sale_order_data.flavour_id', $flavourIds);
                    })
                    
                    ->when(!empty($from), function ($q) use ($from) {
                        $q->where('sale_orders.dc_date', '>=', $from);
                    })
                    ->when(!empty($to), function ($q) use ($to) {
                        $q->where('sale_orders.dc_date', '<=', $to);
                    })
                    ->select(
                        'sale_order_data.product_id',
                        'sale_order_data.flavour_id',
                        'sale_order_data.sale_type',
                        'sale_orders.dc_date',
                        DB::raw('SUM(sale_order_data.total) as total_amount'),
                        DB::raw('SUM(sale_order_data.qty) as qty')
                    )
                    ->groupBy(
                        'sale_order_data.product_id',
                        'sale_order_data.flavour_id',
                        'sale_order_data.sale_type',
                        
                        //'sale_orders.dc_date' // optional: remove if not needed
                    )
                    ->get()
                    ->groupBy(function ($item) {
                        return $item->product_id . '-' . $item->flavour_id . '-' . $item->sale_type;
                    });
            }elseif($stockTypes[0]==4){
                $stocks = DB::table('sale_order_returns')
                    ->join('sale_order_return_details', 'sale_order_returns.id', '=', 'sale_order_return_details.sale_order_return_id')
                    ->join('distributors', 'distributors.id', '=', 'sale_order_returns.distributor_id')
                    ->join('product_prices', 'product_prices.product_id', '=', 'sale_order_return_details.product_id')
                    ->join('product_flavours', 'product_flavours.product_id', '=', 'sale_order_return_details.product_id')
                    ->when(!empty($productIds), function ($q) use ($productIds) {
                        $q->whereIn('sale_order_return_details.product_id', $productIds);
                    })
                    ->when(!empty($flavourIds), function ($q) use ($flavourIds) {
                        $q->whereIn('product_flavours.id', $flavourIds);
                    })
                    ->when(!empty($cityIds), function ($q) use ($cityIds) {
                        $q->whereIn('distributors.city_id', $cityIds);
                    })
                    ->when(!empty($distributorIds), function ($q) use ($distributorIds) {
                        $q->whereIn('sale_order_returns.distributor_id', $distributorIds);
                    })
                    ->when(!empty($from) && !empty($to), function ($q) use ($from, $to) {
                        $q->whereBetween('sale_order_returns.return_date', [$from, $to]);
                    })
                    ->when(!empty($from) && empty($to), function ($q) use ($from) {
                        $q->where('sale_order_returns.return_date', '>=', $from);
                    })
                    ->when(empty($from) && !empty($to), function ($q) use ($to) {
                        $q->where('sale_order_returns.return_date', '<=', $to);
                    })
                    ->where('sale_order_returns.excecution', 1)
                    ->select(
                        'sale_order_return_details.product_id',
                        'product_flavours.id as flavour_id',
                        'sale_order_returns.distributor_id',
                        'product_prices.trade_price as rate',
                        DB::raw('SUM(sale_order_return_details.quantity) as qty')
                    )
                    ->groupBy(
                        'sale_order_return_details.product_id',
                        'product_flavours.id',
                        'sale_order_returns.distributor_id'
                    )
                    ->get()
                    ->groupBy(function ($item) {
                        return $item->product_id . '-' . $item->flavour_id . '-' . 1;
                    });
                // $stocks = DB::table('sales_returns')
                //     ->join('sales_return_data', 'sales_returns.id', '=', 'sales_return_data.sales_return_id')
                //     ->join('sale_orders', 'sales_returns.so_id', '=', 'sale_orders.id')
                //     ->join('sale_order_data', 'sales_return_data.sales_order_data_id', '=', 'sale_order_data.id')
                //     ->Join('distributors', 'distributors.id', '=', 'sale_orders.distributor_id')
                //     ->when(!empty($productIds), function ($q) use ($productIds) {
                //         $q->whereIn('sale_order_data.product_id', $productIds);
                //     })
                //     ->when(!empty($cityIds), function ($q) use ($cityIds) {
                //         $q->whereIn('distributors.city_id', [$cityIds]);
                //     })
                //     ->when(!empty($flavourIds), function ($q) use ($flavourIds) {
                //         $q->whereIn('sale_order_data.flavour_id', $flavourIds);
                //     })
                //     ->when(!empty($from), function ($q) use ($from) {
                //         $q->where('sale_orders.dc_date', '>=', $from);
                //     })
                //     ->when(!empty($to), function ($q) use ($to) {
                //         $q->where('sale_orders.dc_date', '<=', $to);
                //     })
                //     ->where('sales_returns.excute', 1)
                //     ->where('sale_orders.excecution', 1)
                //     ->select(
                //         'sale_order_data.product_id',
                //         'sale_order_data.flavour_id',
                //         'sale_order_data.sale_type',
                //         'sale_order_data.rate',
                //         'sale_orders.dc_date',
                //         //DB::raw('SUM(sales_returns.amount) as total_amount'),
                //         DB::raw('SUM(sales_return_data.qty) as qty')
                //     )
                //     ->groupBy(
                //         'sale_order_data.product_id',
                //         'sale_order_data.flavour_id',
                //         'sale_order_data.sale_type',
                        
                //         //'sale_orders.tso_id',
                //         //'sale_orders.dc_date'
                //     )
                //     ->get()
                //     // ->groupBy(function ($item) {
                //     //     return implode('_', [
                //     //         $item->product_id,
                //     //         $item->flavour_id,
                //     //         $item->distributor_id,
                            
                //     //     ]);
                //     // });
                //     ->groupBy(function ($item) {
                //         return $item->product_id . '-' . $item->flavour_id . '-' . $item->sale_type;
                //     });


            }
            else{

            $stocks = Stock::Join('distributors', 'distributors.id', '=', 'stocks.distributor_id')
                ->select('stocks.*')
                ->whereIn('stocks.product_id', $productIds)
                ->whereIn('stocks.flavour_id', $flavourIds)
                ->when(!empty($cityIds), function ($q) use ($cityIds) {
                    $q->whereIn('distributors.city_id', [$cityIds]);
                })
                ->whereIn('stocks.stock_type', $stockTypes)
                ->where('stocks.status', 1)
                ->when(!empty($from), function ($q) use ($from) {
                    $q->where('stocks.voucher_date', '>=', $from);
                })
                ->when(!empty($to), function ($q) use ($to) {
                    $q->where('stocks.voucher_date', '<=', $to);
                })
                ->get()
                ->groupBy(function ($item) {
                    return  $item->product_id . '-' . $item->flavour_id . '-' . $item->uom_id;
                });
            }
        }        

        foreach ($result as $item) {
            $main_qty = '';
            $main_amount = 0;
            $sales_amount=0;
            //$productPriceList = $productPriceForProduct[$item->product_id] ?? [];
            $productPriceList = $productPrices[$item->product_id] ?? collect();
            
            // Filter only prices which are valid for voucher_date
            $filteredPrices = $productPriceList
                ->where('start_date', '<=', $item->voucher_date)
                ->groupBy('uom_id')
                ->map(function ($prices) {
                    // Get the latest price by start_date
                    return $prices->sortByDesc('start_date')->first();
                });
            // $sales_amount_exe = DB::table('sale_orders')->join('sale_order_data', 'sale_order_data.so_id', '=', 'sale_orders.id')
            // ->where('sale_order_data.sale_type', $sales_count)
            // ->where('sale_orders.excecution', 1)
            // ->sum('sale_order_data.total');
            foreach ($filteredPrices as $productPrice) {
                if($type=='detail'){
                    $stock_key = $item->product_id . '-' . $item->flavour_id . '-' . $productPrice->uom_id . '-' . $item->distributor_id;
                }else{
                    $stock_key = $item->product_id . '-' . $item->flavour_id . '-' . $productPrice->uom_id;
                    
                }
                $qty = isset($stocks[$stock_key]) ? $stocks[$stock_key]->sum('qty') : 0;
                $sales_amount += isset($stocks[$stock_key]) ? $stocks[$stock_key]->sum('total_amount') : 0;
                $uom_name = $uoms[$productPrice->uom_id]->uom_name ?? '--';
                // $pcs_key = $item->product_id . '-' . $productPrice->uom_id;
                $pcs_per_carton = $pcsPerCartonList[$item->product_id][0]->pcs_per_carton ?? 0;

                $main_qty .= ($main_qty ? ' , ' : '') . $qty . '=>' . $uom_name . '=>' . $pcs_per_carton;
                
                if($stockTypes[0]==4){
                    $rate=isset($stocks[$stock_key]) ? $stocks[$stock_key]->sum('rate') : $productPrice->trade_price;
                    $main_amount += $qty * $rate;
                }else{
                    $main_amount += $qty * $productPrice->trade_price;
                }
                
            }

            if($type=='detail'){
                if($stockTypes[0]==3){
                    
                    $preparedData[$item->product_id][$item->flavour_id][$item->distributor_id] = [
                        'main_qty' => $main_qty ?: '--',
                        'main_amount' => $sales_amount,
                    ];
                }else{
                    $preparedData[$item->product_id][$item->flavour_id][$item->distributor_id] = [
                        'main_qty' => $main_qty ?: '--',
                        'main_amount' => $main_amount,
                    ];
                }
                
            }else{
                $preparedData[$item->product_id][$item->flavour_id] = [
                    'main_qty' => $main_qty ?: '--',
                    'main_amount' => $main_amount,
                ];
            }
        }
         
        return $preparedData;
    }



 public static function get_sales_qty_from_orders($product_id, $flavour_id, $distributor_id, $to_date)
{
    $saleorder = DB::table('sale_orders')
        ->join('sale_order_data', 'sale_order_data.so_id', '=', 'sale_orders.id')
        ->join('distributors', 'distributors.id', '=', 'sale_orders.distributor_id')
        ->where('sale_orders.status', 1)
        ->where('sale_orders.excecution', 1)
        ->where('sale_order_data.product_id', $product_id)
        ->where('sale_order_data.flavour_id', $flavour_id)
        ->where('sale_orders.distributor_id', $distributor_id)
        ->where('sale_orders.dc_date', '<=', $to_date) // ✅ Fixed
        ->sum('sale_order_data.qty');

  //  dd($saleorder); // just for testing

    return $saleorder;
}



    public static function get_Stock_by_stock_type_with_date($product_id, $flavour_id, $uom_id, $distributor, $stock_type, $from, $to)
    {
        $data = false;
        $from='';
        $stockTypeOneQty = 0; // Initialize stock_type 1 qty


    
        // Check if stock_type = 1 exists in the array
        if (in_array(1, $stock_type)) {
            // Get the latest record for stock_type = 1
            $stockTypeOneQty = Stock::where('stock_type', 1)
                ->where('status', 1)
                ->where('product_id', $product_id)
                ->where('flavour_id', $flavour_id)
                ->where('uom_id', $uom_id)
                ->when($distributor != null, function ($q) use ($distributor) {
                    $q->where('distributor_id', $distributor);
                })
                ->when(!empty($from), function ($q) use ($from) {
                    $q->where('voucher_date', '>=',$from);
                })
                ->when(!empty($to), function ($q) use ($to) {
                    $q->where('voucher_date', '<=',$to);
                })
                ->orderBy('updated_at', 'desc') // Get the latest record
                ->value('qty'); // Fetch the qty value
        }
	
	  	


        // Remove stock_type = 1 from the array
        $filteredStockType = array_filter($stock_type, function ($type) {
            return $type != 1;
        });
    
        // Get the sum of qty for other stock types
        $otherStockTypeQty = Stock::whereIn('stock_type', $filteredStockType)
            ->where('status', 1)
            ->where('product_id', $product_id)
            ->where('flavour_id', $flavour_id)
            ->where('uom_id', $uom_id)
            ->when($distributor != null, function ($q) use ($distributor) {
                $q->where('distributor_id', $distributor);
            })
            ->when(!empty($from), function ($q) use ($from) {
                $q->where('voucher_date', '>=',$from);
            })
            ->when(!empty($to), function ($q) use ($to) {
                $q->where('voucher_date', '<=',$to);
            })
            ->sum('qty');
    
        // Add both quantities
        $totalQty = $stockTypeOneQty + $otherStockTypeQty;
    
        return $totalQty;
    }
 public static function get_stock_type_wise_data_with_date($product_id, $flavour_id ,$distributor , $stock_type,$from,$to) 
    {
        $main_qty = '';
        $main_amount = 0;

        foreach (self::get_product_price($product_id) as $k => $productPrice) {
            $qty = self::get_Stock_by_stock_type_with_date($product_id, $flavour_id , $productPrice->uom_id , $distributor , $stock_type,$from,$to);
	  

  $pcs_per_carton = ProductPrice::select('pcs_per_carton')->where('product_id' , $product_id)->where('uom_id','!=',7)->where('status', 1)->where('start_date' ,'<=', date('Y-m-d'))->orderBy('start_date','desc')->value('pcs_per_carton');
            $uom_name = self::uom_name($productPrice->uom_id); // Get UOM name for each product_price UOM
            //if ($qty > 0) {
                $main_qty .= ($main_qty ? ' , ' : '') . $qty . '=>' . $uom_name . '=>'. $pcs_per_carton;
                $value = $qty * $productPrice->trade_price;
                $main_amount += $value;
            //}
        }

        return ['main_qty' => $main_qty , 'main_amount' => $main_amount];

    }




 public static function get_stock_type_wise_data_with_date_ordersalecheck($product_id, $flavour_id ,$distributor , $stock_type,$from,$to) 
    {
        $main_qty = '';
        $main_amount = 0;

        foreach (self::get_product_price($product_id) as $k => $productPrice) {
            $qty = self::get_sales_qty_from_orders($product_id, $flavour_id ,$distributor ,$to);
	  

  $pcs_per_carton = ProductPrice::select('pcs_per_carton')->where('product_id' , $product_id)->where('uom_id','!=',7)->where('status', 1)->where('start_date' ,'<=', date('Y-m-d'))->orderBy('start_date','desc')->value('pcs_per_carton');
            $uom_name = self::uom_name($productPrice->uom_id); // Get UOM name for each product_price UOM
            //if ($qty > 0) {
                $main_qty .= ($main_qty ? ' , ' : '') . $qty . '=>' . $uom_name . '=>'. $pcs_per_carton;
                $value = $qty * $productPrice->trade_price;
                $main_amount += $value;
            //}
        }

        return ['main_qty' => $main_qty , 'main_amount' => $main_amount];

    }

 public static function get_stock_type_wise_data_with_date_sale($product_id, $flavour_id ,$distributor , $stock_type,$from,$to) 
    {
        $main_qty = '';
        $main_amount = 0;
        foreach (self::get_product_price($product_id) as $k => $productPrice) {
            $qty = self::get_Stock_by_stock_type_with_date_sale($product_id, $flavour_id , $productPrice->uom_id , $distributor , $stock_type,$from,$to);
	    $pcs_per_carton = ProductPrice::select('pcs_per_carton')->where('product_id' , $product_id)->where('uom_id','!=',7)->where('status', 1)->where('start_date' ,'<=', date('Y-m-d'))->orderBy('start_date','desc')->value('pcs_per_carton');
            $uom_name = self::uom_name($productPrice->uom_id); // Get UOM name for each product_price UOM
            //if ($qty > 0) {
                $main_qty .= ($main_qty ? ' , ' : '') . $qty . '=>' . $uom_name . '=>'. $pcs_per_carton;
                $value = $qty * $productPrice->trade_price;
                $main_amount += $value;
            //}
        }

        return ['main_qty' => $main_qty , 'main_amount' => $main_amount];

    }

public static function get_Stock_by_stock_type_with_date_sale($product_id, $flavour_id, $uom_id, $distributor, $stock_type, $from, $to)
{
    $totalQty = 0;
    $from = $from ?? ''; // Ensure it's a string if null
    $to = $to ?? '';

 dd($product_id, $flavour_id, $uom_id, $distributor, $stock_type, $from, $to);
        $saleQty = DB::table('sale_orders')
            ->join('sale_order_data', 'sale_order_data.so_id', '=', 'sale_orders.id')
            ->join('distributors', 'distributors.id', '=', 'sale_orders.distributor_id')
            ->where('sale_orders.status', 1)
            ->where('sale_orders.excecution', 1)
            ->where('sale_order_data.product_id', $product_id)
            ->where('sale_order_data.flavour_id', $flavour_id)
          //  ->where('sale_order_data.uom_id', $uom_id)
 ->where('sale_orders.excecution',1)
            ->when($distributor, function ($q) use ($distributor) {
                $q->where('sale_orders.distributor_id', $distributor);
            })
            ->when(!empty($from), function ($q) use ($from) {
                $q->where('sale_orders.dc_date', '>=', $from);
            })
            ->when(!empty($to), function ($q) use ($to) {
                $q->where('sale_orders.dc_date', '<=', $to);
            })
            ->sum('sale_order_data.qty');

        $totalQty = $saleQty;
   
    return $totalQty;
}


    public static function get_stock_type_wise_data($product_id, $flavour_id ,$distributor , $stock_type)
    {
        $main_qty = '';
        $main_amount = 0;
        foreach (self::get_product_price($product_id) as $k => $productPrice) {
            $qty = self::get_Stock_by_stock_type($product_id, $flavour_id , $productPrice->uom_id , $distributor , $stock_type);

            $uom_name = self::uom_name($productPrice->uom_id); // Get UOM name for each product_price UOM
            if ($qty > 0) {
                $main_qty .= ($main_qty ? ' , ' : '') . number_format($qty) . 'x' . $uom_name;
                $value = $qty * $productPrice->trade_price;
                $main_amount += $value;
            }
        }

        return ['main_qty' => $main_qty , 'main_amount' => $main_amount];

    }

    public static function get_users_distributors($id)
    {
		
		return User::find($id)->distributors()->pluck('distributor_id');
    }
    public  function get_assign_user()
    {
        $distributors = $this->get_users_distributors(Auth::user()->id);

        return UsersDistributors::whereIn('distributor_id',$distributors)->groupBy('user_id')->pluck('user_id');
    }

    public function get_tso_distribuor_wise()
    {

        return  TSO::whereIn('user_id', $this->get_assign_user())->Status()->get();
    }
    public static function get_tso_name($id)
    {
        return TSO::where('id' , $id)->status()->value('name');
    }
    public function get_route_distribuor_wise()
    {
        return  Route::status()->join('users_distributors as b', 'routes.distributor_id', '=', 'b.distributor_id')
            ->where('b.user_id', Auth::user()->id)
            ->select('routes.*')
            ->get();
    }
    public function get_shop_distribuor_wise()
    {
        return  Shop::status()->join('users_distributors as b', 'shops.distributor_id', '=', 'b.distributor_id')
            ->where('b.user_id', Auth::user()->id)
            ->select('shops.*')
            ->get();
    }
    public function get_shop_distribuor_wise_count()
    {
        return  Shop::status()->join('users_distributors as b', 'shops.distributor_id', '=', 'b.distributor_id')
            ->where('b.user_id', Auth::user()->id)
            ->select('shops.*');
    }
    public function get_sale_return_amount($invoice_no)
    {
        // Fetch the sale_order_data ID
        $sales = SaleOrder::where('invoice_no', $invoice_no)
            ->join('sale_order_data', 'sale_orders.id', '=', 'sale_order_data.so_id')
            ->select('sale_order_data.id as sale_order_data_id') // Selecting the required column from the joined table
            ->first();
    
       
        if (!$sales) {
            return 0;
        }
    
     
        $sales_return_ids = SalesReturnData::where('sales_order_data_id', $sales->sale_order_data_id)
            ->pluck('sales_return_id');
    
        if ($sales_return_ids->isEmpty()) {
            return 0;
        }
    
      
        $total_amount = SalesReturn::whereIn('id', $sales_return_ids)
            ->sum('amount');
    
        return $total_amount > 0 ? $total_amount : 0;
    }
    
    

    // public static function get_sales_orders($request)
    // {
    //     $from = $request->from;
    //     $to = $request->to;

    //  // dd($request->distributor_id);

    //     $sales = SaleOrder::join('users_distributors as b', 'b.distributor_id', '=', 'sale_orders.distributor_id')
    //         ->Status()
    //         ->where('b.user_id', auth()->user()->id)
    //         ->whereBetween('dc_date', [$from, $to])
    //         ->when($request->execution != null, function ($query) use ($request) {
    //             $query->where('excecution', $request->execution);
    //         })
    //         ->when($request->distributor_id != null, function ($query) use ($request) {
    //             $query->where('sale_orders.distributor_id', $request->distributor_id);
    //         })
    //         ->when($request->tso_id != null, function ($query) use ($request) {
    //             $query->where('sale_orders.tso_id', $request->tso_id);


    //         })->when($request->city != null, function ($query) use ($request) {
    //             $query->whereHas('tso.cities',function ($quer) use ($request){
    //                 $quer->where('id',$request->city);
    //             });


    //         })
    //         ->select('sale_orders.*')
    //         ->get();

    //     //    dd($sales);

    //     return $sales;
    // }   
public static function get_sales_orders($request)
{
    $sales = SaleOrder::join('users_distributors as b', 'b.distributor_id', '=', 'sale_orders.distributor_id')
        ->Status()
        ->where('b.user_id', auth()->user()->id)
        
        // ✅ Apply date filter only if from or to is given
        ->when($request->from || $request->to, function ($query) use ($request) {
            $from = $request->from ?? '1900-01-01';
            $to = $request->to ?? now()->format('Y-m-d');
            $query->whereBetween('dc_date', [$from, $to]);
        })

        ->when($request->search, function ($query) use ($request) {
            $query->where(function ($q) use ($request) {
                $q->where('invoice_no', 'LIKE', "%{$request->search}%")
                  ->orWhereHas('distributor', function ($q) use ($request) {
                      $q->where('distributor_name', 'LIKE', "%{$request->search}%");
                  })
                  ->orWhereHas('tso', function ($q) use ($request) {
                      $q->where('name', 'LIKE', "%{$request->search}%");
                  })
                  ->orWhereHas('shop', function ($q) use ($request) {
                      $q->where('company_name', 'LIKE', "%{$request->search}%");
                  });
            });
        })

        ->when($request->execution != null, function ($query) use ($request) {
            $query->where('excecution', $request->execution);
        })

        ->when($request->distributor_id != null, function ($query) use ($request) {
            $query->where('sale_orders.distributor_id', $request->distributor_id);
        })

        ->when($request->tso_id != null, function ($query) use ($request) {
            $query->where('sale_orders.tso_id', $request->tso_id);
        })

        ->when($request->city != null, function ($query) use ($request) {
            $query->whereHas('tso.cities', function ($q) use ($request) {
                $q->where('id', $request->city);
            });
        })

        ->select('sale_orders.*')
        ->get();

    return $sales;
}

    public function get_sales_orders_return($request)
    {
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
        ->when($request->from && $request->to, function ($query) use ($request) {
            $query->whereHas('salesorder', function ($orderQuery) use ($request) {
                $orderQuery->whereBetween('dc_date', [$request->from, $request->to]);
            });
        })
        ->get();
        
        return $sales_return;
    }
  // public static function get_sales_orders_return_excute($request)
    // {
    //     $sales_return = SalesReturn::query()
    //     ->when($request->city, function ($query) use ($request) {
    //         $query->whereHas('salesorder.tso.cities', function ($cityQuery) use ($request) {
    //             $cityQuery->where('id', $request->city);
    //         });
    //     })
    //     ->when($request->distributor_id, function ($query) use ($request) {
    //         $query->whereHas('salesorder', function ($orderQuery) use ($request) {
    //             $orderQuery->where('distributor_id', $request->distributor_id);
    //         });
    //     })
    //     ->when($request->tso_id, function ($query) use ($request) {
    //         $query->whereHas('salesorder.tso', function ($tsoQuery) use ($request) {
    //             $tsoQuery->where('id', $request->tso_id);
    //         });
    //     })
    //     ->when($request->from && $request->to, function ($query) use ($request) {
    //         $query->whereHas('salesorder', function ($orderQuery) use ($request) {
    //             $orderQuery->whereBetween('dc_date', [$request->from, $request->to]);
    //         });
    //     })
    //     ->where('excute', false)
    //     ->get();

    //     return $sales_return;
    // }


  public static function get_sales_orders_return_excute($request)
{
    return SalesReturn::query()
        ->with(['salesorder.tso.cities', 'salesorder.distributor', 'salesorder.shop'])
        // Search filter
        ->when($request->search, function ($query) use ($request) {
            $query->where(function ($q) use ($request) {
                $q->where('voucher_no', 'LIKE', "%{$request->search}%") // Changed from sales_return_no to voucher_no
                  ->orWhereHas('salesorder', function ($orderQuery) use ($request) {
                      $orderQuery->where('invoice_no', 'LIKE', "%{$request->search}%");
                  })
                  ->orWhereHas('salesorder.distributor', function ($distQuery) use ($request) {
                      $distQuery->where('distributor_name', 'LIKE', "%{$request->search}%");
                  })
                  ->orWhereHas('salesorder.tso', function ($tsoQuery) use ($request) {
                      $tsoQuery->where('name', 'LIKE', "%{$request->search}%");
                  })
                  ->orWhereHas('salesorder.shop', function ($shopQuery) use ($request) {
                      $shopQuery->where('company_name', 'LIKE', "%{$request->search}%");
                  });
            });
        })
        // City filter
        ->when($request->city, function ($query) use ($request) {
            $query->whereHas('salesorder.tso.cities', function ($q) use ($request) {
                $q->where('id', $request->city);
            });
        })
        // Distributor filter
        ->when($request->distributor_id, function ($query) use ($request) {
            $query->whereHas('salesorder', function ($q) use ($request) {
                $q->where('distributor_id', $request->distributor_id);
            });
        })
        // TSO filter
        ->when($request->tso_id, function ($query) use ($request) {
            $query->whereHas('salesorder.tso', function ($q) use ($request) {
                $q->where('id', $request->tso_id);
            });
        })
        // Date range filter
        ->when($request->from && $request->to, function ($query) use ($request) {
            $query->whereHas('salesorder', function ($q) use ($request) {
                $q->whereBetween('dc_date', [$request->from, $request->to]);
            });
        })
        // Execution status filter
        ->where('excute', false)
        ->orderBy('created_at', 'desc')
        ->get();
}


    public static function get_sale_qty($from , $to , $product_id , $flavour_id , $uom_id , $tso , $distributor , $execution)
    {
        $qty = DB::table('sale_orders')->join('sale_order_data', 'sale_order_data.so_id', 'sale_orders.id')
        ->where('sale_orders.status', 1)
        ->where('sale_order_data.product_id', $product_id);
        if (isset($flavour_id) && $flavour_id != 0) {
            $qty = $qty->where('sale_order_data.flavour_id', $flavour_id);
        }
        $qty = $qty->where('sale_order_data.sale_type', $uom_id)
        ->whereBetween('sale_orders.dc_date', [$from, $to]);
        if (isset($tso)) {
            $qty = $qty->where('sale_orders.tso_id', $tso);
        }
        if (isset($distributor)) {
            $qty = $qty->where('sale_orders.distributor_id', $distributor);
        }
        if (isset($execution)) {
            $qty = $qty->where('sale_orders.excecution', $execution);
        }
        $qty = $qty->sum('sale_order_data.qty');
        return $qty;
    }

    public static function get_sale_qty2($from , $to , $product_id , $flavour_id , $uom_id , $tso , $distributor , $city , $execution = null)
    {
        // dd($from , $to , $product_id , $flavour_id , $uom_id , $tso , $distributor , $city);
        $qty = DB::table('sale_orders')->join('sale_order_data', 'sale_order_data.so_id', 'sale_orders.id')
        ->join('tso', 'tso.id' , 'sale_orders.tso_id')
        ->where('sale_orders.status', 1)
        ->where('sale_order_data.product_id', $product_id)
        ->where('sale_order_data.flavour_id', $flavour_id)
        ->where('sale_order_data.sale_type', $uom_id)
        ->whereBetween('sale_orders.dc_date', [$from, $to]);
        if (isset($tso)) {
            $qty = $qty->where('sale_orders.tso_id', $tso);
        }
        if (isset($city)) {
            $qty = $qty->where('tso.city', $city);
        }
        if (isset($distributor)) {
            $qty = $qty->where('sale_orders.distributor_id', $distributor);
        }
        if (isset($execution)) {
            $qty = $qty->where('sale_orders.excecution', $execution);
        }
        $qty = $qty->sum('sale_order_data.qty');
        return $qty;
    }

    public function get_receipt_voucher($request)
    {
        $from = $request->from;
        $to = $request->to;

        $rvs = ReceiptVoucher::join('users_distributors as b', 'b.distributor_id', '=', 'receipt_vouchers.distributor_id')
            ->where('b.user_id', auth()->user()->id)
            ->whereBetween('issue_date', [$from, $to])
            ->when($request->execution != null, function ($query) use ($request) {
                $query->where('execution', $request->execution);
            })
            ->select('receipt_vouchers.*')
            ->get();

        return $rvs;
    }

    public static function users_location_submit($obj, $lat, $lan, $table , $activity_type)
    {
		//dd($obj, $lat, $lan, $table , $activity_type);
        $user_location = new UsersLocation();
        $user_location->latitude = $lat;
        $user_location->longitude = $lan;
        $user_location->user_id = Auth::user()->id;
        $user_location->table_name = $table;
        $user_location->activity_type = $activity_type;
        $obj->usersLocation()->save($user_location);
    }

    public static function activity_log_submit($obj, $data, $table , $type , $title = null)
    {
        // dd(json_encode($data));
        if (!$obj || !is_object($obj)) {
            throw new \Exception('Invalid object provided for activity logging.');
        }
        $data = json_encode($data);
        $activity_log = new ActivityLog();

        $activity_log->title = $title;
        $activity_log->description = $data;
        $activity_log->table_name = $table;
        $activity_log->type = $type;
        $activity_log->activity_type = request()->method();
        $activity_log->date = date('Y-m-d');
        $activity_log->url = url()->full();

        $activity_log->user_name = Auth::user()->name;

        $obj->activityLog()->save($activity_log);
    }

    public static function get_user_type($id)
    {
        return Type::where('id', $id)->value('type');
    }

    public static function get_status_value()
    {
        return ['False', 'True'];
    }
    public static function get_active_value()
    {
        return ['Deactivate', 'Activate'];
    }
    public function cities()
    {
        return  City::where('status', 1)->get();
    }

    public static function PrintHead($from, $to, $report, $tso_id)
    {
        $data = '
     <div class="row">

       <div class="col-md-4">&nbsp
       </div>
                <div class="col-md-4" style="text-align:center">
                <h3>' . $report . '</h3>
                </div>
        <div class="col-md-4" style="text-align:right">
        Print Date: ' . date('Y-m-d') . '
        </div>
    </div>
    <div class="row">
    <div class="col-md-4">&nbsp
       </div>
       <div class="col-md-4" style="text-align:center">
       <h4>' . self::get_tso_name($tso_id) . '</h4>
       </div>
    </div>
    <div class="row">

    <div class="col-md-4">&nbsp
    </div>
             <div class="col-md-4" style="text-align:center">
           From: ' . $from . ' &nbsp  To: ' . $to . '
             </div>
     <div class="col-md-4" >
     &nbsp
     </div>
 </div>
    ';

        return $data;
    }

    public static function getAllPermissionList()
    {
        $permissions = Permission::query()
            ->select('main_module', 'name')
            ->groupBy('main_module')
            ->get()
            ->map(function ($permission) {
                return [
                    'main_module' => $permission->main_module,
                    'permissions' => $permission->where('main_module', $permission->main_module)->pluck('name','id')
                ];
            });
        // dd($permissions);
        return $permissions;
        // return Permission::select('id', 'name')->get();
    }

    public static function sidebarModules()
    {
        return [
            'User-Management',
            'Execution',
            'KPO',
            'Product',
            'Shop',
            'TSO',
            'Distributor',
            'Route',
            'Reports',
            'Setting',
            'Sub-Routes'
        ];
    }

    public static function stock_unique_no($type)
    {
        $count =  Stock::where('stock_type',$type)->groupBy('stock_type')->count();
       return $number = sprintf('%03d',$count);
    }



    public function get_all_role()
    {
       return Role::where('name', '!=', 'Super Admin')->get();
    }

    public static function Order_list_total_amount($type ,$id,$from=null,$to=null)
    {
        if ($type==0):
          return  DB::table('sale_order_data')->where('so_id',$id)->sum('total');
        else:
         return     DB::table('sale_orders as a')
            ->join('sale_order_data as b', 'a.id', '=', 'b.so_id')
            ->where('a.tso_id', $id)
            ->where('a.status', 1)
            ->whereBetween('a.dc_date', [$from, $to])
            ->groupBy('a.tso_id')
            ->sum('b.total');
        endif;

    }

    public static function sendSmsNotification($destinationnum , $text ,  $language = 'English', $responseType = 'text')
    {
        // $apiUrl = env('BIZSMS_API_URL');
        // $username = env('BIZSMS_USERNAME');
        // $password = env('BIZSMS_PASSWORD');
        // $masking = env('BIZSMS_MASKING');
        // $text = $text;
        // $destinationNumber = $destinationnum;

        // $url = "{$apiUrl}?username={$username}&pass={$password}&text={$text}&masking={$masking}&destinationnum={$destinationNumber}&language={$language}&responsetype={$responseType}";

        $url = env('BIZSMS_API_URL');
        $params = [
            'username' => env('BIZSMS_USERNAME'),
            'pass' => env('BIZSMS_PASSWORD'),
            'text' => $text,
            'masking' => env('BIZSMS_MASKING'),
            'destinationnum' => $destinationnum,
            'language' => $language,
            'responsetype' => $responseType
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
        }
        curl_close($ch);

        if (isset($error_msg)) {
            return $error_msg;
        }
        dd($response);
        return $response;
    }

    public static function correctPhoneNumber($number) {
        // Remove any non-numeric characters
        $number = preg_replace('/\D/', '', $number);

        // Check if the number starts with '92'
        if (substr($number, 0, 2) !== '92') {
            // If it starts with '0', replace the leading '0' with '92'
            if (substr($number, 0, 1) === '0') {
                $number = '92' . substr($number, 1);
            } else {
                // Otherwise, just prepend '92'
                $number = '92' . $number;
            }
        }
        return $number;
    }

    public static function uom_name($id)
    {
        $data = UOM::find($id);
        return $data->uom_name ?? '--';
    }
    public function get_uom($id)
    {
        $data = UOM::find($id);
        return $data ?? '--';
    }

    public function product_uom($id)
    {
        $product = Product::find($id);
        $uom_id = $product->uom_id;

        $data = UOM::find($uom_id);
        return $data->uom_name ?? '--';
    }

  public static function product_id_get_uom($id)
    {
        $data = ProductPrice::with('uom')
            ->where('product_id', $id)
            ->where('status', 1)
            ->get();
    
        $uoms = [];
        foreach ($data as $d) {
            if ($d->uom) { // Check if the UOM relationship exists
                $uoms[] = $d->uom->uom_name;
            }
        }
    
       
        return implode(', ', $uoms);
    }
    

    public function product_packing_uom($id)
    {
        $product = Product::find($id);
        $uom_id = $product->packing_uom_id;

        $data = UOM::find($uom_id);
        return $data->uom_name ?? '--';
    }

    public static function get_product_price($id)
    {
        // dd($id);
        $data = ProductPrice::with('uom')->where('product_id' , $id)->where('status', 1)->get();
        return $data;
    }

    public function product_price_by_uom_id($product_id , $uom_id)
    {
        // dd($id);
        $data = ProductPrice::with('uom')->where('product_id' , $product_id)->where('uom_id' , $uom_id)->where('status', 1)->first();
        return $data ?? '--';
    }

    public static function get_flavour_name($id)
    {
        // dd($id);
        $data = ProductFlavour::where('id',$id)->value('flavour_name');
        // dd($data);
        return $data;
    }

    public static function get_trade_price($product_id , $uom_id)
    {
        $data = ProductPrice::where('product_id' , $product_id)->where('uom_id' , $uom_id)->value('trade_price');
        return $data;
    }
    public function get_product_price_item($id)
    {
       
        $data = ProductPrice::where('product_id' , $id)->where('status', 1)->first();
        return $data->trade_price;
    }
    public static function get_config($config_key)
    {
        $config=Config::where('config_key',$config_key)->first();
        return (!empty($config))?$config->config_value:'';
    }

    public static function get_tso_max_limit()
    {
        $max_limit = self::get_config('tso_max_limit');
        $tso_count = TSO::Status()->Active()->count();
        // $tso_count = 100;
        // dd($max_limit  , $tso_count , $max_limit > $tso_count);
        if ($max_limit > $tso_count) {
            return true;
        }
        else
        {
            return false;
        }

    }

    public static function get_returned_qty_by_sale_order_id($distributor_id, $tso_id, $shop_id, $start = null, $end = null)
    {
        $returnQuery = DB::table('sale_order_returns as sr')
            ->where('sr.distributor_id', $distributor_id)
            ->where('sr.tso_id', $tso_id)
            ->where('sr.shop_id', $shop_id)
            ->where('sr.excecution', 1)
            ->where('sr.status', 1);
            //   ->whereBetween('sr.return_date', [$start, $end]);
            // $test = $start.'='.$end;

        if ($start && $end) {
            $returnQuery->whereBetween('sr.return_date', [$start, $end]);
        }
        $returnIds = $returnQuery->pluck('sr.id');
        //    return $test;

        if ($returnIds->isEmpty()) {
            return 0; // no returns found
        }
        $totalReturnedQty = DB::table('sale_order_return_details')
            ->whereIn('sale_order_return_id', $returnIds)
            ->sum('quantity');

        return $totalReturnedQty;
    }
    public static function getAddress($lat, $lng)
    {
        if (empty($lat) || empty($lng)) return '';
        
        return cache()->remember("new_address_{$lat}_{$lng}", now()->addDays(30), function () use ($lat, $lng) {
            $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lng}&accept-language=en";

            $options = [
                "http" => [
                    "header" => "User-Agent: Laziza SND/1.0\r\n"
                ]
            ];

            $context = stream_context_create($options);
            $response = @file_get_contents($url, false, $context);
            $data = json_decode($response, true);

            return $data['display_name'] ?? '';
        });
        // $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lng}&accept-language=en";

        // $options = [
        //     "http" => [
        //         "header" => "User-Agent: Laziza SND/1.0\r\n"
        //     ]
        // ];

        // $context = stream_context_create($options);
        // $response = @file_get_contents($url, false, $context);
        // $data = json_decode($response, true);

        // return $data['display_name'] ?? '';
    }

}
