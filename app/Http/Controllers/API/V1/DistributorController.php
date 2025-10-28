<?php

namespace App\Http\Controllers\API\V1;

// use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Distributor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\V1\BaseController;

class DistributorController extends BaseController
{
    public function getTsoWiseDistributor(Request $request)
    {
       // $distributors =  User::find(Auth::user()->id)->tso->distributor;

        $distributor    =   Distributor::status()->whereHas('UserDistributor',function($query){
            $query->where('user_id',Auth::user()->id)
            ->groupBy('user_id');
            })->get();
        return $this->sendResponse($distributor,'Distributor Retrive Successfully');
    }
}
