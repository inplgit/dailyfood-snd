<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDistributorRequest;
use App\Http\Requests\UpdateDistributorRequest;
use App\Helpers\MasterFormsHelper;
use App\Models\Distributor;
use App\Models\PriceType;
use App\Models\TSO;
use App\Models\Zone;
use App\Models\City;
use App\Models\User;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;


class DistributorController extends Controller
{

    protected $page;
    public $master;

    public function __construct()
    {
        $this->page = 'pages.Distributor.Distributor.';
        $this->master = new MasterFormsHelper();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
  public function index(Request $request, Distributor $distributor)
    {


        if ($request->ajax()) :
            return  view($this->page . 'DistributorListAjax');
        endif;
        return  view($this->page . 'DistributorList');
    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Distributor $distributor)
    {
        $unique_no = $distributor->UniqueNo();
        $zones = Zone::status()->latest()->get();
        $priceTypes = PriceType::status()->latest()->get();

        return  view($this->page . 'AddDistributor',compact('priceTypes','zones','unique_no'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDistributorRequest $request)
    {

        $main_distributor =$request->parent_distributor;
        $distributor_code=0;
        $input = $request->all();



        $input = $request->except('pricing_type_id');


        if ($main_distributor!=0):
            $distributor_code =  Distributor::where('id',$main_distributor)->value('distributor_sub_code');
            // $max_code =  Distributor::where('parent_code',$distributor_code)->max('distributor_sub_code');

            $max_code =  Distributor::where('parent_code',$distributor_code)
            ->orderByRaw("CAST(SUBSTRING_INDEX(distributor_sub_code, '-', -1) AS UNSIGNED) DESC")
            ->select('distributor_sub_code')
            ->first();

            $max_code = isset($max_code) ? $max_code->distributor_sub_code : null;

            if ($max_code==null):
               $code =  $distributor_code.'-1';

            else:

                $code = $distributor_code.'-'.((int) last(explode('-', $max_code)) +1);
                // $code = $distributor_code.'-'.(substr($max_code, -1)+1);

            endif;

            $data = explode('-',$code);

            foreach ($data as $key =>$row):
            $input['level'.++$key] = $row;
            endforeach;

         else:
           $level = Distributor::where('parent_code',0)->max('level1');
           $input['level1']=$level+1;
           $code = $level+1;


         endif;


        $input['parent_code'] =  $distributor_code;
        $input['distributor_sub_code'] = $code;
        $input['distributor_code'] = Distributor::UniqueNo();
        $input['city'] = City::findOrFail($request->city_id)->name;
        $distributor = Distributor::create($input);
        $distributor->price_types()->sync($request->pricing_type_id);



        // 3. Get the user who created the distributor (for example, authenticated user)
        $user = auth()->user(); // Assuming the logged-in user should be synced
        // 4. Sync the distributor with the user
        $user->distributors()->syncWithoutDetaching([$distributor->id]);
        // 5. Get all admin users (assuming `type = 1` means admin)
        $adminUsers = User::whereIn('user_type',[1,9])->get();
        // 6. Sync the distributor with all admin users
        foreach ($adminUsers as $admin) {
            $admin->distributors()->syncWithoutDetaching([$distributor->id]);
        }



        return response()->json(['success' => 'Distributor created successfully.']);
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
    public function edit(Distributor $distributor)
    {
        $zones = Zone::status()->latest()->get();
        $priceTypes = PriceType::status()->latest()->get();
        return view($this->page . 'EditDistributor', compact('distributor','zones','priceTypes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDistributorRequest $request, Distributor $distributor)
    {


        $request['city'] = City::findOrFail($request->city_id)->name;
        $distributor->update($request->except('pricing_type_id'));


        $distributor->price_types()->sync($request->pricing_type_id);


        // 3. Get the user who created the distributor (for example, authenticated user)
        $user = auth()->user(); // Assuming the logged-in user should be synced
        // 4. Sync the distributor with the user



        $user->distributors()->syncWithoutDetaching([$distributor->id]);
        // 5. Get all admin users (assuming `type = 1` means admin)

        $user->distributors()->syncWithoutDetaching([$distributor->id]);
        // 6. Sync the distributor with all admin users


$adminUsers = User::whereIn('user_type',[1,9])->get();
        foreach ($adminUsers as $admin) {
            $admin->distributors()->syncWithoutDetaching([$distributor->id]);
        }


        return response()->json(['success' => 'Updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


       public function destroy1($id)
    
    {

    
        $distributor = Distributor::where('id', $id)->update(['status' => 0]);
        return redirect()->back()->with('success', 'Distributor Deleted Successfully');
    }
    public function destroy($id)
    {

dd($id);
        $distributor = Distributor::where('id', $id)->update(['status' => 0]);
        return response()->json(['success' => 'TSO Deleted Successfully']);
    }

    public function ImportDistributor(Request $request)
    {
        // $distributor = Distributor::where('id', $id)->update(['status' => 0]);
        // return response()->json(['success' => 'TSO Deleted Successfully']);
        return view($this->page . 'ImportDistributor');
    }

    public function import_distributors_store(Request $request)
    {

      
        DB::beginTransaction();
        try {
            // dd($request->file('file'));
            $file = Excel::toArray([], $request->file('file'));
            $file = $file[0];
            $shopExistis = [];
            $distributorExistis = [];
            $distributorNotExist = [];
            $zoneNotExist = [];
            $tsoNotExist = [];
            $distributor_code=0;

            // dd($file , $file[0][1] , trim("SHOP NAME"));
            if($file[0][0] == trim("Parent Distributor Code") && $file[0][1] == trim("Parent Distributor (Company Name)") && $file[0][2] == trim("Distributor Custom Code") && $file[0][3] == trim("Distributor (Company Name)") && $file[0][4] == trim("Contact Person") && $file[0][5] == trim("Email") && $file[0][6] == trim("Phone") && $file[0][7] == trim("Address")
            && $file[0][8] == trim("City") && $file[0][9] == trim("State") && $file[0][10] == trim("Zip Code") && $file[0][11] == trim("Minimum Discount") && $file[0][12] == trim("Maximum Discount") && $file[0][13] == trim("Location Title") && $file[0][14] == trim("Latitude")  && $file[0][15] == trim("Longitude") && $file[0][16] == trim("Radius")){
                // if($file[0][1] == trim("SHOP NAME") && $file[0][2] == trim("City") && $file[0][3] == trim("State") && $file[0][6] == trim("Route") && $file[0][7] == trim("Day")){
                foreach ($file as $key => $value) {


                    if($key == 0) continue ;
                    // $tso_id = $request->tso_id;
                    // $tso_id = TSO::where('tso_code',$value[2])->value('id');

                    $zone_id = Zone::where('zone_name' , $value[17])->value('id');
                    if(!$zone_id) array_push($zoneNotExist,$value[17]) ;
                    if(!$zone_id) continue ;

                    $distributor_name = Distributor::where('distributor_name', $value[3])->value('distributor_name');
                    if($distributor_name) array_push($distributorExistis,$value[3]) ;
                    if($distributor_name) continue ;


                    // dd($zone_id);
                    if ($value[0]) {
                        $distributor_code = Distributor::where('distributor_code',$value[0])->value('distributor_sub_code');
                        if(!$distributor_code) array_push($distributorNotExist,$value[0]);
                        if(!$distributor_code) continue ;
                        $max_code =  Distributor::where('parent_code',$distributor_code)
                        ->orderByRaw("CAST(SUBSTRING_INDEX(distributor_sub_code, '-', -1) AS UNSIGNED) DESC")
                        ->select('distributor_sub_code')
                        ->first();

                        $max_code = isset($max_code) ? $max_code->distributor_sub_code : null;
                        // dd($max_code , $distributor_code , substr($max_code, -1) ,  (int) last(explode('-', $max_code)));
                        if ($max_code==null):
                        $code =  $distributor_code.'-1';
                        else:

                            $code = $distributor_code.'-'.((int) last(explode('-', $max_code)) +1);
                            // $code = $distributor_code.'-'.(substr($max_code, -1)+1);

                        endif;
                        // dd($code);
                        $data = explode('-',$code);

                        foreach ($data as $key =>$row):
                        $input['level'.++$key] = $row;
                        endforeach;

                    }
                    else{
                        $level = Distributor::where('parent_code',0)->max('level1');
                        $input['level1']=$level+1;
                        $code = $level+1;
                    }
                    // dd('asdsad');
                    $city_id =  City::where('name',$value[8])->value('id');

                    if (!$city_id) {
                        $city = City::create([
                            'name' => $value[8],
                            'state_id' => 2729,
                            'status' => 1,
                        ]);
                        $city_id = $city->id;
                    }
                    // dd($city_id);

                    $input['parent_code'] =  $distributor_code;
                    $input['distributor_sub_code'] = $code;
                    $input['distributor_code'] = Distributor::UniqueNo();
                    $input['distributor_name'] = $value[3];
                    $input['custom_code'] = $value[2];
                    $input['contact_person'] = $value[4];
                    $input['email'] = $value[5];
                    $input['phone'] = $value[6];
                    $input['address'] = $value[7];
                    $input['city_id'] = $city_id;
                    $input['city'] = $value[8];
                    $input['zip'] = $value[10];
                    $input['zone_id'] = $zone_id;
                    $input['min_discount'] = $value[11];
                    $input['max_discount'] = $value[12];
                    $input['location_title'] = $value[13];
                    $input['location_latitude'] = $value[14];
                    $input['location_longitude'] = $value[15];
                    $input['location_radius'] = $value[16];
                    $input['username'] = Auth::user()->name;
                    $distributor = Distributor::create($input);

                    // 3. Get the user who created the store (for example, authenticated user)
                    $user = auth()->user(); // Assuming the logged-in user should be synced
                    // 4. Sync the store with the user
                    $user->distributors()->syncWithoutDetaching([$distributor->id]);
                }

                DB::commit();
                if($distributorExistis){Session::flash('distributorExistis',$distributorExistis);}
                if($distributorNotExist){Session::flash('distributorNotExist',$distributorNotExist);}
                if($zoneNotExist){Session::flash('zoneNotExist',$zoneNotExist);}

                return redirect()->back();
            }else{
                return redirect()->back()->with('catchError',"Format Not Match");
            }
        } catch (Exception $th) {
            //throw $th;
            DB::rollback();
            return response()->json(['catchError' => $th->getMessage()]);
        }
    }


    public function getDistributorByCity(Request $request)
    {
        // dd($request->all());
        $city_ids = $request->city_id;
        // dd($city_ids );

        $distributor = $this->master->get_distributor_by_city($city_ids);
        // dd($distributor->toArray());
        return  response()->json(['distributor'=>$distributor]);
    }

}
