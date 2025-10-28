<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Department;
use App\Models\Designation;
use App\Models\City;
use App\Models\Distributor;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use App\Models\Product;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index(Request $request)
    // {
    //     $data = User::where('status',1)->where('user_type','!=',5)->orderBy('id', 'DESC')->paginate(100);
    //     if ($request->ajax()) :
    //         return  view('pages.User.indexAjax', compact('data'))
    //             ->with('i', ($request->input('page', 1) - 1) * 100);
    //     endif;
    //     return view('pages.User.index');
    // }

      public function index(Request $request)
    {
      
        $authUser = auth()->user();
    
    
       
     
        if ($authUser->user_type == 9) {
        
            $data = User::where('status', 1)->where('user_type','!=',5)
                ->orderBy('id', 'DESC')
                ->paginate(200);
        } else {
          
            $data = User::where('status', 1)
                ->where('user_type', '!=', 5)->where('user_type', '!=', 9)
                ->orderBy('id', 'DESC')
                ->paginate(200);
        }
    
      
        if ($request->ajax()) {
            return view('pages.User.indexAjax', compact('data'))
                ->with('i', ($request->input('page', 1) - 1) * 100);
        }
    
       
        return view('pages.User.index', compact('data'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $permission = Permission::get();
        $roles = Role::pluck('name', 'name')->all();


        $user_code = UserDetail::UniqueNo();

        $departments = Department::all();
        $designations = Designation::all();
        $distributors = Distributor::where('status', 1)->get();
        $users = User::all();
        $cities = City::whereIn('state_id',['2729','2728','2727','2726','2725','2724','2723'])->get();

        return view('pages.User.create', compact('roles' , 'user_code' ,'designations' , 'departments' , 'distributors' , 'cities' , 'users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        DB::beginTransaction();
        try {

            // $input = $request->all();



            $fileName ='';
            if ($request->file('image')) {
                $file = $request->file('image');
                $fileName = time() . '-'. $file->getClientOriginalName();
                $file->storeAs('profile', $fileName, 'public'); // 'uploads' is the directory to store files.
            }
            $input['image'] = $fileName;

            $input = $request->only('name', 'email', 'password','user_type');
            $input['username'] = $request->name;
            $input['password'] = $input['password'];
            $user = User::create($input);
            $user->assignRole($request->role);

            $user->distributors()->attach($request->distributor);

            $data = $request->except('_token','name', 'email', 'password','role' , 'image', 'user_type' ,'distributor');
            $data['user_code'] = UserDetail::UniqueNo();
            $data['user_id'] = $user->id;
            $user_data = UserDetail::create($data);


            DB::commit();
            return response()->json(['success' => 'User created successfully.']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
        // $user->assignRole($request->input('roles'));

        // return redirect()->route('users.index')
        //                 ->with('success','User created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $user_data = UserDetail::where('user_id' , $user->id)->first();
        $role = $user->roles[0]->id ?? '';
        // dd($user_data);

        $user_code = UserDetail::UniqueNo();


        $departments = Department::all();
        $designations = Designation::all();
        $distributors = Distributor::where('status', 1)->get();
        $users = User::all();
        $cities = City::whereIn('state_id',['2729','2728','2727','2726','2725','2724','2723'])->get();


        return view('pages.User.edit', compact('user' , 'user_data','role' , 'user_code' ,'designations' , 'departments' , 'distributors' , 'cities' , 'users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $user->syncRoles($request->role);
        $input = $request->only('name', 'email', 'password','user_type');
        // dd($input);

        $fileName ='';
        if ($request->file('image')) {
            $file = $request->file('image');
            $fileName = time() .'-'. $file->getClientOriginalName();
            $file->storeAs('profile', $fileName, 'public'); // 'uploads' is the directory to store files.
        }

        if ($input['password']!='') {

            $input['password'] =$input['password'];

        } else {
            $input = Arr::except($input, array('password'));
        }


        $user->update($input);
       // $permissions = Permission::whereIn('id',$input['permissions'])->pluck('name');


        // dd($permissions);
     //   $user->syncPermissions($permissions);
        $user->distributors()->sync($request->distributor);


        $user_data = UserDetail::where('user_id' , $user->id)->first();

        $data = $request->except('_token','name', 'email', 'permissions', 'password','role' ,'image', 'user_type' ,'distributor');
        $data['user_code'] = $user_data->user_code ?? UserDetail::UniqueNo();
        $data['user_id'] = $user->id;
        // $user_data = $user_data->update($data);
        UserDetail::updateOrCreate(
            ['user_id' => $user->id],  // Match on user_id
            $data                        // Update or insert these values
        );

        return response()->json(['success' => 'Updated successfully.']);
        // DB::table('model_has_roles')->where('model_id',$id)->delete();

        // $user->assignRole($request->input('roles'));

        // return redirect()->route('users.index')
        //                 ->with('success','User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $tso = User::where('id', $id)->update(['status'=> 0]);
        return response()->json(['success'=>'TSO Deleted Successfully']);

    }


    public function viewProfile($id)
    {
        // dd($id);
        $user_data = User::find($id);
        return view('pages.User.viewProfile' , compact('user_data'));
    }


    public function profileEdit(Request $request)
    {
        return view('pages.User.profileEdit');
    }


    public function upload_profile(Request $request){
        // dd($request->image);
        $user = User::find(Auth::user()->id);
        if ($request->file('image')) {
            if (Storage::disk('public')->exists('profile/'.$user->image)) {
                Storage::disk('public')->delete('profile/'.$user->image);
            }

            $file = $request->file('image');
            $fileName = time().'-'.$file->getClientOriginalName();
            $file->storeAs('profile', $fileName, 'public'); // 'uploads' is the directory to store files.
        }
        $user->image = $fileName;

        if ($user->save()) {
            return response()->json(['status' => true , 'message'=>'Image uploaded']);
        }
        else{
            return response()->json(['status' => false , 'message'=>'Something went wrong']);
        }

    }


    public  function topRank(Request $request)
    {
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        $tso = DB::table('sale_orders')
        ->join('users_distributors as b', 'b.distributor_id', '=', 'sale_orders.distributor_id')
        ->where('b.user_id', auth()->user()->id)
        ->join('tso','tso.id','sale_orders.tso_id')
        // ->join('sale_order_data', 'sale_order_data.so_id', '=', 'sale_orders.id')
        // ->select(DB::raw('sum(sale_order_data.total) as amount'), 'tso.id','tso.name as tso_name','tso.tso_code')
        ->select(DB::raw('sum(sale_orders.total_amount) as amount'), 'tso.id','tso.name as tso_name','tso.tso_code')
        ->groupBy('sale_orders.tso_id')
        ->whereBetween('sale_orders.dc_date', [$currentMonthStart, $currentMonthEnd])
        ->where('sale_orders.status',1)
        ->where('tso.status',1)
        ->where('sale_orders.excecution' , 1)
        ->orderBy('amount', 'desc') // Sort by amount in descending order
        ->limit(10) // Limit the results to 4 rows
        ->get();    


    //    $tso_check = DB::table('sale_orders')
    // ->join('users_distributors as b', 'b.distributor_id', '=', 'sale_orders.distributor_id')
    // ->where('b.user_id', auth()->user()->id)
    // ->join('tso', 'tso.id', '=', 'sale_orders.tso_id')
    // ->leftJoin(DB::raw('(
    //     SELECT so_id, SUM(amount) as total_return_amount 
    //     FROM sales_returns 
    //     WHERE status = 1 
    //     GROUP BY so_id
    // ) as sales_returns'), 'sales_returns.so_id', '=', 'sale_orders.id') 
    // ->select(
    //     DB::raw('SUM(sale_orders.total_amount) as total_sales_amount'), 
    //     DB::raw('COALESCE(SUM(sales_returns.total_return_amount), 0) as total_return_amount'), 
    //     DB::raw('SUM(sale_orders.total_amount) - COALESCE(SUM(sales_returns.total_return_amount), 0) as net_amount'), 
    //     'tso.id',
    //     'tso.name as tso_name',
    //     'tso.tso_code'
    // )
    // ->groupBy('sale_orders.tso_id', 'tso.id', 'tso.name', 'tso.tso_code') 
    // ->whereBetween('sale_orders.dc_date', [$currentMonthStart, $currentMonthEnd])
    // ->where('sale_orders.status', 1)
    // ->where('tso.status', 1)
    // ->where('sale_orders.excecution', 1)
    // ->orderBy('net_amount', 'desc') 
    // ->limit(10) 
    // ->get();


       $tso_check = DB::table('sale_orders')
    ->join('users_distributors as b', 'b.distributor_id', '=', 'sale_orders.distributor_id')
    ->where('b.user_id', auth()->user()->id)
    ->join('tso', 'tso.id', '=', 'sale_orders.tso_id')
    ->leftJoin(DB::raw('(
        SELECT so_id, SUM(amount) as total_return_amount 
        FROM sales_returns 
        WHERE status = 1 
        GROUP BY so_id
    ) as sales_returns'), 'sales_returns.so_id', '=', 'sale_orders.id') 
    ->select(
        DB::raw('SUM(sale_orders.total_pcs) as total_sales_amount'), 
        DB::raw('COALESCE(SUM(sales_returns.total_return_amount), 0) as total_return_amount'), 
        DB::raw('SUM(sale_orders.total_pcs) as net_amount'), 
        'tso.id',
        'tso.name as tso_name',
        'tso.tso_code'
    )
    ->groupBy('sale_orders.tso_id', 'tso.id', 'tso.name', 'tso.tso_code') 
    ->whereBetween('sale_orders.dc_date', [$currentMonthStart, $currentMonthEnd])
    ->where('sale_orders.status', 1)
    ->where('tso.status', 1)
    ->where('sale_orders.excecution', 1)
    ->orderBy('net_amount', 'desc') 
    ->limit(10) 
    ->get();

   

        $product = DB::table('sale_orders')
        ->join('users_distributors as b', 'b.distributor_id', '=', 'sale_orders.distributor_id')
        ->where('b.user_id', auth()->user()->id)
        ->join('sale_order_data', 'sale_order_data.so_id', '=', 'sale_orders.id')
        ->join('products','products.id','sale_order_data.product_id')
        ->select('sale_order_data.product_id', DB::raw('sum(sale_order_data.qty) as product_count'), 'products.id' , 'products.product_name')
        ->groupBy('sale_order_data.product_id')
        ->whereBetween('sale_orders.dc_date', [$currentMonthStart, $currentMonthEnd])
        ->where('sale_orders.status',1)
        ->where('products.status',1)
        ->where('sale_orders.excecution' , 1)
        ->orderBy('product_count', 'desc') // Sort by product_count in descending order
        ->limit(10) // Limit the results to the top 3 products
        ->get();

      
    $product_check = DB::table('sale_orders')
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
        
        'sale_order_data.product_id',
        'products.id',
        'products.product_name',
        DB::raw('SUM(sale_order_data.qty) as product_count'),
        DB::raw('SUM(COALESCE(return_data.return_qty, 0)) as return_count'),
        DB::raw('SUM(sale_order_data.qty) - SUM(COALESCE(return_data.return_qty, 0)) as net_qty')
    )
    ->whereBetween('sale_orders.dc_date', [$currentMonthStart, $currentMonthEnd])
    ->where('sale_orders.status', 1)
    ->where('tso.status', 1)
    ->where('sale_orders.excecution', 1)
    ->groupBy('sale_order_data.product_id', 'products.product_name')
	->orderBy(DB::raw('SUM(sale_order_data.qty) - SUM(COALESCE(return_data.return_qty, 0))'), 'DESC') // Order by net_qty
    ->limit(10)
    ->get();


    



   

        $distributer = DB::table('sale_orders')
        ->join('users_distributors as b', 'b.distributor_id', '=', 'sale_orders.distributor_id')
        ->where('b.user_id', auth()->user()->id)
        ->join('distributors','distributors.id','sale_orders.distributor_id')
        // ->join('sale_order_data', 'sale_order_data.so_id', '=', 'sale_orders.id')
        // ->select(DB::raw('sum(sale_order_data.total) as amount'),'distributors.id','distributors.distributor_name as distributor_name','distributors.distributor_code')
        ->select(DB::raw('sum(sale_orders.total_amount) as amount') ,'distributors.id','distributors.distributor_name as distributor_name','distributors.distributor_code')
        ->groupBy('sale_orders.distributor_id')
        ->whereBetween('sale_orders.dc_date', [$currentMonthStart, $currentMonthEnd])
        ->where('sale_orders.status',1)
        ->where('distributors.status',1)
        ->where('sale_orders.excecution' , 1)
        ->orderBy('amount', 'desc') // Sort by amount in descending order
        ->limit(10) // Limit the results to 4 rows
        ->get();


        $distributer_check = DB::table('sale_orders')
        ->join('users_distributors as b', 'b.distributor_id', '=', 'sale_orders.distributor_id')
        ->where('b.user_id', auth()->user()->id)
        ->join('distributors', 'distributors.id', '=', 'sale_orders.distributor_id')
        ->leftJoin(DB::raw('(
            SELECT so_id, SUM(amount) as total_return_amount 
            FROM sales_returns 
            WHERE status = 1 
            GROUP BY so_id
        ) as sales_returns'), 'sales_returns.so_id', '=', 'sale_orders.id') // Left join to get return amount
        ->select(
            DB::raw('SUM(sale_orders.total_amount) as total_sales_amount'), // Total sales amount
            DB::raw('COALESCE(SUM(sales_returns.total_return_amount), 0) as total_return_amount'), // Total return amount
            DB::raw('SUM(sale_orders.total_amount) - COALESCE(SUM(sales_returns.total_return_amount), 0) as net_amount'), // Net amount
            'distributors.id',
            'distributors.distributor_name as distributor_name',
            'distributors.distributor_code'
        )
        ->groupBy('sale_orders.distributor_id', 'distributors.id', 'distributors.distributor_name', 'distributors.distributor_code') // Grouping
        ->whereBetween('sale_orders.dc_date', [$currentMonthStart, $currentMonthEnd])
        ->where('sale_orders.status', 1)
        ->where('distributors.status', 1)
        ->where('sale_orders.excecution', 1)
        ->orderBy('net_amount', 'desc') // Sort by net amount in descending order
        ->limit(10) // Limit the results to 10 rows
        ->get();
    

    //   dd($distributer_check);
        $shop = DB::table('sale_orders')
        ->join('users_distributors as b', 'b.distributor_id', '=', 'sale_orders.distributor_id')
        ->where('b.user_id', auth()->user()->id)
        ->join('shops','shops.id','sale_orders.shop_id')
        // ->join('sale_order_data', 'sale_order_data.so_id', '=', 'sale_orders.id')
        // ->select(DB::raw('sum(sale_order_data.total) as amount'),'shops.company_name as shop_name','shops.shop_code')
        ->select(DB::raw('sum(sale_orders.total_amount) as amount') ,'shops.company_name as shop_name','shops.shop_code','shops.id')
        ->groupBy('sale_orders.shop_id')
        ->whereBetween('sale_orders.dc_date', [$currentMonthStart, $currentMonthEnd])
        ->where('sale_orders.status',1)
        ->where('shops.status',1)
        ->where('sale_orders.excecution' , 1)
        ->orderBy('amount', 'desc') // Sort by amount in descending order
        ->limit(10) // Limit the results to 4 rows
        ->get();


        $shop_check = DB::table('sale_orders')
    ->join('users_distributors as b', 'b.distributor_id', '=', 'sale_orders.distributor_id')
    ->where('b.user_id', auth()->user()->id)
    ->join('shops', 'shops.id', '=', 'sale_orders.shop_id')
    ->leftJoin(DB::raw('(
        SELECT so_id, SUM(amount) as total_return_amount 
        FROM sales_returns 
        WHERE status = 1 
        GROUP BY so_id
    ) as sales_returns'), 'sales_returns.so_id', '=', 'sale_orders.id') // Left join for return amounts
    ->select(
        DB::raw('SUM(sale_orders.total_amount) as total_sales_amount'), // Total sales amount
        DB::raw('COALESCE(SUM(sales_returns.total_return_amount), 0) as total_return_amount'), // Total return amount
        DB::raw('SUM(sale_orders.total_amount) - COALESCE(SUM(sales_returns.total_return_amount), 0) as net_amount'), // Net amount
        'shops.id',
        'shops.company_name as shop_name',
        'shops.shop_code'
    )
    ->groupBy('sale_orders.shop_id', 'shops.id', 'shops.company_name', 'shops.shop_code') // Group by shop columns
    ->whereBetween('sale_orders.dc_date', [$currentMonthStart, $currentMonthEnd])
    ->where('sale_orders.status', 1)
    ->where('shops.status', 1)
    ->where('sale_orders.excecution', 1)
    ->orderBy('net_amount', 'desc') // Sort by net amount in descending order
    ->limit(10) // Limit the results to 10 rows
    ->get();

  


        $topBalanceShop = DB::table('shops')
        ->join('users_distributors as b', 'b.distributor_id', '=', 'shops.distributor_id')
        ->where('b.user_id', auth()->user()->id)
        ->join('shops_outstandings','shops_outstandings.shop_id','shops.id')
        // ->select(DB::raw('sum(sale_order_data.total) as amount'),'shops.company_name as shop_name','shops.shop_code')
        ->select('shops.company_name as shop_name','shops.shop_code','shops.id' ,DB::raw('  (shops_outstandings.so_amount + shops_outstandings.sr_amount +
        case
            when shops.debit_credit = 1 then shops.balance_amount
            when shops.debit_credit = 2 then -shops.balance_amount
        end
         - shops_outstandings.rv_amount) as amount'))

        ->groupBy('shops.id')
        // ->whereBetween('sale_orders.dc_date', [$currentMonthStart, $currentMonthEnd])
        // ->where('sale_orders.status',1)
        ->where('shops.status',1)
        ->orderBy('amount', 'desc') // Sort by amount in descending order
        ->limit(10) // Limit the results to 4 rows
        ->get();


        $topBalanceShop_check = DB::table('shops')
        ->join('users_distributors as b', 'b.distributor_id', '=', 'shops.distributor_id')
        ->where('b.user_id', auth()->user()->id)
        ->join('sale_orders', 'sale_orders.shop_id', '=', 'shops.id') // Join with sale_orders to connect shops and sales_returns
        ->join('shops_outstandings', 'shops_outstandings.shop_id', '=', 'shops.id')
        ->leftJoin(DB::raw('(
            SELECT sale_orders.shop_id, SUM(sales_returns.amount) as total_return_amount
            FROM sales_returns
            JOIN sale_orders ON sales_returns.so_id = sale_orders.id
            WHERE sales_returns.status = 1
            GROUP BY sale_orders.shop_id
        ) as sales_returns'), 'sales_returns.shop_id', '=', 'shops.id') // Subquery to calculate return amounts
        ->select(
            'shops.company_name as shop_name',
            'shops.shop_code',
            'shops.id',
            DB::raw('(
                shops_outstandings.so_amount + shops_outstandings.sr_amount +
                CASE
                    WHEN shops.debit_credit = 1 THEN shops.balance_amount
                    WHEN shops.debit_credit = 2 THEN -shops.balance_amount
                END
                - shops_outstandings.rv_amount
            ) as total_outstanding_amount'), // Total outstanding amount logic
            DB::raw('COALESCE(sales_returns.total_return_amount, 0) as total_return_amount'), // Total return amount
            DB::raw('(
                shops_outstandings.so_amount - COALESCE(sales_returns.total_return_amount, 0)
            ) as net_amount') // Net amount calculation
        )
        ->groupBy(
            'shops.id', 'shops.company_name', 'shops.shop_code', 'shops_outstandings.so_amount', 
            'shops_outstandings.sr_amount', 'shops.balance_amount', 'shops.debit_credit', 
            'shops_outstandings.rv_amount', 'sales_returns.total_return_amount'
        )
        ->where('shops.status', 1)
        ->orderBy('net_amount', 'desc') // Sort by net amount in descending order
        ->limit(10) // Limit the results to 10 rows
        ->get();
    
    


        // dd($topBalanceShop_check);

        $subquery = DB::table('sale_orders')
        ->join('users_distributors as b', 'b.distributor_id', '=', 'sale_orders.distributor_id')
        ->where('b.user_id', auth()->user()->id)
        ->select('shop_id', DB::raw('MAX(dc_date) as latest_dc_date'))
        ->where('status', 1)
        ->where('sale_orders.excecution' , 1)
        ->groupBy('shop_id');
        $nonProductiveshop = DB::table('sale_orders as so')
        ->join('shops', 'shops.id', '=', 'so.shop_id')
        ->joinSub($subquery, 'latest_orders', function($join) {
            $join->on('so.shop_id', '=', 'latest_orders.shop_id')
                ->on('so.dc_date', '=', 'latest_orders.latest_dc_date')
                ;
        })
        ->select('shops.company_name as shop_name', 'shops.shop_code', 'shops.id', 'so.dc_date')
        ->where('so.status', 1)
        ->where('so.excecution' , 1)
        ->where('shops.status', 1)
        ->where('so.dc_date' , '<' , date('Y-m-d', strtotime(date('Y-m-d')." -1 month")))
        ->orderBy('so.dc_date')
        ->limit(10)
        ->groupBy('so.shop_id')
        ->get();

     

        $data = [
            'tso'=>$tso,
            'tso_check'=>$tso_check,
            'product'=>$product,
            'product_check'=>$product_check,
            'distributer'=>$distributer,
            'distributer_check'=>$distributer_check,
            'shop'=>$shop,
            'shop_check'=>$shop_check,
            'topBalanceShop'=>$topBalanceShop,
            'topBalanceShop_check'=>$topBalanceShop_check,
            'nonProductiveshop'=>$nonProductiveshop,
        ];
        return $data;
    }

}
