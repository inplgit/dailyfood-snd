<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Channel;

class ChannelController extends Controller
{
   
     public function channels()
    {
        $channels = Channel::where('status', 1)->get();

        return response()->json([
            'success' => true,
            'data' => $channels
        ], 200);
    }
}
