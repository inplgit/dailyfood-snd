<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Attendence;
use App\Models\Product;
use App\Models\TSO;
use App\Models\TSOTarget;
use App\Models\UserAttendence;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use DateTime;
use DateInterval;
use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class TSOTargetController extends Controller
{
    public function __construct()
    {
        $this->page = 'pages.TSO.TSOTarget.';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index(Request $request)
{
      $tso = TSO::Active()->get();  
    
    if ($request->ajax()):
        $tso_id = $request['tso_id'];
        $month = $request['month'];
        $shop_type_id = $request['shop_type_id'];
        $city_id = $request['city'];
        $distributor_id =$request['distributor_id'];

       

        $products = Product::where('status', 1)->with('product_flavour')->get();


        // **Filter Conditions**
        $query = TSOTarget::query();

        if ($tso_id) {
            $query->where('tso_id', $tso_id);
        }
        if ($city_id) {
            $query->where('city_id', $city_id);
        }
        if ($distributor_id) {
            $query->where('distributor_id', $distributor_id);
        }

        $query->whereYear('month', substr($month, 0, 4))
              ->whereMonth('month', substr($month, 5, 2));

        // **Total Amount Target**
        $total_amount_target = $query->whereNull('product_id')->value('amount');

        // **Shop Type Target**
        $shop_type = clone $query;
        $shop_type = $shop_type->where('type', 3)->get();

  

        return view($this->page . 'TableTSOTarget', compact(
            'shop_type_id', 'tso_id', 'month', 'products', 'total_amount_target', 'shop_type','month','tso_id','distributor_id','city_id'
        ));
    endif;

    return view($this->page . 'AddTSOTarget', compact('tso'));
}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     
 public function store(Request $request)
{
    Log::info('Store method called', ['request' => $request->all()]);

    $validatedData = $request->validate([
        'tso_id' => 'nullable|integer',
        'city' => 'required|integer',
        'distributor_id' => 'nullable|integer',
        'month' => 'required|date',
        'total_amount_target' => 'nullable|numeric',
        'product_id' => 'array',
        'flavour_id' => 'array',
        'target_type' => 'array',
        'amount' => 'array',
        'quantity' => 'array',
    ]);

    $month = date("m", strtotime($request->month));
    $modifiedMonth = (new DateTime($request->month))->add(new DateInterval('P1D'))->format('Y-m-d');

    try {
        DB::beginTransaction();

        if ($request->filled('city') && !$request->filled('distributor_id') && !$request->filled('tso_id')) {
            $distributors = Distributor::where('city_id', $request->city)->get();
            foreach ($distributors as $distributor) {
                $tsos = TSO::Active()->where('distributor_id', $distributor->id)->get();
                foreach ($tsos as $tso) {
                    $this->saveTarget($request, $modifiedMonth, $request->city, $distributor->id, $tso->id, $month);
                }
            }
        } elseif ($request->filled('city') && $request->filled('distributor_id') && !$request->filled('tso_id')) {
            $tsos = TSO::Active()->where('distributor_id', $request->distributor_id)->get();
            foreach ($tsos as $tso) {
                $this->saveTarget($request, $modifiedMonth, $request->city, $request->distributor_id, $tso->id, $month);
            }
        } elseif ($request->filled('city') && $request->filled('distributor_id') && $request->filled('tso_id')) {
            $this->saveTarget($request, $modifiedMonth, $request->city, $request->distributor_id, $request->tso_id, $month);
        }

        DB::commit();
        return response()->json(['success' => 'TSO Target Assigned Successfully']);
    } catch (\Exception $e) {
        DB::rollback();
        Log::error('Error in store method', ['error' => $e->getMessage()]);
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
private function saveTarget($request, $modifiedMonth, $cityId, $distributorId, $tsoId, $month)
{
    // First: Delete all previous targets for this city + distributor + tso + month
    TSOTarget::where('city_id', $cityId)
        ->where('distributor_id', $distributorId)
        ->where('tso_id', $tsoId)
        ->whereMonth('month', $month)
        ->delete();

    // Then: Insert total target if given
    if ($request->filled('total_amount_target')) {
        TSOTarget::create([
            'city_id' => $cityId,
            'distributor_id' => $distributorId,
            'tso_id' => $tsoId,
            'month' => $modifiedMonth,
            'type' => 2,
            'amount' => $request->total_amount_target,
        ]);
    }

    // Now: Insert product-wise/flavor-wise targets
    if ($request->has('product_id')) {
        foreach ($request->product_id as $key => $productId) {
            if (!$productId) {
                continue; // Skip invalid product
            }

            $flavorId = $request->flavour_id[$key] ?? null;

            TSOTarget::create([
                'city_id' => $cityId,
                'distributor_id' => $distributorId,
                'tso_id' => $tsoId,
                'product_id' => $productId,
                'flavour_id' => $flavorId,
                'month' => $modifiedMonth,
                'type' => $request->target_type[$key] == 1 ? 1 : 2,
                'amount' => $request->target_type[$key] == 2 ? $request->amount[$key] : null,
                'qty' => $request->target_type[$key] == 1 ? $request->quantity[$key] : null,
            ]);
        }
    }
}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function tso_summary_report()
    {
        return view('pages.Reports.TsoSummary');
    }

    public function tso_summary_report_data(Request $request){



    }
    public function addAttendence(Request $request)
    {
        $attendence = new UserAttendence;
        $attendence->user_id =$request->id;
        $attendence->date= Carbon::now();
        $attendence->attendence_status=1;
        $attendence->status=1;
        if($attendence->save())
        {
            return 1;
        }else{
            return 0;
        }

    }
    public function attendenceList(Request $request)
    {
       if($request->Ajax())
       {

            $from =$request->from;
            $to =$request->to;
            $startDate = date('Y-m-d 00:00:00', strtotime($from)); // "2023-08-04 00:00:00"
            $endDate = date('Y-m-d 23:59:59', strtotime($to));
                if(Auth::user()->user_type == 1){
                $Attendence =  UserAttendence::whereBetween('date',[$startDate,$endDate])->get();

                }else{
                $Attendence =  UserAttendence::where('user_id',Auth::user()->id)->whereBetween('date',[$from,$to])->get();
                }
                return view('pages.Attendence.indexAjax',compact('Attendence'));

         }
        return view('pages.Attendence.index');
    }
}
