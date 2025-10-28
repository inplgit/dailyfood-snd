<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Attendence;
use App\Models\TSO;
use App\Models\User;
use App\Helpers\MasterFormsHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendenceController extends BaseController
{
    public function in(Request $request)
    {
        date_default_timezone_set("Asia/Karachi");

        $request->validate([
        //    'in' => 'required|date_format:Y-m-d H:i:s',
           // 'route_id' => 'required',
             'route_id' => 'nullable|numeric',
            'latitude' => 'required',
            'longitude' => 'required',
            // 'user_id' => 'required|numeric',
        ]);
        $request['user_id'] = Auth::id();
        $request['latitude_in'] = $request->latitude;
        $request['longitude_in'] = $request->longitude;
        $request['date'] = date('Y-m-d');

        $tso_id = Auth::user()->tso->id;
        $status = $this->findUsersNearby($request->latitude , $request->longitude , $tso_id);

        if (!$status) {
            return response()->json([
                'success' => false,
                'message' => 'You are not in location'
            ]);
        }


        $check_record = Attendence::where('date',date('Y-m-d'))->where('tso_id',$tso_id)->where('distributor_id',$request['distributor_id']);
        if ($check_record->count() > 0 ):
            return $this->sendResponse([$check_record->first()], 'Successfully CheckedIn.');
        endif;

        $distributor_id = $request->distributor_id;
        //$route_id = $request->route_id;
  $request['route_id'] = $request->route_id ?? 0;
        $request['tso_id'] = $tso_id;
        $request['in'] = date('Y-m-d').' '.date("H:i:s");
        $in = Attendence::create($request->only(['in', 'user_id', 'latitude_in', 'longitude_in','distributor_id','route_id','tso_id','date']));
        MasterFormsHelper::users_location_submit($in,$request->latitude,$request->longitude,'attendences','Check In');
        // dd($in);
        return $this->sendResponse([$in], 'Successfully CheckedIn.');
    }

    public function out(Request $request)
    {
        date_default_timezone_set("Asia/Karachi");
        $request->validate([
            'id' => 'required|numeric',
            'latitude' => 'required',
            'longitude' => 'required',
            // 'user_id' => 'required|numeric',
     //       'out' => 'required|date_format:Y-m-d H:i:s',
        ]);
        $tso_id = Auth::user()->tso->id;
        $status = $this->findUsersNearby($request->latitude , $request->longitude , $tso_id);

        if (!$status) {
            return response()->json([
                'success' => false,
                'message' => 'You are not in location'
            ]);
        }

        $request['user_id'] = Auth::id();
        $request['latitude_out'] = $request->latitude;
        $request['longitude_out'] = $request->longitude;
        $request['out'] = date('Y-m-d').' '.date("H:i:s");
        $out = Attendence::find($request->id)->update($request->only('out', 'latitude_out', 'longitude_out'));
        $att = new Attendence();
        $att = $att->find($request->id);
        MasterFormsHelper::users_location_submit($att,$request->latitude,$request->longitude,'attendences', 'Check Out');

        return $this->sendResponse([], 'Successfully CheckedOut.');
    }

    public function getAttendenceList(Request $request)
    {
        return $this->sendResponse(Auth::user()->attendence()->latest()->paginate($request->limit??5), "Retrive Attendence List.",200);
    }

    // public function findUsersNearby($latitude  , $longitude , $tso_id)
    // {


    //     // Construct URL for Geocoding API
    //     // $url = "https://maps.googleapis.com/maps/api/geocode/json?key=" . env('MAP_KEY') . "&latlng=" . $latitude . "," . $longitude . "&sensor=false";

    //     // // Make a request to the Geocoding API
    //     // $geocode = file_get_contents($url);
    //     // $json = json_decode($geocode);

    //     // // Extract formatted address
    //     // $address = isset($json->results[0]) ? $json->results[0]->formatted_address : '';

        
    //     $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$latitude}&lon={$longitude}&accept-language=en";
    
    //     $options = [
    //         'http' => [
    //             'header' => "User-Agent: dashi-snd 1.0"
    //         ]
    //     ];
        
    //     $context = stream_context_create($options);
    //     $response = file_get_contents($url, false, $context);
    //     $json = json_decode($response, true);
    //     $address = isset($json['display_name']) ? $json['display_name'] : '';

    //     $tso = TSO::find($tso_id);
    //     if ($tso->latitude && $tso->longitude && $tso->radius) {

    //         $lat2 = $tso->latitude;
    //         $lon2 = $tso->longitude;
    //         $lat1 = $latitude;
    //         $lon1 = $longitude;
    //         $earthRadius = 6371; // Earth's radius in kilometers
    //         $dLat = deg2rad($lat2 - $lat1);
    //         $dLon = deg2rad($lon2 - $lon1);
    //         $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
    //         $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    //         $distance = $earthRadius * $c;

    //         // dd($distance , $tso->radius , $tso->radius >= $distance);

    //         if ($tso->radius >= $distance) {
    //             return true;
    //         }
    //         else{
    //             return false;
    //         }

    //     }
    //     else{
    //         return true;
    //     }

    // }


     public function findUsersNearby($latitude, $longitude, $tso_id)
{
    
    $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$latitude}&lon={$longitude}&accept-language=en";
    $options = [
        'http' => [
            'header' => "User-Agent: popular-snd 1.0"
        ]
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    $json = json_decode($response, true);
    $address = $json['display_name'] ?? '';

    // 🔹 Get TSO with all saved locations
    $tso = TSO::with('locations')->find($tso_id);

    if (!$tso) {
        return false; // TSO not found
    }

    // Agar koi location hi nahi set hai → sab allowed
    if ($tso->locations->count() == 0) {
        return true;
    }

    $earthRadius = 6371; // Earth's radius in kilometers

    foreach ($tso->locations as $location) {
        if ($location->latitude && $location->longitude && $location->radius) {
            $lat2 = $location->latitude;
            $lon2 = $location->longitude;
            $lat1 = $latitude;
            $lon1 = $longitude;

            $dLat = deg2rad($lat2 - $lat1);
            $dLon = deg2rad($lon2 - $lon1);

            $a = sin($dLat / 2) * sin($dLat / 2) +
                 cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
                 sin($dLon / 2) * sin($dLon / 2);

            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
            $distance = $earthRadius * $c;

            // ✅ Agar user is location ke radius ke andar hai → allow
            if ($distance <= $location->radius) {
                return true;
            }
        }
    }

    // ✅ Agar koi bhi location match nahi hui
    return false;
}

}
