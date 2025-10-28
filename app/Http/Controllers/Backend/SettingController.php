<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Config;
use DB;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function config(Request $request)
    {
        $config=Config::all();
        $config_arr=array();
        foreach ($config as $cf){
            $config_arr[$cf->config_key] = $cf->config_value;
        }
        return view('pages.config.add',['config'=>$config_arr]);
    }

    public function config_store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validatedData =  Validator::make($request->all(),[
                'tso_max_limit' => 'required',
            ]);
            if ($validatedData->fails()):
                return response()->json(['error' => $validatedData->errors()]);
            endif;
            $config=new Config();
            foreach ($request->all() as $key=>$value){
                $exists = $config->where('config_key', $key)->count();
                if ($exists == 0) {
                    $data=array(
                        'config_key' => $key,
                        'config_value'=>$value
                    );
                    $config->insert($data);
                }else{
                    $data=array('config_value'=>$value);
                    $config->where('config_key',$key)->update($data);
                }
            }
            DB::commit();
            return response()->json(['success' => 'Data Save Successfully']);
        } catch (Exception $th) {
            //throw $th;
            DB::rollback();
            return response()->json(['catchError' => $th->getMessage()]);
        }
    }
}
