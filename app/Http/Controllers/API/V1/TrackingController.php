<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\V1\BaseController as BaseController;
use App\Models\TsoTrackingHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class TrackingController extends BaseController
{
    /**
     * Store tracking data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $locations = $request->json()->all();
        // $validator = Validator::make($request->all(), [
        //     'locations' => 'required|array',
        //     'locations.*.latitude' => 'required|numeric',
        //     'locations.*.longitude' => 'required|numeric',
        //     'locations.*.sync_date_time' => 'required|date_format:Y-m-d H:i:s',
        //     'locations.*.local_id' => 'required',
        // ]);


        $user = Auth::user();
        if (!$user->tso) {
            return $this->sendError('Unauthorized.', ['error' => 'User is not a TSO.'], 403);
        }

        $tso = $user->tso;
        $distributor_id = $tso->distributor_id;

        $trackingData = [];
        $response = [
            'success' => [],
            'failed'  => []
        ];
        foreach ($locations as $location) {
            try {
                $validator = Validator::make($location, [
                    'latitude' => 'required|numeric',
                    'longitude' => 'required|numeric',
                    'sync_date_time' => 'required|date_format:Y-m-d H:i:s',
                    'local_id' => 'required',
                ]);

                if ($validator->fails()) {
                    $response['failed'][] = [
                        'local_id' => $location['local_id'] ?? null,
                        'errors'   => $validator->errors()->all()
                    ];
                    continue;
                }

                
                $location['local_id'] = (string) ($location['local_id'] ?? '');

                if (!empty($location['local_id'])) {
                    $existing = TsoTrackingHistory::where('local_id', $location['local_id'])->first();
                    if ($existing) {
                        $response['success'][] = [
                            'local_id'      => $location['local_id'],
                            'tracking_id' => $existing->id,
                            'message'       => 'Tracking with this local_id already exists'
                        ];
                        continue; // skip insert
                    }
                }

                $trackingData[] = [
                    'user_id' => $user->id,
                    'tso_id' => $tso->id,
                    'distributor_id' => $distributor_id,
                    'latitude' => $location['latitude'],
                    'longitude' => $location['longitude'],
                    'sync_date_time' => $location['sync_date_time'],
                    'local_id' => $location['local_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            } catch (\Exception $e) {
                continue;
            }
        }

        try {
            TsoTrackingHistory::insert($trackingData);
            return response()->json($response, 201);
            // return $this->sendResponse($response, 'Tracking data stored successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Server Error.', ['error' => $e->getMessage()], 500);
        }
    }
}
